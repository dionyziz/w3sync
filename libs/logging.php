<?php
    global $types;

    $types = array(
        'sync' => 'core',
        'csssync' => 'css / js'
    );

    function logSync( $username, $comment, $rev, $type, $diff ) {
        global $types;

        mailSync( $username, $comment, $rev, $type, $diff );
        if ( !isset( $types[ $type ] ) ) {
            return;
        }
        $userid = getUserByName( $username );
        $comment = mysql_real_escape_string( $comment );
        $diff = mysql_real_escape_string( $diff );
        $sql = "INSERT INTO `sync`
                ( `sync_id`, `sync_userid`, `sync_comment`, `sync_created`, `sync_rev`, `sync_type`, `sync_diff` ) VALUES
                ( NULL , '$userid', '$comment', '" . mktime() . "', '$rev', '$type', '$diff' );";
        mysql_query( $sql );
    }

    function mailSync( $username, $comment, $rev, $type, $diff ) {
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

    function getLastSyncs() {
        $sql = "SELECT
                    *
                FROM
                    `sync` LEFT JOIN `users`
                        ON `sync_userid` = `user_id`
                ORDER BY 
                    `sync_id` DESC
                LIMIT 20;";
        $res = mysql_query( $sql );
        $return = array();
        while( $row = mysql_fetch_array( $res ) ) {
            $return[] = $row;
        }
        return $return;
    }
?>
