<?php
	if ( $_SERVER[ 'REMOTE_ADDR' ] != "88.198.246.217" ) {
        die( 'Access from ' . $_SERVER[ 'REMOTE_ADDR' ] . ' is denied' );
	}
	$now = time();
	if ( isset( $_GET[ 'revision' ] ) ) {
		$revision = ( int )$_GET[ 'revision' ];
	}
	if ( !( $revision > 0 ) ) {
		?>Invalid revision specified.<?php
		return;
	}
	echo "Syncing to revision $revision.\n";
	system( 'sudo -u syncer sync ' . $revision );
?>
