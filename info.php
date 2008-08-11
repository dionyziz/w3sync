<?php
    include "header.php";

    $syncid = ( int )$_GET[ 'syncid' ];

    ?>
    <h2>Sync #<?php
    echo $syncid;
    ?></h2><?php
    $sync = Log_GetById( $syncid );
    if ( $sync === false ) {
        ?>There was no such sync.<?php
    }
    else {
        ?><ul><li><strong>Author:</strong> <a href="mailto:<?php
        echo htmlspecialchars( $sync[ 'user_name' ] );
        ?>@kamibu.com"><?php
        echo htmlspecialchars( $sync[ 'user_name' ] );
        ?></a></li><li><strong>Revision:</strong> <?php
        echo htmlspecialchars( $sync[ 'sync_rev' ] );
        ?></li><li><strong>Reason:</strong> <?php
        echo htmlspecialchars( $sync[ 'sync_comment' ] );
        ?></li><li><strong>Date:</strong> <?php
        echo date( 'r', $sync[ 'sync_date' ] );
        ?></li><li><strong>Type:</strong> <?php
        echo htmlspecialchars( $sync[ 'sync_type' ] );
        ?></li></ul>
            
        <div class="diff"><?php
        $diff = $sync[ 'sync_diff' ];
        $diff = htmlspecialchars( $diff );
        $diff = str_replace( "\t", "    ", $diff );
        $diff = preg_replace( '# (?= )#', '&nbsp;', $diff );
        $diff = explode( "\n", $diff );
        foreach ( $diff as $line ) {
            switch ( $line{ 0 } ) {
                case '+':
                    $line = '<span class="added">' . $line . '</span>';
                    break;
                case '-':
                    $line = '<span class="removed">' . $line . '</span>';
                    break;
            }
        }
        echo nl2br( $diff );
        ?></pre><?php
    }
    include 'footer.php';
?>
