<?php

/**
 * @file _app.LoginAjaxImpl.php
 * @author giorno
 *
 * Ajax server implementation for Login application.
 */

require_once CHASSIS_LIB . 'session/_session_wrapper.php';
require_once UNSIGNEDTAB_LIB . '_app.Login.php';

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