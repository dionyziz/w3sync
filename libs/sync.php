<?php
    function Sync_Core( $revision, $username, $comment ) {
        $revision = ( int )$revision;
        exec( "wget -O - http://zeus.blogcube.net/sync/beta.php?revision=" . $revision, $output, $ret );
        $data = implode( "\n", $output );

        $latestsync = Log_GetLatestByType( 'sync' );
        $previousrevision = $latestsync[ 'sync_rev' ];
        $diff = SVN_Diff( $previousrevision, $revision );
        if ( empty( $diff ) ) {
            $diff = "(none)";
        }
        else {
            $diff = "\n\n$diff";
        }
        $data .= "\nDiff between revisions $previousrevision and $revision: $diff";
        
        $syncid = Log_Create( $username, $comment, $revision, "sync", $data );

        return $syncid;
    }
    
    function Sync_Static( $revision, $username, $comment ) {
        exec( "sudo -u syncer svn up /var/www/zino.gr/static/css --revision " . $revision, $output, $ret );
        exec( "sudo -u syncer svn up /var/www/zino.gr/static/js --revision " . $revision, $output, $ret );
        exec( SVN_ROOT . "etc/generate-static.php production /var/www/zino.gr/static/css > /var/www/zino.gr/static/css/global.css" );
        exec( SVN_ROOT . "etc/generate-static.php production /var/www/zino.gr/static/js|/srv/svn/jsmin > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";
        $syncid = Log_Create( $username, $comment, $match[ 'rev' ], "csssync", $data );

        return $syncid;
    }
?>
