<?php

/**
 * @file install.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Script running N7 solution installer.
 */

require_once '../_init.php';

require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
include N7_SOLUTION_ROOT . 'i18n/en.php';

date_default_timezone_set( 'Europe/Brussels' );

/**
 * Ajax server processing.
 */
if ( array_key_exists( 'action', $_POST ) )
{
	include N7_SOLUTION_APPS . 'installer/_ajx.php';
	_app_registry::getInstance()->exec( Installer::ID );
}
else // normal request -> build UI and client logic
{
	include N7_SOLUTION_APPS . 'installer/_idx.php';
	n7_ui::getInstance( )->preRender( );
	_app_registry::getInstance()->exec( Installer::ID );
	_app_registry::getInstance()->render( );
	_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'N7_MSG', $__msg );
	_smarty_wrapper::getInstance( )->render( );
}

?>