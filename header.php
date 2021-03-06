<?php
    define( 'WEB_ROOT', 'https://code.kamibu.com/sync/' );

    if( !isset( $_SERVER[ 'HTTPS' ] ) ) {
        header( "301 Moved Permanently" );
        header( "Location: " . WEB_ROOT );
        die();
    }

    $xmlmimetype = 'application/xhtml+xml';
    $accepted = explode( ',' , $_server[ 'http_accept' ] );
    if ( in_array( $xmlmimetype , $accepted ) ) {
        header( "content-type: $xmlmimetype; charset=utf-8" );
        echo '<?xml version="1.0" encoding="utf-8"?>';
    }
    else {
        header( "content-type: text/html; charset=utf-8" );
    }

    include "libs/functions.php";
    magicquotes_off();

    ob_start();
    echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Zino Deployment</title>
        <link type="text/css" rel="stylesheet" href="style.css" />
    </head>
    <body>
        <div class="content">
            <div class="username">
            <?php
            echo htmlspecialchars( $_SERVER[ 'REMOTE_USER' ] );
            ?>@kamibu.com
            </div>
            <h1><a href="<?php
            echo WEB_ROOT;
            ?>">Deploy Zino</a></h1>
