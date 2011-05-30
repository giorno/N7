<?PHP

/**
 * @file index.php
 * @author giorno
 * @package N7
 *
 * Main application script for N7.
 */

require_once '../_init.php';

require_once CHASSIS_LIB . 'session/_session_wrapper.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';

error_reporting( E_ALL );

/**
 * Initialize Menhir framework.
 */
require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg.Spacer.php';

$__SMARTY->assign( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );

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
		 *
		 * @todo Change to implicit registration by going through diven app dir
		 *       and call _reg.php in each of them or define order in G
		 *       namespace of tSettings
		 */
		
		include N7_SOLUTION_APPS . 'ai/_idx.php';
		include N7_SOLUTION_APPS . 'account/_idx.php';
		include N7_SOLUTION_APPS . 'branding/_idx.php';
		include N7_SOLUTION_APPS . 'signed/_idx.php';
		_app_registry::getInstance( )->exec( $__APP );

		/**
		 * Enforce layout for BOTTOM pool.
		 */
		$spacer = new Spacer( );
		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_BOTTOM, $spacer->getId( ), $spacer );
		_wwg_registry::getInstance( )->setLayout( _wwg_registry::POOL_BOTTOM, Array( $spacer->getId( ), Clock::ID ) );

		/**
		 * Enforce layout for MENU pool.
		 */
		$spacer = new Spacer( );
		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_MENU, $spacer->getId( ), $spacer );
		_wwg_registry::getInstance( )->setLayout( _wwg_registry::POOL_MENU, Array( n7_ui::getInstance( )->getMenu( )->getId( ), $spacer->getId( ), LogMeOut::ID ) );
	}
	else
	{
		/**
		 * Register applications for unsigned user and trigger proper one.
		 */
		include N7_SOLUTION_APPS . 'unsigned/_idx.php';
		_app_registry::getInstance( )->exec( $__APP );
	}

	_app_registry::getInstance( )->render( );
	_wwg_registry::getInstance( )->render( );	
	_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_MSG', $__msg );
	_smarty_wrapper::getInstance( )->render( );

?>