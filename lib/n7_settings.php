<?PHP

require_once CHASSIS_LIB . "session/settings.php";
require_once CHASSIS_LIB . 'session/session.php';

/**
 * @file n7_settings.php
 * @author giorno
 * @package N7
 *
 * Solution settings implementation. This is supposed to cover user/session
 * scope settings. For global settings there is a separate global instance
 * created in n7_globals.
 */
class n7_settings extends io\creat\chassis\session\settings
{
	/**
	 * Constructor. Defines instance as abstraction over 'U' scope of settings
	 * table.
	 */
	public function __construct( ) { parent::__construct( 'U', N7_SOLUTION_ID, \io\creat\chassis\session::getInstance( )->getUid( ) ); }
}

?>