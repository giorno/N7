<?php
/**
 * @file libtz.php
 * @author giorno
 * @package N7
 *
 * Hacks for timezone. This is not quite recyclable or refactorable library and
 * I am not very proud about myself for writing it.
 */

require_once CHASSIS_LIB . "libdb.php";
require_once CHASSIS_LIB . "libpdo.php";

require_once N7_SOLUTION_LIB . "n7_globals.php";

/**
 * Convert datetimes from server side timezone to client side timezone.
 *
 * @param string $input datetime in format understandable strtotime()
 * @return string timestamp in user timezone
 */
function _tz_transformation ( $input )
{
	n7_globals::serverTz( )->importTzDateTime( $input );
	n7_globals::userTz( )->importStamp( n7_globals::serverTz( )->exportStamp( ) );
	return n7_globals::userTz( )->exportTzStamp( );
}

/**
 * Convert datetimes from client side timezone to server side timezone. This
 * special tweak is used for form values recording.
 *
 * @param string $input datetime in format understandable strtotime()
 * @return string timestamp in server timezone
 */
function _tz_detransformation ( $input )
{
	n7_globals::userTz( )->importTzDateTime( $input );
	n7_globals::serverTz( )->importStamp( n7_globals::userTz( )->exportStamp( ) );
	return n7_globals::serverTz( )->exportTzStamp( );
}

/**
 * Set MySQL connection timezone.
 * @todo remove old DB layer calls
 * @param string $zone timezone identifier
 */
function _tz_setsqltz ( $zone )
{
	_db_query( "SET time_zone = \"" . _db_escape( $zone ) . "\"" );
	n7_globals::getInstance()->get(n7_globals::PDO )->prepare( "SET time_zone = ?" )->execute( array( $zone ) );
}

?>