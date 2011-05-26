<?php

/**
 * @file _app.Users.php
 * @author giorno
 * @package N7
 * @subpackage USers
 * 
 * Common part of Users application implementations.
 */

require_once N7_SOLUTION_LIB . '_app.N7App.php';

abstract class Ai extends N7App
{
	/**
	 * Identifier of application instance.
	 */
	const APP_ID = '_app.Ai';
	
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
			_app_registry::getInstance()->register( static::$instance );
		}

		return static::$instance;
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