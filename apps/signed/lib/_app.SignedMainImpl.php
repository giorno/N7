<?php

/**
 * @file _app.SignedMainImpl.php
 * @author giorno
 *
 * Class responsible for delivering HTML content for signed user in all apps.
 */

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

require_once SIGNEDTAB_LIB . '_app.Signed.php';
require_once SIGNEDTAB_LIB . '_wwg.Clock.php';
require_once SIGNEDTAB_LIB . '_wwg.LogMeOut.php';

class SignedMainImpl extends Signed
{
	protected $logMeOut = NULL;
	//protected $messages = NULL;

	protected function __construct ( )
	{
		parent::__construct( );
		
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'APP_SIGNED_MSG', $this->getMessages( ) );		
	}

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
		}
	}
	
	public function exec ( )
	{
		
	}
}

?>