<?php

/**
 * @file _appAiAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Ajax server implementation for AI application.
 */

require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once APP_AI_LIB . '_app.Ai.php';
require_once APP_AI_LIB . 'class.AiUsers.php';

class AiAjaxImpl extends Ai
{
	public function exec ( )
	{
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
		$smarty->assign( 'USR_LIST_CUST_MGR', APP_AI_UI . 'list/list_cust_mgr.html' );
		//$smarty->assign( 'APP_STUFF_TEMPLATES', STUFFTAB_TEMPLATES );
		//$smarty->assignByRef( 'APP_STUFF_MSG', $this->messages );
		
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
						//require_once CHASSIS_3RD . 'check_email_address.php';
						
						/**
						 * Check validity of address.
						 */
						$validator = new EmailAddressValidator;
						if ( $validator->check_email_address( $_POST['email'] ) )
					//	if ( check_email_address( $_POST['email'] ) )
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
							
							//$engine = new AiUsers( $this );
							if ( AiUsers::save( $_POST['uid'], $_POST['login'], $_POST['password'], $_POST['email'], $_POST['enabled'] ) )
								echo 'OK';
							else
								echo 'KO';
						}
						else
							echo 'e_address';
						//$engine->
					break;
					
				}
				
			break;
		}
	}
}

?>