<?php

// vim: ts=4

/**
 * @file _app.Installer.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_app.App.php';
require_once CHASSIS_LIB . 'apps/_app_registry.php';

/** 
 * Common part of installer application. Contains functionality shared by all
 * its implementations. Singleton.
 * 
 * Installer instances owned by Installer application are used only for
 * installation of solution and its core application through main install.php
 * script, they are not involved in installation of any other applications. To
 * install applications please use Installer class in N7 libraries folder.
 */
abstract class Installer extends App
{
	// Identifier of application instances.
	const ID = '_app.Installer';
	
	/**
	 * PDO instance used for installation/upgrade.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Singleton instance.
	 * @var Installer 
	 */
	protected static $instance = NULL;
	
	protected function __construct ( )
	{
		$this->id = static::ID;
		_app_registry::getInstance()->setDefault( $this->id );
		$this->pdo = n7_globals::getInstance()->get( n7_globals::PDO );		
	}
	
	/**
	 * Singleton interface.
	 * @return Installer 
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
