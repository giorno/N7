<?php
/**
 * @file _cfg.php
 * @author giorno
 * @subpackage Account
 *
 * Script responsible for setting up My Account application.
 */

define( 'ACCTAB_ROOT',			N7_SOLUTION_APPS . 'account/' );

define( 'ACCTAB_3RD',			ACCTAB_ROOT . '3rd/' );
define( 'ACCTAB_LIB',			ACCTAB_ROOT . 'lib/' );
define( 'ACCTAB_PAGE',			ACCTAB_ROOT . 'page/' );
define( 'ACCTAB_TEMPLATES',		ACCTAB_ROOT . 'templates/' );

//include ACCTAB_ROOT . "i18n/{n7_globals::lang( )}.php";

/**
 * Registering application.
 */
//_app_registry::Register( 'account', $__msgAcc['tabName'], ACCTAB_ROOT, _app_registry::FLAG_DEFAULTICON  );

?>