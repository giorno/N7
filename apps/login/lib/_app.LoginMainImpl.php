<?php

/**
 * @file _app.LoginMainImpl.php
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'class.Wa.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'uicmp/layout.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';
require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';

require_once APP_LOGIN_LIB . '_app.Login.php';
require_once APP_LOGIN_LIB . 'uicmp/_uicmp_login_frm.php';

/**
 * Main implementation of Login application. Delivers resources to client side.
 */
class LoginMainImpl extends Login
{
	/**
	 * Localization strings.
	 *
	 * @var array
	 */
	protected $messages = NULL;

	/**
	 * Associative array of languages, indexed by 2-letter codes.
	 *
	 * @var array
	 */
	protected $languages = NULL;

	/**
	 * Common initialization of application instance.
	 */
	protected function __construct ( )
	{
		parent::__construct( );

		/**
		 * Setting up localization messages.
		 */
		include APP_LOGIN_ROOT . "i18n/all.php";

		$this->messages = &$__msgUnsigned;
		$this->languages = &n7_globals::languages( );
	}

	/**
	 * Tries to use login credentials provided by user and perform login
	 * attempt. If it fails, it hands control over to another handler.
	 */
	public function exec ( )
	{
		_smarty_wrapper::getInstance( )->setContent( APP_LOGIN_ROOT . 'ui/index.html' );
		
		$layout = n7_ui::getInstance( )->getLayout( );
		$this->mkUicmp( $layout );
	}
	
	/**
	 * Method creates UICMP component for Login widget. This separated
	 * instantiation is intended for use not only from within this application,
	 * but also for custom public interfaces.
	 * 
	 * @param _uicmp_layout $layout parent instance rendering the form
	 * @return _uicmp_login_frm reference to login form widget
	 */
	public function mkUicmp ( &$layout )
	{
		/**
		 * Build UI.
		 */
		$uicmp = new _uicmp_login_frm( $layout, $this->id . '.Form', n7_globals::getInstance( )->get('url')->myUrl( ) . '/ajax.php', array( 'app' => $this->id, 'action' => 'login' ), $this->messages );
		$uicmp->show( );
		
		//$layout->addUicmp( $uicmp );
		$layout->init( );
		
		/**
		 * Set Smarty resources for the application interface.
		 */
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
			$smarty->assignByRef( 'APP_LOGIN_FORM', $layout );
			$smarty->assignByRef( 'APP_LOGIN_MSG', $this->messages[n7_globals::lang( )] );
			$smarty->assignByRef( 'APP_LOGIN_LANG', n7_globals::lang( ) );
			$smarty->assignByRef( 'APP_LOGIN_LANGUAGES', $this->languages );
			
		return $uicmp;
	}

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function event ( $event ) { return NULL; }
}

?>