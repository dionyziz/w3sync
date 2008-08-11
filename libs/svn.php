<?php
    define( 'SVN_ROOT', '/var/www/zino.gr/beta/phoenix/' );

    function SVN_GetCurrentRevision() {
        // get current rev. this will be the rev of the new synced version
        $revstring = exec( "svn info " . SVN_ROOT . "|grep Revision" );
        preg_match( "/Revision: (?<rev>\w+)/", $revstring, $match );
        $revision = $match[ 'rev' ];

        return $revision;
    }

    function SVN_Diff( $oldrevision, $newrevision ) {
        $oldrevision = ( int )$oldrevision;
        $newrevision = ( int )$newrevision;

        assert( $oldrevision > 0 );
        assert( $newrevision > 0 );

        echo( 'svn diff -r ' . $oldrevision . ':' . $newrevision . ' ' . escapeshellarg( SVN_ROOT ) ); // , $output, $ret );

        return implode( "\n", $output );
    }
?>
