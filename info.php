<?php
    include "header.php";

    $syncid = ( int )$_GET[ 'syncid' ];

    ?>
    <div class="username"><?php
    echo htmlspecialchars( $_SERVER[ 'REMOTE_USER' ] );
    ?>@kamibu.com</div>
    <h1>Deploy Zino</h1>
    <h2>Sync #<?php
    echo $syncid;
    ?></h2><?php
    $sync = getSyncInfo( $syncid );
    if ( $sync === false ) {
        ?>There was no such sync.<?php
    }
    else {
        ?><ul><li><strong>Author:</strong> <?php
        echo $sync[ 'user_name' ];
        ?></li><li><strong>Revision:</strong> <?php
        echo $sync[ 'sync_rev' ];
        ?></li><li><strong>Reason:</strong> <?php
        echo $sync[ 'sync_comment' ];
        ?></li><li><strong>Date:</strong> <?php
        echo date( 'r', $sync[ 'sync_date' ] );
        ?></li><li><strong>Type:</strong> <?php
        echo $sync[ 'sync_type' ];
        ?></li></ul><pre><?php
        echo htmlspecialchars( $sync[ 'sync_diff' ] );
        ?></pre><?php
    }
    ?><a href="index.php">Back</a><?php

    include 'footer.php';
?>
