<?PHP

require_once CHASSIS_LIB . "session/settings.php";

/**
 * @file n7_config.php
 * @author giorno
 * @package N7
 *
 * Solution global configuration. Utilizes global scope settings, ergo requires
 * working database connection.
 *
 * @todo rethink about using only the 'ns' field from the table by appending key
 * to solution namespace (e.g. io.creat.n7.server_timezone); that could save one
 * table field
 */
class n7_config extends io\creat\chassis\session\settings
{
	/**
	 * Constructor. Defines instance as abstraction over 'G' scope of settings
	 * table.
	 * @param PDO $pdo data provider
	 */
	public function __construct ( ) { parent::__construct( 'G', N7_SOLUTION_ID, '' ); }
}

?>