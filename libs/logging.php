<?php
    function Log_Create( $username, $comment, $rev, $diff ) {
        Log_Mail( $username, $comment, $rev, $diff );
        $userid = User_ByName( $username );
        $comment = mysql_real_escape_string( $comment );
        $diff = mysql_real_escape_string( $diff );
        $sql = "INSERT INTO `sync`
                ( `sync_id`, `sync_userid`, `sync_comment`, `sync_created`, `sync_rev`, `sync_diff` ) VALUES
                ( NULL , '$userid', '$comment', NOW(), '$rev', '$diff' );";
        mysql_query( $sql ) or die( mysql_error() );

        return mysql_insert_id();
    }

    function Log_Mail( $username, $comment, $rev, $diff ) {
        if ( !preg_match( '#^[a-z0-9-\_]+$#', $username ) ) {
            return;
        }

        $text = "Author: $username\nDate: " 
                . date( 'r' ) 
                . "\n" 
                . "Synced to revision: $rev\nReason: $comment\n\n$diff\n";
        
        mail( "svn@kamibu.com", "[SYNC] $rev - $comment", $text, "From: $username@kamibu.com" );
    }

    function Log_GetPivot( $syncid, $limit = 10 ) {
        $limit = ( int )$limit;
        $limit += 2; // get two more (one on each side of the pivot) to determine if the last sync in $limit is a rollack by comparing their revision numbers
        $sql = "SELECT
                    `sync`.*, `users`.*
                FROM
                    `sync` LEFT JOIN `users`
                        ON `sync_userid` = `user_id`
                ORDER BY 
                    ABS( " . $syncid . " - `sync`.`sync_id` ) ASC
                LIMIT " . $limit;
        $res = mysql_query( $sql ) or die( mysql_error() );
        $ret = array();
        while ( $row = mysql_fetch_array( $res ) ) {
            $ret[ $row[ 'sync_id' ] ] = $row;
        }
        ksort( $ret ); // in ascending chronological order
        for ( $i = 1; $i < count( $ret ); ++$i ) {
            if ( $ret[ $i ][ 'sync_rev' ] < $ret[ $i - 1 ][ 'sync_rev' ] ) {
                $ret[ $i ][ 'rollback' ] = true;
            }
        }
        --$limit;
        if ( count( $ret ) > $limit ) {
            array_pop( $ret );
            // remove the extra items we don't need
        }
        $ret = array_values( $ret );
        return $ret;
    }

    function Log_GetLatest( $limit = 20, $order = 'DESC' ) {
        $limit = ( int )$limit;
        if ( $order == 'DESC' ) {
            ++$limit; // get one more to determine if the last sync in $limit is a rollack by comparing their revision numbers
        }
        if ( $order != 'ASC' && $order != 'DESC' ) {
            $order = 'DESC';
        }
        $sql = "SELECT
                    `sync`.*, `users`.*
                FROM
                    `sync` LEFT JOIN `users`
                        ON `sync_userid` = `user_id`
                ORDER BY 
                    `sync_id` " . $order . "
                LIMIT " . $limit;
        $res = mysql_query( $sql ) or die( mysql_error() );
        $return = array();
        while( $row = mysql_fetch_array( $res ) ) {
            $return[] = $row;
        }
        for ( $i = 0; $i < count( $return ) - 1; ++$i ) {
            if ( $return[ $i ][ 'sync_rev' ] < $return[ $i + 1 ][ 'sync_rev' ] ) {
                $return[ $i ][ 'rollback' ] = true;
            }
        }
        --$limit;
        if ( count( $return ) > $limit ) {
            if ( $order == 'DESC' ) {
                array_pop( $return ); // remove the extra item we don't need
            }
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
