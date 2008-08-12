<?php
    function Log_Create( $username, $comment, $rev, $diff ) {
        Log_Mail( $username, $comment, $rev, $diff );
        $userid = getUserByName( $username );
        $comment = mysql_real_escape_string( $comment );
        $diff = mysql_real_escape_string( $diff );
        $sql = "INSERT INTO `sync`
                ( `sync_id`, `sync_userid`, `sync_comment`, `sync_created`, `sync_rev`, `sync_diff` ) VALUES
                ( NULL , '$userid', '$comment', NOW(), '$rev', '$diff' );";
        mysql_query( $sql ) or die( mysql_error() );

        return mysql_insert_id();
    }

    function Log_Mail( $username, $comment, $rev, $type, $diff ) {
        if ( !preg_match( '#^[a-z0-9-\_]+$#', $username ) ) {
            return;
        }

        $text = "Author: $username\nDate: " 
                . date( 'r' ) 
                . "\n" 
                . "Synced to revision: $rev\nReason: $comment\n\n$diff\n";
        
        mail( "svn@kamibu.com", "[SYNC] $rev - $comment", $text, "From: $username@kamibu.com" );
    }

    function Log_GetLatest( $limit = 20 ) {
        $limit = ( int )$limit;
        // the two left joins with itself are for retrieving the boolean "rollback" value which determines
        // whether the sync is a rollback -- essentially we're looking for the exact previous sync_id
        // for each row and compare its sync_rev value with the current row's sync_rev value.
        $sql = "SELECT
                    `sync`.*, previoussync.`sync_rev`>`sync`.sync_rev AS rollback, `users`.*
                FROM
                    `sync` LEFT JOIN `users`
                        ON `sync_userid` = `user_id`
                    LEFT JOIN `sync` AS previoussync
                        ON `sync`.`sync_id`>previoussync.`sync_id`
                    LEFT JOIN `sync` AS maxfilter
                        ON previoussync.`sync_id`<maxfilter.`sync_id` AND maxfilter.`sync_id`<`sync`.`sync_id`
                WHERE
                    maxfilter.`sync_id` IS NULL
                GROUP BY
                    `sync`.`sync_id`
                ORDER BY 
                    `sync_id` DESC
                LIMIT " . $limit;
        $res = mysql_query( $sql ) or die( mysql_error() );
        $return = array();
        while( $row = mysql_fetch_array( $res ) ) {
            $return[] = $row;
        }
        return $return;
    }

    function Log_GetById( $syncid ) {
        $syncid = ( int )$syncid;
        $res = mysql_query( "SELECT * FROM `sync` LEFT JOIN `users` ON `sync_userid` = `user_id` WHERE `sync_id` = " . $syncid . " LIMIT 1;" );
        if ( !mysql_num_rows( $res ) ) {
            return false;
        }
        return mysql_fetch_array( $res );
    }
?>
