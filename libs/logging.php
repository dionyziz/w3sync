<?php
function logSync( $username, $comment, $rev, $type ) {
    $userid = getUserByName( $username );
    $sql = "INSERT INTO `sync` ( `sync_id`, `sync_userid`, `sync_comment`, `sync_date`, `sync_rev`, `sync_type`) VALUES ( NULL , '$userid', '$comment', '" . mktime() . "', '$rev', '$type' );";
    mysql_query( $sql );
	
	mailSync( $username, $comment, $rev, $type );
}

function mailSync( $username, $comment, $rev, $type ) {
	$types = array();
	$types[ 'sync' ] = "core";
	$types[ 'csssync' ] = "css / js";

	$text = $username . " synced on " . $types[ $type ] . " to revision " . $rev . "\n\nComment:\n" . $comment;
	
	mail( "svn@kamibu.com", "[SYNC] $username: " . $types[ $type ] . " - $rev", $text );
}

function getLastSyncs() {
    $sql = "SELECT * FROM `sync` ORDER BY `sync_date` DESC LIMIT 0, 10;";
    $res = mysql_query( $sql );
    $return = array();
    while( $row = mysql_fetch_array( $res )) {
        $sql = "SELECT * FROM `users` WHERE `user_id` = " . $row[ 'sync_userid' ] . ";";
        $res2 = mysql_query( $sql );
        $temp = mysql_fetch_array( $res2 );
        $row[ 'user_name' ] = $temp[ 'user_name' ];
        $return[] = $row;
    }
    return $return;
}
?>
