<?php

/**
 * @file _wwg.LogMeOut.php
 * @author giorno
 * @package N7
 * @subpackage Signed
 *
 * Widget displaying user's name and Logout anchor.
 */

require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

class LogMeOut extends Wwg
{
	const ID = '_wwg.LogMeOut';

	public function __construct ( )
	{
		$this->id = static::ID;
		$this->template = dirname(__FILE__) . '/../templ/_wwg.LogMeOut.html';
		_app_registry::getInstance( )->requireJs( 'inc/signed/_wwg.LogMeOut.js', $this->id );
		$spacer = new Spacer( );
		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_MENU, $spacer->getId( ), $spacer );
		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_MENU, $this->id, $this );
		_wwg_registry::getInstance( )->setLayout( _wwg_registry::POOL_MENU, Array( n7_ui::getInstance( )->getMenu( )->getId( ), $spacer->getId( ), LogMeOut::ID ) );
		_smarty_wrapper::getInstance()->getEngine( )->assign( 'WWG_LOGMEOUT_USERNAME', _session_wrapper::getInstance()->getNick( ) );
	}
}

?>