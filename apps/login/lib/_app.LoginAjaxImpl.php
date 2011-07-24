<?php

/**
 * @file _app.LoginAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'session/_session_wrapper.php';
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
		$session = _session_wrapper::getInstance( );
		
		if ( $session->login( N7_SOLUTION_ID , $_POST['login'], $_POST['password'], $_POST['rememberMe'] ) )
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