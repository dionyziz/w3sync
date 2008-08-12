<?php
    include 'header.php';

    if ( !count( $_POST ) ) {
        return;
    }

    if( empty( $_POST[ 'comment' ] ) ) {
        ?><p>A comment is required. No lock obtained.</p><?php
        include 'footer.php';
        return;
    }

    $userid = User_ByName( $_SERVER[ 'REMOTE_USER' ] );
    $locks = Lock_GetActive();
    foreach ( $locks as $lock ) {
        if ( $lock[ 'lock_userid' ] == $userid ) {
            ?><p>You already have an active lock.</p><?php
            include 'footer.php';
            return;
        }
    }

    Lock_Obtain( $_POST[ 'comment' ], $_SERVER[ 'REMOTE_USER' ] );

    ?>A sync lock was placed. Make sure you remove it when you're done working.<br />
    <small>Prefer to use masking instead of sync locks to assist your fellow hackers in their daily tasks.</small><?php
?>
