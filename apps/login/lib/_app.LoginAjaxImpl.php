<?php

/**
 * @file _app.LoginAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once APP_LOGIN_LIB . '_app.Login.php';

/**
 * Ajax server implementation for Login application.
 */
class LoginAjaxImpl extends Login
{
	/**
	 * Attempts login with data from POST request. Returns result for client
	 * logic.
	 */
	public function exec ( )
	{
		$session = \io\creat\chassis\session::getInstance( );
		
		n7_globals::getInstance( )->authbe( );
		
		if ( ( array_key_exists( 'login', $_POST ) && array_key_exists( 'password', $_POST ) && array_key_exists( 'auto', $_POST ) ) && ( $session->login( N7_SOLUTION_ID , $_POST['login'], $_POST['password'], $_POST['auto'] ) ) )
			echo "OK";
		else
			echo "KO";
	}

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function event ( $event ) { return NULL; }
}

?>