<?php
    function Sync_Core( $revision, $username, $comment ) {
        $revision = ( int )$revision;
        exec( "wget -O - http://zeus.blogcube.net/sync/beta.php?revision=" . $revision, $output, $ret );
        $data = implode( "\n", $output );

        $latestsync = Log_GetLatestByType( 'sync' );
        $previousrevision = $latestsync[ 'sync_rev' ];
        $diff = SVN_Diff( $previousrevision, $revision );
        $data .= "\nDiff between revisions $previousrevision and $revision:\n\n" . $diff;
        
        Log_Create( $username, $comment, $revision, "sync", $data );

        return $data;
    }
    
    function Sync_Static( $revision, $username, $comment ) {
        $revision = SVN_GetCurrentRevision();

        exec( "diff /var/www/zino.gr/static/css/global.css /var/www/zino.gr/static/css/global-beta.css", $output, $ret );
        exec( "diff /var/www/zino.gr/static/css/global.js /var/www/zino.gr/static/css/global-beta.js", $output, $ret );

        exec( SVN_ROOT . "etc/generate-static.php production " . SVN_ROOT . "css > /var/www/zino.gr/static/css/global.css" );
        exec( SVN_ROOT . "etc/generate-static.php production " . SVN_ROOT . "js|/srv/svn/jsmin > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";
        Log_Create( $username, $comment, $match[ 'rev' ], "csssync", $data );

        return $data;
    }
?>
