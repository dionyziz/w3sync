<?php
    include "header.php";

    $locks = Lock_GetActive();

    if ( count( $locks ) ) {
        $usernames = array();
        $i = 0;
        foreach ( $locks as $lock ) {
            $usernames[ $lock[ 'user_name' ] ] = $i;
            ++$i;
        }
        $usernames = array_flip( $usernames );
        foreach ( $usernames as $i => $username ) {
            $usernames[ $i ] = '<a href="mailto:' . htmlspecialchars( $username ) . '@kamibu.com">' . htmlspecialchars( $username ) . '</a>';
        }
        $usernames = array_values( $usernames );

        ?><img src="images/lock.png" alt="Locked:" /> <?php
        if ( count( $usernames ) > 1 ) {
            $usernames[ count( $usernames ) - 1 ] = 'and ' . $usernames[ count( $usernames ) -1 ];
        }
        echo implode( ', ', $usernames );
        if ( count( $usernames ) > 1 ) {
            ?> have<?php
        }
        else {
            ?> has<?php
        }
        ?> requested a sync lock. You cannot currently sync.<?php
    }
    else {
        ?> What do you want to do?
        <form method="POST" action="sync.php">
            <input type="radio" name="do" value="sync" checked="checked" id="sync" onchange="document.getElementById( 'comment' ).focus();document.getElementById( 'comment' ).select()" /><label for="sync">Sync</label><br />
            <!-- <input type="radio" name="do" value="beta" />Beta Sync (fast for the user - might be broken)<br /> -->
            <input type="radio" name="do" value="csssync" id="csssync" onchange="document.getElementById( 'comment' ).focus();document.getElementById( 'comment' ).select()" /><label for="csssync">CSS and JS Sync</label><br />
            <br />
            <!-- Revision: <input type="text" value="<?php
            $revision = getCurrentRevision();
            echo $revision;
            ?>" name="revision" /><br /> -->
            Reason (required): <br />
            <textarea name="comment" id="comment"></textarea><br />

            <script type="text/javascript">
                document.getElementById( 'comment' ).focus();
            </script>
            <input type="submit" value="Deploy to Production" />
        </form><?php
    }
    ?>
    <h2>Last syncs</h2><?php
    $lastSyncs = getLastSyncs( 20 );
    ?><table><thead><tr><td>Revision</td><td>Developer</td><td>Type</td><td>Reason</td><td>Date</td></tr></thead><tbody><?php
    $i = 1;
    foreach ( $lastSyncs as $sync ) {
        ?><tr<?php
        if ( $i % 2 == 0 ) {
            ?> class="l"<?php
        }
        ?>><td><?php
        if ( $sync[ 'rollback' ] ) {
            ?><img src="images/arrow_undo.png" alt="Rollback to " title="Rolled back to revision <?php
            echo $sync[ 'sync_rev' ];
            ?>" /> <?php
        }
        ?><a href="info.php?syncid=<?php
        echo $sync[ 'sync_id' ];
        ?>"><?php
        echo $sync[ 'sync_rev' ];
        ?></a></td><td><?php
        if ( empty( $sync[ 'user_name' ] ) ) {
            ?>(unknown)<?php
        }
        else { 
            ?><a href="mailto:<?php
            echo htmlspecialchars( $sync[ 'user_name' ] );
            ?>@kamibu.com"><?php
            echo htmlspecialchars( $sync[ 'user_name' ] );
            ?></a><?php
        }
        ?></td><td><?php
        echo $sync[ 'sync_type' ];
        ?></td><td><?php
        echo $sync[ 'sync_comment' ];
        ?></td><td><?php
        if ( $sync[ 'sync_created' ] == '0000-00-00 00:00:00' ) {
            ?>(unknkown)<?php
        }
        else {
            echo date( "r", strtotime( $sync[ 'sync_created' ] ) );
        }
        ?></td></tr><?php
        ++$i;
    }
    ?></tbody></table>
    <h2>Sync locks</h2><?php
    if ( !count( $locks ) ) {
        ?>No active sync locks are currently placed.<br />
        <small>Use sync locks to force no syncs for a limited time.</small><?php
    }
    else {
        ?><table><thead><tr><td>Developer</td><td>Reason</td><td>Date</td><td>&nbsp;</td></tr></thead><tbody><?php
        foreach ( $locks as $lock ) {
            ?><tr<?php
            if ( $i % 2 == 0 ) {
                ?> class="l"<?php
            }
            ?><td><a href="mailto:<?php
            echo htmlspecialchars( $lock[ 'user_name' ] );
            ?>"><?php
            echo htmlspecialchars( $lock[ 'user_name' ] );
            ?></a></td><td><?php
            echo htmlspecialchars( $lock[ 'lock_reason' ] );
            ?></td><td><?php
            echo date( "r", strtotime( $lock[ 'lock_created' ] ) );
            ?></td><td><?php
            if ( $_SERVER[ 'REMOTE_USER' ] == $lock[ 'user_name' ] ) {
                ?><form style="display:inline" action="unlock.php" method="post">
                    <input type="hidden" name="lockid" value="<?php
                    echo $lock[ 'lock_id' ];
                    ?>" /><input type="submit" value="Delete" />
                </form><?php
            }
            ?></td></tr><?php
        }
        ?></tbody></table><?php
    }
    ?>
    <br /><br />
    Place a sync lock:<br />
    <form action="lock.php" method="post">
        Reason (required): <br />
        <textarea name="comment"></textarea><br />

        <input type="submit" value="Sync Lock" />
    </form><?php
    include 'footer.php';
?>
