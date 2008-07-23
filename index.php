<html>
<head>
<title>Kamibu Sync Services</title>
</head>
<body>
<?php
include( "libs/functions.php" );
if( !isset( $_POST[ 'do' ] )) {
    ?>
    Hey <?php echo $_SERVER['REMOTE_USER'] ?>!<br />
    What do you want to do?
    <p style="color: red;">Take care:<br />Every action on this page is being made directly without a second click on 'yes'. All actions got logged.</p>
    <form method="POST" action="index.php">
        <input type="radio" name="do" value="nothing" checked />Nothing<br />
        <input type="radio" name="do" value="sync" />Sync<br />
        <input type="radio" name="do" value="csssync" />CSS and JS Sync<br />

        <input type="submit" value="Do it" />
    </form>
    <p>Last syncs:<br />
    <?php
    $lastSyncs = getLastSyncs();
    for( $x = 0; $x < count( $lastSyncs ); $x++ ) {
        echo date( "c", $lastSyncs[ $x ][ 'sync_date' ] ) . " " .$lastSyncs[ $x ][ 'user_name' ] . " (" . $lastSyncs[ $x ][ 'sync_rev' ] . " - " . $lastSyncs[ $x ][ 'sync_type' ] . "): " . $lastSyncs[ $x ][ 'sync_comment' ] . "<br />";
    }
    ?>
    </p>
    <?php
} else {
// DO NOTHING
    if( $_POST[ 'do' ] == "nothing" ) {
        ?>Nothing to do? Well, you're really on the safe side, guy!<?php
// DO A CORE SYNC
    } elseif( $_POST[ 'do' ] == "sync" ) {
        ?>
        A Sync is being made:
        <pre><?php
        $revstring = system( "wget -O - http://zeus.blogcube.net/sync/" );
        preg_match( "/revision (?<rev>\w+)./", $revstring, $match );
        logSync( $_SERVER[ 'REMOTE_USER' ], "$revstring", $match[ 'rev' ], "sync" );
        ?></pre>
        <a href="index.php">back</a>
        <?php
    } elseif( $_POST[ 'do' ] == "csssync" ) {
// DO A CSS SYNC
        // get current rev. this will be the rev of the new synced version
        $revstring = exec( "svn info /var/www/zino.gr/beta/phoenix/|grep Revision" );
        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
        logSync( $_SERVER[ 'REMOTE_USER' ], "test comment", $match[ 'rev' ], "csssync" );
        // do the main syncing
        exec( "cat /var/www/zino.gr/static/css/global-beta.css > /var/www/zino.gr/static/css/global.css" );
        exec( "cat /var/www/zino.gr/static/js/global-beta.js > /var/www/zino.gr/static/js/global.js" );
        ?>
        You successfully synced to revision <?php echo $match[ 'rev' ]; ?>.
        <a href="index.php">back</a>
        <?php
    }
}
?>
</body>
</html>
