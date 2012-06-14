<?php

// vim: ts=4

/**
 * @file _app.Upgrader.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_app.App.php';
require_once CHASSIS_LIB . 'apps/_app_registry.php';

/** 
 * Common logic of upgrade application. Used to update database for new
 * version of N7 and core applications.
 */
abstract class Upgrader extends App
{
	// Identifier of application instances.
	const ID = '_app.Upgrader';
	
	/**
	 * PDO instance used for installation/upgrade.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Singleton instance.
	 * @var Upgrader 
	 */
	protected static $instance = NULL;
	
	/**
	 * Constructor. Ensures that our application is the first application.
	 */
	protected function __construct ( )
	{
		$this->id = static::ID;
		_app_registry::getInstance( )->setDefault( $this->id );
		$this->pdo = n7_globals::getInstance()->get( n7_globals::PDO );		
	}
	
	/**
	 * Singleton interface.
	 * @return Upgrader
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
		{
			static::$instance = new static ( );
			_app_registry::getInstance( )->register( static::$instance );
		}
		
		return static::$instance;
	}
	
	public function icon ( ) { }
	
	public function event ( $event ) { }
}

?>
