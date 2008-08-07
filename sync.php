<?php
    include 'header.php';

    if ( !count( $_POST ) ) {
        return;
    }

    if( empty( $_POST[ 'comment' ] ) ) {
        ?><p>A comment is required. No sync made.</p><?php
        return;
    }

    switch ( $_POST[ 'do' ] ) {
        case 'sync':
            ?>
            A Sync is being made:
            <pre><?php
            $revstring = system( "wget -O - http://zeus.blogcube.net/sync/" );
            preg_match( "/revision (?<rev>\w+)./", $revstring, $match );
            logSync( $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $match[ 'rev' ], "sync" );
            ?></pre><?php
            break;
        case 'beta':
	        ?>
	        A Sync is being made:
	        <pre><?php
	        $revstring = system( "wget -O - http://zeus.blogcube.net/sync/beta.php" );
	        preg_match( "/revision (?<rev>\w+)./", $revstring, $match );
	        logSync( $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $match[ 'rev' ], "sync" );
	        ?></pre><?php
            break;
        case 'csssync':
	        // get current rev. this will be the rev of the new synced version
	        $revstring = exec( "svn info /var/www/zino.gr/beta/phoenix/|grep Revision" );
	        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
	        logSync( $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $match[ 'rev' ], "csssync" );
	        // do the main syncing
	        exec( "cat /var/www/zino.gr/static/css/global-beta.css > /var/www/zino.gr/static/css/global.css" );
	        exec( "cat /var/www/zino.gr/static/js/global-beta.js > /var/www/zino.gr/static/js/global.js" );
	        ?>
	        You successfully synced to revision <?php
            echo $match[ 'rev' ];
            ?>.<?php
            break;
        default:
            ?><p>Invalid option</p><?php
    }
    ?><a href="index.php">back</a><?php
?>
