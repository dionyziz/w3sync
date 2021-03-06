<?php
    include "header.php";

    $syncid = ( int )$_GET[ 'syncid' ];

    $sync = Log_GetById( $syncid );
    if ( $sync === false ) {
        ?>There was no such sync.<?php
        return include 'footer.php';
    }

    $latest = Log_GetLatest( 1 );
    $oldest = Log_GetLatest( 1, 'ASC' );
    assert( count( $latest ) == 1 ); // since at least one sync item exists (per check above)
    assert( count( $oldest ) == 1 );
    $latest = $latest[ 0 ];
    $oldest = $oldest[ 0 ];
    $pivot = Log_GetPivot( $syncid, 25 );
    ?><div id="history" class="history"><?php
    if ( $pivot[ 0 ][ 'sync_id' ] != $oldest[ 'sync_id' ] ) {
        ?><img src="images/bullet_go.png" alt="-&gt;" title="There were older syncs than this" /><?php
    }
    $i = 0;
    foreach ( $pivot as $item ) {
        ?><div<?php
        if ( $item[ 'sync_id' ] == $sync[ 'sync_id' ] ) {
            ?> class="selected"<?php
        }
        ?>><a href="info.php?syncid=<?php
        echo $item[ 'sync_id' ];
        ?>"><?php
        echo htmlspecialchars( $item[ 'user_name' ] );
        if ( isset( $item[ 'rollback' ] ) ) {
            ?> back<?php
        }
        ?> to <?php
        echo $item[ 'sync_rev' ];
        ?></a></div><?php
        if ( $i != count( $pivot ) - 1 ) {
            ?><img src="images/bullet_go.png" alt="-&gt;" /><?php
        }
        ++$i;
    }
    if ( $pivot[ count( $pivot ) - 1 ][ 'sync_id' ] != $latest[ 'sync_id' ] ) {
        ?><img src="images/bullet_go.png" alt="-&gt;" title="There were more recent syncs than this" /><?php
    }
    ?><a id="endofhistory"></a></div><div class="eof"></div>
    <ul><li><strong>Author:</strong> <a href="mailto:<?php
    echo htmlspecialchars( $sync[ 'user_name' ] );
    ?>@kamibu.com"><?php
    echo htmlspecialchars( $sync[ 'user_name' ] );
    ?></a></li><li><strong>Revision:</strong> <?php
    echo htmlspecialchars( $sync[ 'sync_rev' ] );
    ?></li><li><strong>Reason:</strong> <?php
    echo htmlspecialchars( $sync[ 'sync_comment' ] );
    ?></li><li><strong>Date:</strong> <?php
    echo dateDiffText( $sync[ 'sync_created' ] );
    ?></li></ul>
        
    <div class="diff" id="diff"><?php
    $diff = $sync[ 'sync_diff' ];
    $diff = htmlspecialchars( $diff );
    $diff = str_replace( "\t", "    ", $diff );
    $diff = preg_replace( '# (?= )#', '&nbsp;', $diff );
    $diff = explode( "\n", $diff );
    foreach ( $diff as $i => $line ) {
        switch ( $line{ 0 } ) {
            case '+':
                if ( substr( $line, 0, 3 ) == '+++' ) {
                    $diff[ $i ] = '<span class="file added">' . $line . '</span>';
                }
                else {
                    $diff[ $i ] = '<span class="added">' . $line . '</span>';
                }
                break;
            case '-':
                $diff[ $i ] = '<span class="removed">' . $line . '</span>';
                break;
        }
    }
    $diff = implode( "<br />", $diff );
    echo $diff;
    ?></div>
    <script type="text/javascript">
        function scrollToRevision() {
            var history = document.getElementById( 'history' );
            var divs = history.getElementsByTagName( 'div' );
            var j = 0;

            document.getElementById( 'endofhistory' ).scrollIntoView( true );

            for ( i = 0; i < divs.length; ++i ) {
                if ( divs[ i ].className == 'selected' ) {
                    j = i;
                    i -= 3;
                    if ( i < 0 ) {
                        i = 0;
                    }
                    divs[ i ].scrollIntoView();
                    break;
                }
            }

            divs[ j ].scrollIntoView();

            document.getElementsByTagName( 'html' )[ 0 ].scrollIntoView( true );
        }
        scrollToRevision();
        document.getElementById( 'diff' ).style.display = '';
    </script><?php

    include 'footer.php';
?>
