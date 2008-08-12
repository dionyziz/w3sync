<?php
    function Sync( $revision, $username, $comment ) {
        $revision = ( int )$revision;

        $core = Sync_Core( $revision, $username, $comment );
        $static = Sync_Static( $revision, $username, $comment );

        $data = "Syncing core...\n" . $core . "Syncing static...\n" . $static;

        $syncid = Log_Create( $username, $comment, $revision, $data );

        return $syncid;
    }

    function Sync_Core( $revision, $username, $comment ) {
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

        return $data;
    }
    
    function Sync_Static( $revision, $username, $comment ) {
        exec( "sudo -u syncer svn up /var/www/zino.gr/static/css --revision " . $revision, $output, $ret );
        exec( "sudo -u syncer svn up /var/www/zino.gr/static/js --revision " . $revision, $output, $ret );
        exec( SVN_ROOT . "etc/generate-static.php production /var/www/zino.gr/static/css > /var/www/zino.gr/static/css/global.css" );
        exec( SVN_ROOT . "etc/generate-static.php production /var/www/zino.gr/static/js|/srv/svn/jsmin > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";

        return $data;
    }
?>
