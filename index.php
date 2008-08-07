<?php
    include "header.php";

    if( !isset( $_SERVER[ 'HTTPS' ] ) ) {
        header( "301 Moved Permanently" );
        header( "Location: https://code.kamibu.com/sync/" );
        die();
    }

    ?>
    <h1>Deploy Zino</h1>
    Greetings fellow zino hacker, <?php
    echo $_SERVER[ 'REMOTE_USER' ];
    ?>!<br />
    What do you want to do?
    <p class="caution">All actions performed in this page get logged. Your actions are not undoable and affect the production environment directly.</p>
    <form method="POST" action="sync.php">
        <input type="radio" name="do" value="sync" />Sync<br />
        <!-- <input type="radio" name="do" value="beta" />Beta Sync (fast for the user - might be broken)<br /> -->
        <input type="radio" name="do" value="csssync" />CSS and JS Sync<br />
        <br />
        Comment (required): <br />
        <textarea name="comment"></textarea><br />

        <input type="submit" value="Deploy to Production" />
    </form>
    <h2>Last syncs</h2><?php
    $lastSyncs = getLastSyncs();
    ?><table><thead><tr><td>Date</td><td>Developer</td><td>Revision</td><td>Type</td><td>Reason</td></tr></thead><tbody><?php
    foreach ( $lastSyncs as $sync ) {
        ?><tr><td><?php
        echo date( "r", $sync[ 'sync_date' ] );
        ?></td><td><a href="mailto:<?php
        echo $sync[ 'user_name' ];
        ?>@kamibu.com"><?php
        echo $sync[ 'user_name' ];
        ?></a></td><td><?php
        echo $sync[ 'sync_rev' ];
        ?></td><td><?php
        echo $sync[ 'sync_type' ];
        ?></td><td><?php
        echo $sync[ 'sync_comment' ];
        ?></td></tr><?php
    }
    ?></tbody></table><?php

    include 'footer.php';
?>
