<?php

/**
 * @file _app.LoginMainImpl.php
 * @author giorno
 *
 * Main implementation of Login application. Delivers resources to client side.
 */

require_once CHASSIS_LIB . 'class.Wa.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_layout.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';
require_once N7_SOLUTION_LIB . 'n7_globals.php';

require_once UNSIGNEDTAB_LIB . '_app.Login.php';
require_once UNSIGNEDTAB_LIB . '_uicmp_login_form.php';

class LoginMainImpl extends Login
{
	/**
	 * Reference to UICMP Layout.
	 *
	 * @var <_uicmp_layout>
	 */
	protected $layout = NULL;

	/**
	 * Localization strings.
	 *
	 * @var <array>
	 */
	protected $messages = NULL;

	/**
	 * Associative array of languages, indexed by 2-letter codes.
	 *
	 * @var <array>
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
		include UNSIGNEDTAB_ROOT . "i18n/all.php";

		$this->messages = &$__msgUnsigned;
		$this->languages = &n7_globals::languages( );
	}

	/**
	 * Tries to use login credentials provided by user and perform login
	 * attempt. If it fails, it hands control over to another handler.
	 */
	public function exec ( )
	{
		_smarty_wrapper::getInstance( )->setContent( UNSIGNEDTAB_ROOT . 'templ/index.html' );

		$this->layout = new _uicmp_layout( n7_requirer::getInstance( ) );
			$tab = new _uicmp_login_form( $this->layout, $this->id . '.Form', n7_globals::getInstance( )->get('url')->myUrl( ) . '/ajax.php', $this->id );
				$tab->show( );
			$this->layout->addUicmp( $tab );

			$this->layout->init( );

		/**
		 * Set Smarty resources for the application interface.
		 */
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
			$smarty->assignByRef( 'APP_LOGIN_FORM', $this->layout );
			$smarty->assignByRef( 'APP_LOGIN_MSG', $this->messages[n7_globals::lang( )] );
			$smarty->assignByRef( 'APP_LOGIN_LANG', n7_globals::lang( ) );
			$smarty->assignByRef( 'APP_LOGIN_LANGUAGES', $this->languages );

		/**
		 * Translate localization into Javascript.
		 */
		_app_registry::getInstance()->requireJsPlain( Wa::JsMessages( $this->messages, '_appLoginMsg' ) );
	}

	/**
	 * Fake implementation to conform abstract parent. It is not used in all
	 * descendants.
	 */
	public function event ( $event ) { return NULL; }
}

?>