<?PHP

/**
 * @file index.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Main application script for N7.
 */

require_once '../_init.php';

require_once CHASSIS_LIB . 'session/_session_wrapper.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';
require_once N7_SOLUTION_LIB . 'n7_at.php';

error_reporting( E_ALL );

/**
 * Initialize Menhir framework.
 */
require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg.Spacer.php';

/**
 * Extract action from request data.
 */
if ( is_array( $_POST ) && array_key_exists( 'app', $_POST ) )
	$__APP = $_POST['app'];
elseif ( is_array( $_GET ) && array_key_exists( 'app', $_GET ) )
	$__APP = $_GET['app'];
else
	$__APP = 'default';

include N7_SOLUTION_ROOT . 'i18n/' . n7_globals::lang() . '.php';

/**
 * Handle base authentification, authorization and access to applications.
 */
if ( _session_wrapper::getInstance( )->isSigned( ) === true )
{
	/**
	 * Provide user nickname and domain name in left top corner.
	 */
	$host = parse_url( n7_globals::getInstance( )->get( 'url' )->myUrl( ), PHP_URL_HOST );
	_smarty_wrapper::getInstance( )->getEngine( )->assign( 'N7_NICKNAME', _session_wrapper::getInstance( )->getNick( ) . '@' . $host );
		
	/**
	 * Register applications.
	 */
	n7_at::run( n7_at::FL_SIGNED | n7_at::FL_MAINRR );
	_app_registry::getInstance( )->exec( $__APP );

	/**
	 * Enforce layout for BOTTOM pool.
	 */
	$spacer = new Spacer( );
	_wwg_registry::getInstance( )->register( _wwg_registry::POOL_BOTTOM, $spacer->getId( ), $spacer );
	//_wwg_registry::getInstance( )->setLayout( _wwg_registry::POOL_BOTTOM, Array( Clock::ID, $spacer->getId( ) ) );
}
else
{
	/**
	 * Register applications for unsigned user and trigger proper one.
	 */
	n7_at::run( n7_at::FL_UNSIGNED | n7_at::FL_MAINRR );
	_app_registry::getInstance( )->exec( $__APP );
}

_app_registry::getInstance( )->render( );
_wwg_registry::getInstance( )->render( );	
_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_MSG', $__msg );
_smarty_wrapper::getInstance( )->getEngine( )->assign( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );
_smarty_wrapper::getInstance( )->render( );

?>