<?php

/**
 * @file _app.AiAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_3RD . 'EmailAddressValidator.php';
require_once CHASSIS_LIB . 'list/_list_empty.php';

require_once APP_AI_LIB . '_app.Ai.php';
require_once APP_AI_LIB . 'class.AiApps.php';

/**
 * Ajax server implementation for AI application.
 */
class AiAjaxImpl extends Ai
{
	public function exec ( )
	{
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
		$smarty->assign( 'USR_LIST_CUST_MGR', APP_AI_UI . 'list/list_cust_mgr.html' );
		
		switch ($_POST['action'])
		{
			// Persistence instances.
			case 'pers':
				$this->getPi( )->handle( );
			break;
		
			// Applications Table methods.
			case 'at':
				switch ($_POST['method'])
				{
					// List all applications.
					case 'list':
						$engine = new AiApps( $this );
						$results = $engine->search( $_POST['js_var'] );
						if ( $results !== false )
						{
							$smarty->assignByRef( 'USR_LIST_DATA', $results );
							_smarty_wrapper::getInstance( )->setContent( CHASSIS_UI . '/list/list.html' );
							_smarty_wrapper::getInstance( )->render( );
						}
					break;
					
					// Perform installation of application.
					case 'install':
						$lib = N7_SOLUTION_APPS . $_POST['fsname'] . '/inst/class.Installer.php';
						if ( file_exists( $lib ) )
						{
							require_once $lib;
							$installer = new Installer( );
							$installer->exec( );
							echo "OK";
						}
						else
							echo "KO";
					break;
					
					// Perform upgrade of the application.
					case 'upgrade':
						$lib = N7_SOLUTION_APPS . $_POST['fsname'] . '/inst/class.Installer.php';
						if ( file_exists( $lib ) )
						{
							require_once $lib;
							$installer = new Installer( );
							$installer->upgrade( );
							echo "OK";
						}
						else
							echo "KO";
					break;
					
					// Move app up in execution sequence.
					case 'up':
						if ( n7_at::move( $_POST['id'], -1 ) )
							echo "OK";
						else
							echo "KO";
					break;
				
					// Move app down in execution sequence.
					case 'down':
						if ( n7_at::move( $_POST['id'], 1 ) )
							echo "OK";
						else
							echo "KO";
					break;
				}
			break;
		}
	}
}

?>