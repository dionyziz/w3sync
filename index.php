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
        $lockusernames = $usernames;
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
    $lastSyncs = Log_GetLatest( 15 );
    ?><form method="POST" action="sync.php" onsubmit="return checkForm()"><br />
    <table><thead><tr><td>Revision</td><td>Developer</td><td>Reason</td><td>When</td><td>&nbsp;</td></tr></thead><tbody><?php
    if ( !count( $locks ) ) {
        ?><tr><td>
                <input type="text" value="<?php
                $revision = SVN_GetCurrentRevision();
                echo $revision;
                ?>" name="revision" id="revision" /><br />
            </td><td><?php
                echo htmlspecialchars( $_SERVER[ 'REMOTE_USER' ] );
            ?></td><td>
                <input name="comment" id="comment" />
            </td><td>
            <input type="submit" value="Deploy to Production" />
        </td></tr><?php
    }
    $i = 1;
    $latestrevision = $lastSyncs[ 0 ][ 'sync_rev' ];
    foreach ( $lastSyncs as $sync ) {
        ?><tr<?php
        if ( $i % 2 == 0 ) {
            ?> class="l"<?php
        }
        ?>><td><a href="info.php?syncid=<?php
        echo $sync[ 'sync_id' ];
        ?>"><?php
        echo $sync[ 'sync_rev' ];
        ?></a><?php
        if ( $sync[ 'rollback' ] ) {
            ?> <img src="images/arrow_undo.png" alt="Rollback to " title="Rolled back to revision <?php
            echo $sync[ 'sync_rev' ];
            ?>" /><?php
        }
        ?></td><td><?php
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
        echo $sync[ 'sync_comment' ];
        ?></td><td><?php
        if ( $sync[ 'sync_created' ] == '0000-00-00 00:00:00' ) {
            ?>(unknkown)<?php
        }
        else {
            echo dateDiffText( $sync[ 'sync_created' ] );
        }
        ?></td><td><?php
        if ( $sync[ 'sync_rev' ] < $latestrevision ) {
            ?><a class="rollback" href="" onclick="Rollback( '<?php
            echo $sync[ 'sync_rev' ];
            ?>' );return false;">Rollback to here</a><?php
        }
        ?></td></tr><?php
        ++$i;
    }
    ?></tbody></table></form>

    <form id="rollback" action="sync.php" method="POST" style="display:none">
        <input type="hidden" name="comment" />
        <input type="hidden" name="revision" value="" />
        <input type="hidden" name="do" value="sync" />
    </form>

    <script type="text/javascript">
        document.getElementById( 'comment' ).focus();
        function Rollback( revision, anchor ) {
            var reason = prompt( 'Why do you want to rollback to revision ' + revision + '?' );

            if ( reason === '' || reason === null ) {
                return;
            }

            var form = document.getElementById( 'rollback' );
            
            form.getElementsByTagName( 'input' )[ 0 ].value = reason;
            form.getElementsByTagName( 'input' )[ 1 ].value = revision;
            form.submit();
        }
        function checkForm() {
            if ( document.getElementById( 'comment' ).value == '' ) {
                alert( 'Please provide a reason for your sync' );
                document.getElementById( 'comment' ).focus();
                return false;
            }
            if ( document.getElementById( 'revision' ).value < <?php
            echo $latestrevision;
            ?> - 100 ) {
                if ( !confirm( "You are about to sync to revision " + document.getElementById( 'revision' ).value + ". This is a substancially old revision!\nAre you sure you want to sync?" ) ) {
                    return false;
                }
            }
            var div = document.createElement( 'div' );
            div.style.position = 'fixed';
            div.style.zIndex = '10';
            div.style.left = '0';
            div.style.right = '0';
            div.style.top = '0';
            div.style.bottom = '0';
            div.style.backgroundColor = 'black';
            div.style.opacity = '0.7';
            div.style.textAlign = 'center';
            div.style.color = 'white';
            div.style.padding = '50px';
            
            var img = document.createElement( 'img' );
            div.appendChild( document.createTextNode( 'Now deploying. This may take a few minutes.' ) );
            div.appendChild( document.createElement( 'br' ) );
            div.appendChild( img );
            img.src = 'images/ajax-loader.gif';
            img.alt = 'Deploying...';
            img.style.padding = '50px';

            document.body.appendChild( div );

            return true;
        }
        function checkLockForm() {
            if ( document.getElementById( 'lockcomment' ).value == '' ) {
                alert( 'Please provide a reason for your lock' );
                document.getElementById( 'lockcomment' ).focus();
                return false;
            }
            return true;
        }
        var myImage = new Image();
        myImage.src = 'images/ajax-loader.gif'; // preload
    </script>

    <?php
    if ( !count( $locks ) ) {
        ?>
        <br /><br />
        No active sync locks are currently placed.<br />
        <small>Use sync locks to enforce no syncs for a limited time.</small><?php
    }
    else {
        ?><h2>Sync locks</h2>
        <table><thead><tr><td>Developer</td><td>Reason</td><td>When</td><td>&nbsp;</td></tr></thead><tbody><?php
        $i = 1;
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
            echo dateDiffText( $lock[ 'lock_created' ] );
            ?></td><td><?php
            if ( $_SERVER[ 'REMOTE_USER' ] == $lock[ 'user_name' ] ) {
                ?><form style="display:inline" action="unlock.php" method="POST">
                    <input type="hidden" name="lockid" value="<?php
                    echo $lock[ 'lock_id' ];
                    ?>" /><input type="submit" value="Unlock" />
                </form><?php
            }
            ?></td></tr><?php
        }
        ?></tbody></table><?php
    }
    if ( !isset( $lockusernames[ $_SERVER[ 'REMOTE_USER' ] ] ) ) { // don't allow them to add a second simultanious lock
        ?>
        <br /><br />
        <form action="lock.php" method="POST" onsubmit="return checkLockForm()">
            Reason: <input name="comment" value="" id="lockcomment" /> <input type="submit" value="Sync Lock" />
        </form><?php
    }
    include 'footer.php';
?>
