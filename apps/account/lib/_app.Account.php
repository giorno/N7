<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once CHASSIS_LIB . 'apps/_app.App.php';

abstract class Account extends App
{
	const APP_ID = '_app.Account';

	/**
	 * Instance of class or its descendant.
	 * @var <App>
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
		include ACCTAB_ROOT . "i18n/" . n7_globals::lang( ) . ".php";
		$this->messages = &$__msgAcc;
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
	 * Providing structured information used later to render application icon.
	 */
	public function icon ( )
	{
		return Array( 'id' => $this->id,	// shoud be consistent with what is passed to _uicmp_stuff_fold class
					  'title' => $this->messages['tabName'] );
	}
}

?>