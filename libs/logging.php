<?php
    global $types;

    $types = array(
        'sync' => 'core',
        'csssync' => 'css / js'
    );

    function Log_Create( $username, $comment, $rev, $type, $diff ) {
        global $types;

        if ( !isset( $types[ $type ] ) ) {
            return;
        }
        Log_Mail( $username, $comment, $rev, $type, $diff );
        $userid = getUserByName( $username );
        $comment = mysql_real_escape_string( $comment );
        $diff = mysql_real_escape_string( $diff );
        $sql = "INSERT INTO `sync`
                ( `sync_id`, `sync_userid`, `sync_comment`, `sync_created`, `sync_rev`, `sync_type`, `sync_diff` ) VALUES
                ( NULL , '$userid', '$comment', NOW(), '$rev', '$type', '$diff' );";
        mysql_query( $sql );

        return mysql_insert_id();
    }

    function Log_Mail( $username, $comment, $rev, $type, $diff ) {
        global $types;
        
        if ( !preg_match( '#^[a-z0-9-\_]+$#', $username ) ) {
            return;
        }
        if ( !isset( $types[ $type ] ) ) {
            return;
        }

        $text = "Author: $username\nDate: " 
                . date( 'r' ) 
                . "\n" 
                . ucfirst( $types[ $type ] ) 
                . " synced to revision: $rev\nReason: $comment\n\n$diff\n";
        
        mail( "svn@kamibu.com", "[SYNC] " . $types[ $type ] . " - $rev - $comment", $text, "From: $username@kamibu.com" );
    }

    function Log_GetLatest( $limit = 20 ) {
        $limit = ( int )$limit;
        // the two left joins with itself are for retrieving the boolean "rollback" value which determines
        // whether the sync is a rollback -- essentially we're looking for the exact previous sync_id of the
        // same type for each row and compare its sync_rev value with the current row's sync_rev value.
        $sql = "SELECT
                    `sync`.*, previoussync.`sync_rev`>`sync`.sync_rev AS rollback, `users`.*
                FROM
                    `sync` LEFT JOIN `users`
                        ON `sync_userid` = `user_id`
                    LEFT JOIN `sync` AS previoussync
                        ON `sync`.`sync_type`=previoussync.`sync_type`
                        AND `sync`.`sync_id`>previoussync.`sync_id`
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

    function Log_GetLatestByType( $type ) {
        global $types;

        if ( !isset( $types[ $type ] ) ) {
            return;
        }

        $res = mysql_query(
            "SELECT
                *
            FROM
                `sync` LEFT JOIN `users` ON `sync_userid` = `user_id`
            WHERE
                `sync_type`='" . $type . "'
            ORDER BY `sync_id` DESC LIMIT 1;"
        );
        if ( !mysql_num_rows( $res ) ) {
            $row = array( 'sync_revision' => 0 );
        }
        else {
            $row = mysql_fetch_array( $res );
        }
        return $row;
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
