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
error_reporting( E_ALL);
require_once N7_SOLUTION_LIB . 'libtz.php';


require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_at.php';
n7_globals::getInstance( );

include N7_SOLUTION_ROOT . 'i18n/' . n7_globals::lang() . '.php';

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
	/*include N7_SOLUTION_APPS . 'signed/_ajx.php';		// must be first to become fallback Ajax interface
	include N7_SOLUTION_APPS . 'ai/_ajx.php';
	include N7_SOLUTION_APPS . 'account/_ajx.php';*/
	n7_at::run( n7_at::FL_SIGNED | n7_at::FL_AJAXRR, '_ajx.php' );
/*		if ( is_array( $apps ) )
			foreach ( $apps as $app )
				include N7_SOLUTION_APPS . $app[n7_at::F_FSNAME] .'/_ajx.php';*/
	
	$__SMARTY->assign( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );
	$__SMARTY->assign( 'UICMP_TEMPLATES', N7_SOLUTION_ROOT . 'smarty/templates/uicmp' );
}
else
{
	n7_at::run( n7_at::FL_UNSIGNED | n7_at::FL_AJAXRR, '_ajx.php' );
	/*$apps = n7_at::get( n7_at::FL_UNSIGNED | n7_at::FL_AJAXRR );
		if ( is_array( $apps ) )
			foreach ( $apps as $app )
				include N7_SOLUTION_APPS . $app[n7_at::F_FSNAME] .'/_ajx.php';*/
}

_app_registry::getInstance()->exec( $_POST['app'] );

_db_close();

?>