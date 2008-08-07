<?php
    global $types;

    $types = array(
        'sync' => 'core',
        'csssync' => 'css / js'
    );

    function logSync( $username, $comment, $rev, $type ) {
        global $types;

        if ( !isset( $types[ $type ] ) ) {
            return;
        }
        $userid = getUserByName( $username );
        $comment = mysql_real_escape_string( $comment );
        $sql = "INSERT INTO `sync`
                ( `sync_id`, `sync_userid`, `sync_comment`, `sync_date`, `sync_rev`, `sync_type`) VALUES
                ( NULL , '$userid', '$comment', '" . mktime() . "', '$rev', '$type' );";
        mysql_query( $sql );
        
        mailSync( $username, $comment, $rev, $type );
    }

    function mailSync( $username, $comment, $rev, $type ) {
        global $types;
        
        if ( !preg_match( '#^[a-z0-9-\_]+$#', $username ) ) {
            return;
        }
        if ( !isset( $types[ $type ] ) ) {
            return;
        }

        $text = $username . " synced on " . $types[ $type ] . " to revision " . $rev . "\n\nComment:\n" . $comment;
        
        mail( "$username@kamibu.com", "[SYNC] " . $types[ $type ] . " - $rev - $comment", $text );
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
        while( $row = mysql_fetch_array( $res )) {
            $return[] = $row;
        }
        return $return;
    }
?>
