<?PHP

// vim: ts=4

/**
 * @file index.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Main application script for N7.
 */

require_once '../_init.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';
require_once N7_SOLUTION_LIB . 'n7_at.php';

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

$config = n7_globals::getInstance( )->get( 'config' );

/**
 * Deployment string for client side logic.
 */
if ( !is_null( $magic = $config->get( 'server.magic' ) ) )
	_app_registry::getInstance( )->setMagic( $magic );

// N7 installation/upgrade is required.
if ( $config->get( 'server.version' ) !== N7_SOLUTION_VERSION )
{
	include N7_SOLUTION_APPS . 'installer/_idx.php';
	n7_ui::getInstance( )->preRender( );
	_app_registry::getInstance()->exec( "" );
	_app_registry::getInstance()->render( );
	_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_MSG', $__msg );
	_smarty_wrapper::getInstance( )->render( );
}
else // standard flow
{
	/**
	 * Handle base authentification, authorization and access to applications.
	 */
	if ( \io\creat\chassis\session::getInstance( )->isSigned( ) === true )
	{
		/**
		 * Provide user nickname and domain name in left top corner.
		 */
		if ( ( \io\creat\chassis\session::getInstance()->getUid() != 1) && ( ( $authbe = n7_globals::getInstance( )->authbe( ) ) != NULL ) )
			$host = $authbe->authority( );
		else
			$host = parse_url( n7_globals::getInstance( )->get( 'url' )->myUrl( ), PHP_URL_HOST );
		$whoami = \io\creat\chassis\session::getInstance( )->getNick( ) . '@' . $host;
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'N7_NICKNAME', $whoami );

		n7_ui::getInstance( )->getMenu( )->register(	new MenuItem(	MenuItem::TYPE_TXT,
																		$whoami,
																		NULL ) );

		\_app_registry::getInstance( )->requireJsPlain( 'var n7_signed=true;' );

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
	}
	else
	{
		\_app_registry::getInstance( )->requireJsPlain( 'var n7_signed=false;' );

		/**
		 * Register applications for unsigned user and trigger proper one.
		 */
		n7_at::run( n7_at::FL_UNSIGNED | n7_at::FL_MAINRR );
		_app_registry::getInstance( )->exec( $__APP );
	}

	n7_ui::getInstance( )->preRender( );
	_app_registry::getInstance( )->render( );
	_wwg_registry::getInstance( )->render( );	
	_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_MSG', $__msg );
	_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );
	_smarty_wrapper::getInstance( )->render( );
}

?>