<?php

/**
 * @file _app.Login.php
 * @author giorno
 *
 * Singleton interface and basic functionality for Login application
 * implementations.
 */

require_once CHASSIS_LIB . 'apps/_app.App.php';
require_once CHASSIS_LIB . 'apps/_app_registry.php';

abstract class Login extends App
{
	/**
	 * Singleton instance.
	 *
	 * @var <Login>
	 */
	static protected $instance = NULL;

	/**
	 * Common initialization of application instance.
	 */
	protected function __construct ( )
	{
		$this->id = '_app.Login';
		_app_registry::getInstance()->setDefault( $this->id );
	}

	/**
	 * Singleton interface.
	 *
	 * @return <Login>
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
	 * Fake implementation to conform abstract parent. It will be not used in
	 * implementations.
	 */
	public function icon ( ) { }
}

?>