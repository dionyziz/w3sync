<?php
    function Sync( $revision, $username, $comment ) {
        $revision = ( int )$revision;

        // the order is significant; static must be synced before the core so that the changes in css/js are propagated
        // before the version invalidation kick in
        $t = microtime( true );
        $static = Sync_Static( $revision, $username, $comment );
        $statictook = round( microtime( true ) - $t, 2 );
        $t = microtime( true );
        $core = Sync_Core( $revision, $username, $comment );
        $coretook = round( microtime( true ) - $t, 2 );

        $data = "Syncing core...\n" . $core . "\nCore syncing took $coretook seconds.\n\nSyncing static...\n" . $static . "\nStatic syncing took $statictook seconds.\n";

        $syncid = Log_Create( $username, $comment, $revision, $data );

        return $syncid;
    }

    function Sync_Core( $revision, $username, $comment ) {
		exec( "wget -O - http://deploy2.zino.gr:500/sync.php?revision=" . $revision, $output, $ret );
        $data .= implode( "\n", $output );

        $latestsync = Log_GetLatest( 1 );
        if ( empty( $latestsync ) ) {
            $previousrevision = 0;
        }
        else {
            $previousrevision = $latestsync[ 0 ][ 'sync_rev' ];
        }
        $diff = SVN_Diff( $previousrevision, $revision );
        if ( empty( $diff ) ) {
            $diff = "(none)";
        }
        else {
            $diff = "\n\n$diff";
        }
        $data .= "\nDiff between revisions $previousrevision and $revision: $diff";

        return $data;
    }
    
    function Sync_Static( $revision, $username, $comment ) {
		exec( "wget -O - http://gaia.kamibu.com/sync/syncstatic.php?revision=" . $revision, $output, $ret );

        $data = implode( "\n", $output );
        $data .= "\nGenerated global.css and global.js revision " . $revision . ".\n";

        return $data;
    }
?>
