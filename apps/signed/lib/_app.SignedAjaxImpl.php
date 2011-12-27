<?php

/**
 * @file _app.SignedAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Signed
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once SIGNEDTAB_LIB . '_app.Signed.php';
require_once SIGNEDTAB_LIB . '_wwg.News.php';

/**
 * Ajax hadler for signed user session without specified application, e.g.
 * general solution-wide tasks like settings, etc.
 */
class SignedAjaxImpl extends Signed
{
	/**
	 * Common initialization of application instance.
	 */
	protected function __construct ( )
	{
		parent::__construct( );
	}

	public function exec ( )
	{

		switch ( $_POST['action'] )
		{
			case 'saveSetting':
				n7_globals::settings( )->SaveOne( $_POST['key'], $_POST['val'] );
				n7_globals::settings( )->Load( );
				echo ( ( n7_globals::settings( )->get( $_POST['key'] ) == $_POST['val'] ) ? '1' : '0' ); // send result of operation
			break;

			case 'loadSetting':
				echo n7_globals::settings( )->get( $_POST['key'] ); // send result of operation
			break;

			/**
			 * Perform logout and signal client side to reload page.
			 */
			case '_wwg.LogMeOut:logout':
				if ( \io\creat\chassis\session::getInstance()->logout() )
					echo "OK";
				else
					echo "KO";
			break;

			/*
			 * Provide structured datetime information in XML format. Used
			 * for clock and calendar leaf.
			 */
			case '_wwg.Clock:update':
				/*
				 * Workaround for some strange behaviour. Sometimes server
				 * has sent hour less by 1.
				 */
				/*date_default_timezone_set( n7_globals::settings( )->RealTz( ) );*/
				require_once dirname( __FILE__ ) . '/_wwg.Clock.php';
				$this->clock = new Clock( $this->id );
				echo $this->clock->xml( );

			break;

			/**
			 * Update the RSS cache and return newest headlines.
			 */
			case '_wwg.News:update':
				$news = new io\creat\n7\apps\Signed\News( $this, $this->getMessages( ), true );
			break;
		}
	}
}

?>