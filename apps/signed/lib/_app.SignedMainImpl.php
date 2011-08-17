<?php

/**
 * @file _app.SignedMainImpl.php
 * @author giorno
 * @package N7
 * @subpackage Signed
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

require_once SIGNEDTAB_LIB . '_app.Signed.php';
require_once SIGNEDTAB_LIB . '_wwg.Clock.php';
require_once SIGNEDTAB_LIB . '_wwg.LogMeOut.php';
require_once SIGNEDTAB_LIB . '_wwg.News.php';

/**
 * Class responsible for delivering HTML content for signed user in all apps.
 */
class SignedMainImpl extends Signed
{
	protected $logMeOut = NULL;

	protected function __construct ( )
	{
		parent::__construct( );
		
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'APP_SIGNED_UI', SIGNEDTAB_ROOT . 'templ/' );
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'APP_SIGNED_MSG', $this->getMessages( ) );
	}

	/**
	 * Handler of application registry events.
	 * 
	 * @param int $event event code
	 */
	public function event ( $event )
	{
		if ( $event | _app_registry::EV_REGISTERED )
		{
			/**
			 * Registers objects which are supposed to be available in all apps when
			 * user is signed in.
			 */
			$this->clock = new Clock( $this->id );
			$this->logMeOut = new LogMeOut( $this->id );
			$news = new io\creat\n7\apps\Signed\News( $this, $this->getMessages( ) );
		}
	}
	
	/**
	 * This app does not have actual execution body in main Request-Response.
	 */
	public function exec ( ) { }
}

?>