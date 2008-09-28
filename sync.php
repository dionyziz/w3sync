<?php
	include 'header.php';

	if ( !count( $_POST ) ) {
		return;
	}

	if( empty( $_POST[ 'comment' ] ) ) {
		?><p>A comment is required. No sync made.</p><?php
		include 'footer.php';
		return;
	}
	if ( empty( $_POST[ 'revision' ] ) ) {
		?><p>You must provide a revision number. No sync made.</p><?php
		include 'footer.php';
		return;
	}
	$locks = Lock_GetActive();
	if ( !empty( $locks ) ) {
		?><p>Could not sync at this time, because there are active sync locks.</p><?php
		include 'footer.php';
		return;
	}

	$comment = $_POST[ 'comment' ];
	$revision =  $_POST[ 'revision' ];
	if ( $revision == 'latest' ) {
		$revision = SVN_GetCurrentRevision();
	}

	$syncid = Sync( $revision, $_SERVER[ 'REMOTE_USER' ], $comment );

	ob_clean();
	header( 'Location: info.php?syncid=' . $syncid );
?>
