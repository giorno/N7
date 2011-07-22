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
require_once APP_AI_LIB . 'class.AiUsers.php';
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
			/**
			 * Search solutions request.
			 */
			case 'search':
				
				switch ($_POST['method'])
				{
					case 'refresh':
						switch ($_POST['id'])
						{
							/**
							 * Search users.
							 */
							case $this->getVcmpSearchId( 'Users'):
								
								$engine = new AiUsers( $this );
								$results = $engine->search( $_POST['ue_js_var'], $_POST['keywords'], $_POST['order'], $_POST['dir'], $_POST['page'] );

								if ( $results !== false )
								{
									$smarty->assignByRef( 'USR_LIST_DATA', $results );
									_smarty_wrapper::getInstance( )->setContent( CHASSIS_UI . '/list/list.html' );
									_smarty_wrapper::getInstance( )->render( );
								}
								else
								{
									$search_id = $this->getVcmpSearchId( 'Users' );
									if ( trim( $_POST['keywords'] ) != '' )
									{
										$empty = new _list_empty( $this->messages['nomatch']['Users'] );
										$empty->add( $this->messages['eo']['again'], "_uicmp_lookup.lookup( '{$search_id}' ).focus();" );
										$empty->add( $this->messages['eo']['allUsers'], "_uicmp_lookup.lookup( '{$search_id}' ).showAll();" );
									}
									else
									{
										/**
										 * This is impossible to happen. If it does, you are probably using too big hammer.
										 */
										$empty = new _list_empty( $this->messages['empty']['Users'] );
										$empty->add( $this->messages['eo']['createUser'], "{$_POST['ue_js_var']}.create();" );
									}
									$empty->render( );
								}
							break;
						}
						
					break;

					/**
					 * List size changed.
					 */
					case 'resize':
						$this->saveSize( (int)$_POST['size'] );
					break;
				}
			break;
		
			/**
			 * User Editor form.
			 */
			case 'ue':
				
				switch ($_POST['method'])
				{
				
					/**
					 * Toggle status flag.
					 */
					case 'toggle':
						$engine = new AiUsers( $this );
						if ( $engine->toggle( $_POST['uid'] ) )
							echo "OK";
						else
							echo "KO";
					break;
					
					/**
					 * Process UE form data.
					 */
					case 'save':
						
						/**
						 * Check validity of address.
						 */
						$validator = new EmailAddressValidator;
						if ( $validator->check_email_address( $_POST['email'] ) )
						{
							/**
							 * Check for correct login.
							 */
							if ( ( (int)$_POST['uid'] == 0 ) && ( !AiUsers::loginOk( $_POST['login'] ) ) )
							{
								echo 'e_login';
								break;
							}
							
							/**
							 * Check duplicity.
							 */
							if ( ( (int)$_POST['uid'] == 0 ) && ( AiUsers::exists( $_POST['login'] ) ) )
							{
								echo 'e_exists';
								break;
							}
							
							/**
							 * Check for password in case of new entry or any password supplied.
							 */
							if ( ( ( trim( $_POST['password']) != '') || ((int)$_POST['uid'] == 0 ) ) && ( !AiUsers::passOk( $_POST['password'] ) ) )
							{
								echo 'e_emptypass';
								break;
							}
							
							if ( AiUsers::save( $_POST['uid'], $_POST['login'], $_POST['password'], $_POST['email'], $_POST['enabled'] ) )
								echo 'OK';
							else
								echo 'KO';
						}
						else
							echo 'e_address';
					break;
				}
				
			break;
			
			/**
			 * Applications Table methods.
			 */
			case 'at':
				
				switch ($_POST['method'])
				{
				
					/**
					 * List all applications.
					 */
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
					
					/**
					 * Perform installation of application.
					 */
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
					
					/**
					 * Move app up in execution sequence.
					 */
					case 'up':
						if ( n7_at::move( $_POST['id'], -1 ) )
							echo "OK";
						else
							echo "KO";
					break;
				
					/**
					 * Move app down in execution sequence.
					 */
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