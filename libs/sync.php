<?php
    function Sync( $revision, $username, $comment ) {
        exec( "wget -O - http://zeus.blogcube.net/sync/", $output, $ret );
        $data = implode( "\n", $output );
        preg_match( "/revision (?<rev>\w+)./", $data, $match );
        $revision = $match[ 'rev' ];
        logSync( $username, $comment, $revision, "sync", $data );

        return $data;
    }

    function StaticSync( $revision, $username, $comment ) {
        // get current rev. this will be the rev of the new synced version
        $revstring = exec( "svn info /var/www/zino.gr/beta/phoenix/|grep Revision" );
        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
        $revision = $match[ 'rev' ];

        exec( "diff /var/www/zino.gr/static/css/global-bete.css /var/www/zino.gr/static/css/global.css", $output, $ret );
        exec( "diff /var/www/zino.gr/static/css/global-beta.js /var/www/zino.gr/static/css/global.js", $output, $ret );

        exec( "cat /var/www/zino.gr/static/css/global-beta.css > /var/www/zino.gr/static/css/global.css" );
        exec( "cat /var/www/zino.gr/static/js/global-beta.js > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        logSync( $username, $comment, $match[ 'rev' ], "csssync", $diff );

        return $data;
    }
?>
