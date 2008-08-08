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
            echo Sync( 0, $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ]);
            ?></pre><?php
            break;
        case 'beta':
            // TODO
	        ?>
	        A Sync is being made:
	        <pre><?php
	        $revstring = system( "wget -O - http://zeus.blogcube.net/sync/beta.php" );
	        preg_match( "/revision (?<rev>\w+)./", $revstring, $match );
	        logSync( $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $match[ 'rev' ], "sync" );
	        ?></pre><?php
            break;
        case 'csssync':
	        // do the main syncing
            echo StaticSync( 0, $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ] );
            break;
        default:
            ?><p>Invalid option</p><?php
    }
    ?><a href="index.php">back</a><?php
?>
