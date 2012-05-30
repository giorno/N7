<?php

/**
 * @file _app.Installer.php
 * @author giorno
 * @package N7
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
		try
		{
			$this->pdo = new PDO(	"mysql:host=" . N7_MYSQL_HOST . ";dbname=" . N7_MYSQL_DB,
									N7_MYSQL_USER,
									N7_MYSQL_PASS );
		}
		catch ( PDOException $e )
		{
			// This will be tested for and error will be reported in UI.
			$this->pdo = NULL;
		}
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
