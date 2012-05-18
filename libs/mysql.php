<?php
    $config[ 'mysql_user' ] = "username";
    $config[ 'mysql_password' ] = "password";
    $config[ 'mysql_host' ] = "127.0.0.1";
    $config[ 'mysql_database' ] = "database";

    if( !mysql_connect( $config[ 'mysql_host' ], $config[ 'mysql_user' ], $config[ 'mysql_password' ])) {
        die( "Mysql connection failed" );
    }
    mysql_select_db( $config[ 'mysql_database' ] );
?>
