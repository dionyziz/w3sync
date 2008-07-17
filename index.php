<html>
<head>
<title>Kamibu Sync Services</title>
</head>
<body>
<?php
include( "libs/functions.php" );
if( !isset( $_POST[ 'do' ] )) {
	?>
	Hey <?php echo $_SERVER['REMOTE_USER'] ?>!<br />
	What do you want to do?
	<p style="color: red;">Take care:<br />Every action on this page is being made directly without a second click on 'yes'. All actions got logged.</p>
	<form method="POST" action="index.php">
		<input type="radio" name="do" value="nothing" checked />Nothing<br />
		<input type="radio" name="do" value="sync" />Sync<br />
		<input type="radio" name="do" value="csssync" />CSS and JS Sync<br />

		<input type="submit" value="Do it" />
	</form>
	<p>Last syncs:<br />
	<?php
	$lastSyncs = getLastSyncs();
	for( $x = 0; $x < count( $lastSyncs ); $x++ ) {
		echo date( "c", $lastSyncs[ $x ][ 'sync_date' ] ) . " " .$lastSyncs[ $x ][ 'user_name' ] . " (" . $lastSyncs[ $x ][ 'sync_rev' ] . " - " . $lastSyncs[ $x ][ 'sync_type' ] . "): " . $lastSyncs[ $x ][ 'sync_comment' ] . "<br />";
	}
	?>
	</p>
	<?php
} else {
	if( $_POST[ 'do' ] == "nothing" ) {
		?>Nothing to do? Well, you're really on the safe side, guy!<?php
	} elseif( $_POST[ 'do' ] == "sync" ) {
		?>
		A Sync is being made:
		<pre><?php
		system( "wget -O - http://zeus.blogcube.net/sync/" )
		?></pre>
		<a href="index.php">back</a>
		<?php
	} elseif( $_POST[ 'do' ] == "csssync" ) {
		logSync( $_SERVER[ 'REMOTE_USER' ], "test comment", 112, "csssync" );
		?>
		This is not working yet :P
		<a href="index.php">back</a>
		<?php
	}
}
?>
</body>
</html>
