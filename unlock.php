<?php
    include 'header.php';

    if ( !count( $_POST ) ) {
        return;
    }

    if( empty( $_POST[ 'lockid' ] ) ) {
        ?><p>A lockid is required. No lock shifted.</p><?php
        return;
    }

    Lock_Disable( $_POST[ 'lockid' ], $_SERVER[ 'REMOTE_USER' ] );

    ob_clean();
    header( 'Locaiton: /' );
    return;
?>
