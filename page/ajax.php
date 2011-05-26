<?PHP

/**
 * @file ajax.php
 *
 * Ajax dispatcher for whole GTDtab.com. Depending on fragments coming here
 * the proper application is raised to handle the request.
 *
 * @author giorno
 */

require_once '../_init.php';

require_once CHASSIS_LIB . 'session/_session_wrapper.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
error_reporting( E_ERROR);
require_once N7_SOLUTION_LIB . 'libtz.php';

require_once CHASSIS_LIB . 'apps/_app_registry.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
n7_globals::getInstance( );

if ( _session_wrapper::getInstance( )->isSigned( ) === true )
{
	require_once N7_SOLUTION_LIB . 'n7_settings.php';
	require_once N7_SOLUTION_LIB . 'n7_timezone.php';

	/**
	 * Register applications.
	 *
	 * @todo change to implicit registration by going through given app dir
	 *        and call _reg.php in each of them
	 */
	include N7_SOLUTION_APPS . 'signed/_ajx.php';		// must be first to become fallback Ajax interface
	include N7_SOLUTION_APPS . 'ai/_ajx.php';
	include N7_SOLUTION_APPS . 'account/_ajx.php';
	
	$__SMARTY->assign( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );
	$__SMARTY->assign( 'UICMP_TEMPLATES', N7_SOLUTION_ROOT . 'smarty/templates/uicmp' );
}
else
{
	include N7_SOLUTION_APPS . 'unsigned/_ajx.php';
}

_app_registry::getInstance()->exec( $_POST['app'] );

_db_close();

?>