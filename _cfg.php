<?PHP

/**
 * @file _cfg.php
 * @author giorno
 *
 * Main configuration file for the solution.
 */

/**
 * This is used for certain solution and framework instances and also in config
 * (G scope) entries in settings table.
 */
define ( 'N7_SOLUTION_ID',		'io.creat.n7' );

define ( 'N7_FRAMEWORK_ROOT',	'/path/to/Chassis/framework/dir' );

define ( 'N7_MYSQL_HOST',		'MySQL server' );
define ( 'N7_MYSQL_USER',		'MySQL user' );
define ( 'N7_MYSQL_PASS',		'MySQL password' );
define ( 'N7_MYSQL_DB',			'MySQL database' );

/**
 * Paths.
 */
/*define( 'N7_SOLUTION_ROOT', dirname( __FILE__ ) );
define( 'N7_SOLUTION_LIB',	N7_SOLUTION_ROOT . 'lib/' );
define( 'N7_SOLUTION_APPS',	N7_SOLUTION_ROOT . 'apps/' );*/

/**
 * Identifier of application (for common tables, etc.).
 */
//define( 'GTDTAB_APPNAME', 'GTDtab.com' );

/*
 * Halfsize value for pager in lists. See Menhir framework for more.
 */
define( 'PAGE_HALFSIZE', 3 );

/*
 * Server timezone. This is used for transformations, kabala and other
 * occult stuff.
 */
//define( 'TZ_SERVER', 'Europe/Dublin' );

/**
 * Load database configuration connection.
 */
//require_once N7_SOLUTION_ROOT . 'configMySQL.php';

/**
 * Load SMTP mailer configuration.
 */
//require_once N7_SOLUTION_ROOT . 'configSmtp.php';

/**
 * Load Menhir framework configuration.
 */
//require_once N7_SOLUTION_ROOT . 'configMenhir.php';

/**
 * Load LinkProvider configuration.
 */
//require_once N7_SOLUTION_ROOT . 'configLinkProvider.php';

?>