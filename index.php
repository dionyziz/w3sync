<?php
    if( !isset( $_SERVER[ 'HTTPS' ] ) ) {
        header( "301 Moved Permanently" );
        header( "Location: https://code.kamibu.com/sync/" );
        die();
    }

    include "header.php";

    ?>
    <div class="username"><?php
    echo htmlspecialchars( $_SERVER[ 'REMOTE_USER' ] );
    ?>@kamibu.com</div>
    <h1>Deploy Zino</h1>
    What do you want to do?
    <form method="POST" action="sync.php">
        <input type="radio" name="do" value="sync" checked="checked" />Sync<br />
        <!-- <input type="radio" name="do" value="beta" />Beta Sync (fast for the user - might be broken)<br /> -->
        <input type="radio" name="do" value="csssync" />CSS and JS Sync<br />
        <br />
        Comment (required): <br />
        <textarea name="comment"></textarea><br />

        <input type="submit" value="Deploy to Production" />
    </form>
    <h2>Last syncs</h2><?php
    $lastSyncs = getLastSyncs();
    ?><table><thead><tr><td>Revision</td><td>Developer</td><td>Type</td><td>Reason</td><td>Date</td></tr></thead><tbody><?php
    $i = 1;
    foreach ( $lastSyncs as $sync ) {
        ?><tr<?php
        if ( $i % 2 == 0 ) {
            ?> class="l"<?php
        }
        ?>><td><?php
        echo $sync[ 'sync_rev' ];
        ?></td><td><?php
        if ( empty( $sync[ 'user_name' ] ) ) {
            ?>(unknown)<?php
        }
        else { 
            ?><a href="mailto:<?php
            echo $sync[ 'user_name' ];
            ?>@kamibu.com"><?php
            echo $sync[ 'user_name' ];
            ?></a><?php
        }
        ?></td><td><?php
        echo $sync[ 'sync_type' ];
        ?></td><td><?php
        echo $sync[ 'sync_comment' ];
        ?></td><td><?php
        echo date( "r", $sync[ 'sync_date' ] );
        ?></td></tr><?php
        ++$i;
    }
    ?></tbody></table><?php

    include 'footer.php';
?>
