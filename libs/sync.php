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

        exec( "diff /var/www/zino.gr/static/css/global.css /var/www/zino.gr/static/css/global-beta.css", $output, $ret );
        exec( "diff /var/www/zino.gr/static/css/global.js /var/www/zino.gr/static/css/global-beta.js", $output, $ret );

        exec( "cat /var/www/zino.gr/static/css/global-beta.css > /var/www/zino.gr/static/css/global.css" );
        exec( "cat /var/www/zino.gr/static/js/global-beta.js > /var/www/zino.gr/static/js/global.js" );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";
        logSync( $username, $comment, $match[ 'rev' ], "csssync", $data );

        return $data;
    }

    function getSyncInfo( $syncid ) {
        $syncid = ( int )$syncid;
        $res = mysql_query( "SELECT * FROM `sync` LEFT JOIN `users` ON `sync_userid` = `user_id` WHERE `sync_id` = " . $syncid . " LIMIT 1;" );
        if ( !mysql_num_rows( $res ) ) {
            return false;
        }
        return mysql_fetch_array( $res );
    }

?>
