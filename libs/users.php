<?php
function getUserByName( $username ) {
	$sql = "SELECT * FROM `users` WHERE `user_name` = '$username';";
	$result = mysql_query( $sql );
	$data = mysql_fetch_array( $result );
	return $data[ 'user_id' ];
}
?>
