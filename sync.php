<?php
    include 'header.php';

    if ( !count( $_POST ) ) {
        return;
    }

    if( empty( $_POST[ 'comment' ] ) ) {
        ?><p>A comment is required. No sync made.</p><?php
        include 'footer.php';
        return;
    }
    if ( empty( $_POST[ 'revision' ] ) ) {
        ?><p>You must provide a revision number. No sync made.</p><?php
        include 'footer.php';
        return;
    }

    switch ( $_POST[ 'do' ] ) {
        case 'sync':
            $syncid = Sync_Core( $_POST[ 'revision' ], $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ] );
            break;
        case 'csssync':
            $syncid = Sync_Static( $_POST[ 'revision' ], $_SERVER[ 'REMOTE_USER' ], $_POST[ 'comment' ] );
            break;
        default:
            ?><p>Invalid option.</p><?php
            include 'footer.php';
            return;
    }

    ob_clean();
    header( 'Location: info.php?syncid=' . $syncid );
?>
