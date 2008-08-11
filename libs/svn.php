<?php
    function SVN_GetCurrentRevision() {
        // get current rev. this will be the rev of the new synced version
        $revstring = exec( "svn info /var/www/zino.gr/beta/phoenix/|grep Revision" );
        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
        $revision = $match[ 'rev' ];

        return $revision;
    }

?>
