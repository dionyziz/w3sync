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
    $comment = $_POST[ 'comment' ];
    $revision =  $_POST[ 'revision' ];

    switch ( $_POST[ 'do' ] ) {
        case 'sync':
            $syncid = Sync_Core( $revision, $_SERVER[ 'REMOTE_USER' ], $comment );
            break;
        case 'csssync':
            $syncid = Sync_Static( $revision, $_SERVER[ 'REMOTE_USER' ], $comment );
            break;
        default:
            ?><p>Invalid option.</p><?php
            include 'footer.php';
            return;
    }

    ob_clean();
    header( 'Location: info.php?syncid=' . $syncid );
?>
