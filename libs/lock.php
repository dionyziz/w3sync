<?php
    function Lock_Obtain( $reason, $username ) {
        $reason = mysql_real_escape_string( $reason );
        $userid = getUserByName( $username );
        mysql_query(
            "INSERT INTO
                `synclocks`
            SET
                `lock_userid`=$userid,
                `lock_created`=NOW(),
                `lock_active`=1,
                `lock_reason`=$reason;"
        );
    }

    function Lock_Disable( $lockid, $username ) {
        $userid = getUserByName( $username );
        $lockid = ( int )$lockid;

        mysql_query(
            "UPDATE
                `synclocks`
            SET
                `lock_active`=0
            WHERE
                `lock_userid`=" . $userid . "
                AND `lock_id`=" . $lockid . "
            LIMIT 1;"
        );
    }

    function Lock_GetActive() {
        $res = mysql_query(
            "SELECT
                *
            FROM
                `synclocks` CROSS JOIN `users`
                    ON `sync_userid`=`user_id`
            WHERE
                `lock_active`=1;"
        );
        $ret = array();
        while ( $row = mysql_fetch_array( $res ) ) {
            $ret[] = $row;
        }
        return $ret;
    }
?>
