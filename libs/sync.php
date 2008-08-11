<?php
    function Sync( $revision, $username, $comment ) {
        exec( "wget -O - http://zeus.blogcube.net/sync/", $output, $ret );
        $data = implode( "\n", $output );
        preg_match( "/revision (?<rev>\w+)./", $data, $match );
        $revision = $match[ 'rev' ];
        logSync( $username, $comment, $revision, "sync", $data );

        return $data;
    }
    
    function getCurrentRevision() {
        // get current rev. this will be the rev of the new synced version
        $revstring = exec( "svn info /var/www/zino.gr/beta/phoenix/|grep Revision" );
        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
        $revision = $match[ 'rev' ];

        return $revision;
    }

    function StaticSync( $revision, $username, $comment ) {
        $revision = getCurrentRevision();

        exec( "diff /var/www/zino.gr/static/css/global.css /var/www/zino.gr/static/css/global-beta.css", $output, $ret );
        exec( "diff /var/www/zino.gr/static/css/global.js /var/www/zino.gr/static/css/global-beta.js", $output, $ret );

        exec( "/var/www/zino.gr/beta/phoenix/etc/generate-static.php production /var/www/zino.gr/beta/phoenix/css > /var/www/zino.gr/static/css/global.css" );
        exec( "/var/www/zino.gr/beta/phoenix/etc/generate-static.php production /var/www/zino.gr/beta/phoenix/js|/srv/svn/jsmin > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";
        logSync( $username, $comment, $match[ 'rev' ], "csssync", $data );

        return $data;
    }


?>
