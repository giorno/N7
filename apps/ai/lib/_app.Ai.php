<?php

// vim: ts=4

/**
 * @file _app.Users.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once N7_SOLUTION_LIB . '_app.N7App.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';

require_once APP_AI_LIB . 'pers/users.php';

/**
 * Common part of Users application implementations.
 */
abstract class Ai extends N7App
{
	/**
	 * Identifier of application instance.
	 */
	const APP_ID = '_app.Ai';
	
	/**
	 * Persistence instance over table of users.
	 * @var \io\creat\n7\ai\users;
	 */
	protected $pi = NULL;
	
	/**
	 * Instance of class or its descendant.
	 * 
	 * @var <Users>
	 */
	protected static $instance = NULL;
	
	/**
	 * Common initialization of application instance.
	 */
	protected function __construct ( )
	{
		$this->id = self::APP_ID;

		/**
		 * Setting up localization messages.
		 */
		$i18n = APP_AI_I18N . n7_globals::lang( ) . ".php";
		if ( file_exists( $i18n ) )
			include $i18n;
		else
			include APP_AI_I18N . "en.php";

		$this->messages = &$__msg_ai;
	}
	
	/**
	 * Singleton interface.
	 *
	 * @return <App>
	 */
	static public function getInstance ( )
	{
		if ( static::$instance == NULL )
		{
			static::$instance = new static( );
			_app_registry::getInstance( )->register( static::$instance );
		}

		return static::$instance;
	}
	
	/**
	 * Builder of persistence instance over the table of users.
	 * @return \io\creat\n7\ai\users
	 */
	protected function getPi ( )
	{
		if ( is_null( $this->pi ) )
			$this->pi = new \io\creat\n7\ai\users(	n7_ui::getInstance( )->getLayout( ),
													$this->messages['pi_users'],
													n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php',
													array( 'app' => $this->id, 'action' => 'pers' ) );
		
		return $this->pi;
	}

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function icon ( ) { return null; }

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function event ( $event ) { return null; }
}

?>