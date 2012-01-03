<?php

/**
 * @file authbe_ssh2.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7\auth;

require_once CHASSIS_LIB . 'session/authbe.php';
require_once CHASSIS_LIB . 'session/session.php';

/**
 * Authentication against SSH2 service using username and password. It requires
 * remote SSH2 service to support password authentication. Hostname (and port)
 * or IP address are expected to be set in global scope of given settings
 * instance at key 'server.auth.ssh2'. It also requires that PECL SSH2 package
 * is installed.
 */
class authbe_ssh2 extends \io\creat\chassis\authbe
{
	/**
	 * Attempts to sign user.
	 * @param string $login user login name
	 * @param string $password user password
	 * @return mixed
	 */
	public function validate ( $login, $password )
	{
		if ( ( $host = $this->authority( ) ) == NULL )
			return FALSE;
		
		$conn = @ssh2_connect( $host );
		
		// Connection failed.
		if ( !$conn )
			return FALSE;

		// Password is OK and username is not used as root login.
		if ( @ssh2_auth_password( $conn, $login, $password ) )
			if ( ( $uid = (int)$this->mkid( $login ) ) > 1 )
				return $uid;
		
		return FALSE;
	}
	
	/**
	 * Provides authority string (a name of host/domain serving as authority).
	 * @return mixed
	 */
	public function authority ( )
	{
		$host = trim( $this->sett->get( 'server.auth.ssh2' ) );

		// Hostname is not configured in the global scope.
		if ( $host == '' )
			return NULL;
		else
			return $host;
	}
}

// Initialization and binding with globals.
$abe = new authbe_ssh2( \n7_globals::getInstance( )->get( 'config' ) );
\n7_globals::getInstance( )->set( 'server.authbe', $abe );

?>