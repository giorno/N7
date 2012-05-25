<?PHP

/**
 * @file ajax.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Ajax server dispatcher. It reads app field from Ajax request and triggers
 * proper application server instance to execute the loop.
 */

require_once '../_init.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'libtz.php';
require_once N7_SOLUTION_LIB . 'n7_at.php';
require_once N7_SOLUTION_LIB . 'n7_ajax.php';

n7_globals::getInstance( );

$n7a = new io\creat\n7\n7_ajax( );
n7_globals::getInstance()->set( 'n7_ajax', $n7a );

include N7_SOLUTION_ROOT . 'i18n/' . n7_globals::lang() . '.php';

if ( \io\creat\chassis\session::getInstance( )->isSigned( ) === true )
{	
	n7_at::run( n7_at::FL_SIGNED | n7_at::FL_AJAXRR, '_ajx.php' );
	$__SMARTY->assign( 'N7_URL', n7_globals::getInstance( )->get( 'url' ) );
	$__SMARTY->assign( 'UICMP_TEMPLATES', N7_SOLUTION_ROOT . 'smarty/templates/uicmp' );
}
else
{
	$n7a->setExpired( );
	echo $n7a->getResponse( );
	n7_at::run( n7_at::FL_UNSIGNED | n7_at::FL_AJAXRR, '_ajx.php' );
}


_app_registry::getInstance()->exec( $_POST['app'] );
_db_close();

?>