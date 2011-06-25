<?php
/**
 * @file _app.Signed.php
 * @author giorno
 *
 * Common ancestor to Signed app implementations. Signed app is general purpose
 * app to provide solution wide features for signed user.
 */

require_once CHASSIS_LIB . 'apps/_app.App.php';
require_once CHASSIS_LIB . 'apps/_app_registry.php';

abstract class Signed extends App
{
	/**
	 * Application identifier.
	 */
	const APP_ID = '_[4lan/7uring/0be/5rs]_';
	
	/**
	 * Instance of class or its descendant.
	 * @var <App>
	 */
	protected static $instance = NULL;

	protected $clock = NULL;

	/**
	 * Common initialization of application instance.
	 */
	protected function __construct ( )
	{
		/**
		 * To make this app name unique and non-conflicting with others.
		 */
		$this->id = self::APP_ID;
	}

	/**
	 * On demand loading of localization and returning reference to it.
	 *
	 * @return <array>
	 */
	public function  getMessages ()
	{
		if ( is_null( $this->messages ) )
		{
			/**
			 * Load messages.
			 */
			$i18n = SIGNEDTAB_ROOT . 'i18n/' . n7_globals::lang( ) . '.php';
			if ( file_exists(  $i18n ) )
				include $i18n;
			else
				include SIGNEDTAB_ROOT . 'i18n/en.php';

			$this->messages = &$__msgSigned;
		}
		
		return parent::getMessages();
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
	 * Fake implementation to conform abstract parent.
	 */
	public function icon ( ) { return null; }

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function event ( $event ) { return null; }
}

?>