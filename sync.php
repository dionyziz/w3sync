<?php
    include 'header.php';

    if ( !count( $_POST ) ) {
        return;
    }

    if( empty( $_POST[ 'comment' ] ) ) {
        ?><p>A comment is required. No sync made.</p><?php
        return;
    }
    if ( empty( $_POST[ 'revision' ] ) ) {
        ?><p>You must provide a revision number. No sync made.</p><?php
        return;
    }

    switch ( $_POST[ 'do' ] ) {
        case 'sync':
            ?>
            A Sync is being made:
            <pre><?php
            echo htmlspecialchars( Sync_Core( $_POST[ 'revision' ], $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $syncid ) );
            ?></pre><?php
            break;
        case 'csssync':
	        // do the main syncing
            ?>
            A Static Sync is being made:
            <pre><?php
            echo htmlspecialchars( Sync_Static( 0, $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ], $syncid ) );
            ?></pre><?php
            break;
        default:
            ?><p>Invalid option</p><?php
    }

    ob_clean();
    header( 'Location: info.php?syncid=' . $syncid );
    return;
?>
