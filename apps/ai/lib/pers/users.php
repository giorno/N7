<?php

// vim: ts=4

/**
 * @file users.php
 * @author jstanik
 * @package N7
 * @subpackage AI
 * @copyright Openet Telecom Ltd.
 */

namespace io\creat\n7\ai;

require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once CHASSIS_LIB . 'pers/instance.php';
require_once CHASSIS_LIB . 'pers/settproxy.php';
require_once CHASSIS_LIB . 'list/_list_cell.php';
require_once CHASSIS_LIB . 'libpdo.php';

require_once N7_SOLUTION_LIB . 'class.XmlRpcSrv.php';

require_once APP_AI_LIB . 'class.AiCfgFactory.php';
require_once APP_AI_LIB . 'class.AiListCell.php';

/**
 * Persistence instance over the table of users.
 */
class users extends \io\creat\chassis\pers\instance
{
	// Artificial field for icon to disable/enable account in the list.
	const FN_DISABLE	= '__dis__';
	
	/**
	 * Constructor. Initializes Persistence instance for table of users.
	 * @param \io\creat\chassis\uicmp\layout $layout  reference to UICMP layout instance to render UI in
	 * @param array $messages custome (non-framework) localization messages
	 * @param string $url Ajax server URL
	 * @param array $params Ajax request parameters, provided by app to identify Ajax channel
	 */
	public function __construct ( &$layout, $messages, $url, $params )
	{
		parent::__construct(	\Config::T_USERS,
								\pers::FL_PI_RUI | \pers::FL_PI_TUI | \pers::FL_PI_RESIZE | \pers::FL_PI_ANCHORS,
								$layout,
								$messages,
								$url,
								$params,
								new \io\creat\chassis\pers\settproxy( \n7_globals::settings( ), \AiCfgFactory::getInstance( ), 'usr.lst.len', 'usr.lst.pagerhalf' )
							);
		
		// Honour the authentication backend configuration.
		$authbe = \n7_globals::getInstance()->authbe( );
		if ( ( is_null( $authbe ) ) || ( $authbe->hasFlag( \io\creat\chassis\authbe::ABE_MODPASSWD ) ) )
			$this->flags |= \pers::FL_PI_CREATE;
	}
	
	/**
	 * Overrides superclass row item to add special handling of custom fields.
	 * @param \io\creat\chassis\pers\field $field configuration of the field
	 * @param array $record database fields for particular record
	 * @param array $search reference to parsed search query
	 */
	protected function listri ( &$field, &$record, &$search )
	{
		// Is root account?
		if ( (int)$record[\Config::F_UID] == 1 )
		{
			if ( $field->name == \Config::F_LOGIN )
				return new \_list_cell( \_list_cell::Text( $record[$field->name], '', $field->align ) );
			
			// Dummy cell.
			if ( $field->name == self::FN_DISABLE )
				return new \_list_cell( \_list_cell::Text( '', '', 'left' ) );
		}
		else
		{
			if ( $field->name == self::FN_DISABLE )
			{
				if ( $record[\Config::F_ENABLED] == '1' )
					return new \_list_cell(	\_list_cell::Code(	$search['jsvar'] . '.tui.iclick( \'' . self::FN_DISABLE . '\', ' . $record[\Config::F_UID] . ' )', $this->messages['disable'] ), \AiListCell::MAN_AI_USR_E );
				else
					return new \_list_cell(	\_list_cell::Code(	$search['jsvar'] . '.tui.iclick( \'' . self::FN_DISABLE . '\', ' . $record[\Config::F_UID] . ' )', $this->messages['enable'] ), \AiListCell::MAN_AI_USR_D );
			}
		}
	
		// Fallback to superclass implementation.
		return parent::listri( $field, $record, $search );		
	}
	
	/**
	 * Adds special logic for Last Access field, which is foreign to this table.
	 * @todo would it be beneficial to add timezone conversion, use custom datetime format and/or remove data part from today's (perhaps in upstream logic)?
	 * @param array $search parsed search query, output of searchp()
	 * @return string
	 */
	protected function searchq ( &$search )
	{
		$and = $or = $where = $extraLp = $extraRp = '';
		
		foreach ( $this->fields as $field )
			parent::fieldq( $search, $field, $and, $or );
		
		if ( is_array( $or ) )
			$and[] = ' ( ' . implode( ' OR ', $or ) . ' ) ';
		if ( is_array( $and ) )
			$where = 'WHERE ' . implode( ' AND ', $and );

		// we would need to add extra level of SELECT nesting to avoid confusing
		// data output (such as invalid record count)
		return "FROM  ( SELECT * FROM (	SELECT `{$this->table}`.*,`" . \Config::T_LOGINS . "`.`" . \Config::F_STAMP . "` as " . \Config::F_STAMP . " FROM `{$this->table}`
									LEFT JOIN `" . \Config::T_LOGINS . "`
										ON ( `" . $this->table . "`.`" . \Config::F_UID . "` = `" . \Config::T_LOGINS . "`.`" . \Config::F_UID . "` )
									{$where}
									ORDER BY `" . \Config::T_LOGINS . "`.`" . \Config::F_STAMP. "` DESC ) `{$this->table}`
								GROUP BY `" . $this->table . "`.`" . \Config::F_UID. "`  ) grouped";
	}
	
	/**
	 * Deletes all sessions associated with user account.
	 * @param int $uid user ID
	 */
	private function cancel ( $uid )
	{
		$params = array( $uid );
		if ( (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT `" . \Config::F_ENABLED . "`
													FROM `" . \Config::T_USERS . "`
													WHERE `" . \Config::F_UID . "` = ?" ), $params ) != 1 )
		{
			$this->pdo->prepare( "DELETE FROM `" . \Config::T_SESSIONS . "`
									WHERE `" . \Config::F_UID . "` = ?" )->execute( $params );
			$this->pdo->prepare( "DELETE FROM `" . \Config::T_SIGNTOKENS . "`
									WHERE `" . \Config::F_UID . "` = ?" )->execute( $params );
			$this->pdo->prepare( "DELETE FROM `" . \XmlRpcSrv::T_RPCSESS . "`
									WHERE `" . \XmlRpcSrv::F_UID . "` = ?" )->execute( $params );
		}
	}
	
	/**
	 * Overrides superclass behaviour. Writes directly to the output buffer and
	 * superclass is supposed to respect this and write no further. Output
	 * written in this method corresponds to RUI indicator messages
	 * localization.
	 * @param array $index index data sent by RUI
	 * @param array $fields values extracted from RUI sent XML.
	 * @return bool
	 */
	protected function validate( &$index, &$fields )
	{
		$validator = new \EmailAddressValidator;
		if ( $validator->check_email_address( $fields[\Config::F_EMAIL] ) )
		{
			// Check for correct login.
			if ( ( (int)$index[0] == 0 ) && ( !self::loginOk( $fields[\Config::F_LOGIN] ) ) )
			{
				echo 'e_login';
				return FALSE;
			}
			
			// Check duplicity.
			$uid = (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT `" . \Config::F_UID . "`
										FROM `" . \Config::T_USERS . "`
										WHERE `" . \Config::F_LOGIN . "` = ?" ), array( $fields[\Config::F_LOGIN] ) );
			if ( ( $uid != 0 ) && ( $uid != (int)$index[0] ) )
			{
				echo 'e_exists';
				return FALSE;
			}
							
			if ( $this->has( \pers::FL_PI_CREATE ) )
			{
				// Check for password in case of new entry or any password supplied.
				if ( ( ( trim( $fields[\Config::F_PASSWD] ) != '' ) || ( (int)$index[0] == 0 ) ) && ( !self::passOk( $fields[\Config::F_PASSWD] ) ) )
				{
					echo 'e_emptypass';
					return FALSE;
				}
				else
					$fields[\Config::F_PASSWD] = \_fw_hash_passwd( $fields[\Config::F_PASSWD] );
			}
			
			return TRUE;
		}
		else
			echo 'e_address';

		return FALSE;
	}
	
	/**
	 * Apply extra step to delete account sessions.
	 */
	protected function save ( )
	{
		if ( parent::save( ) )
		{
			// Should be only single literal.
			$this->cancel( (int)$_POST['index'] );
		
			return true;
		}
		
		return false;
	}
	
	/**
	 * Overrides superclass implementation to implement account enable/disable
	 * feature.
	 */
	public function handle()
	{
		if ( $_POST['primitive'] == 'tui' )
		{
			switch ( $_POST['method'] )
			{				
				// Extract UID from the payload and apply the operation.
				case 'iclick':
					$uid = (int)$_POST['data'];
					
					// No invalid (0), no root (1)
					if ( ( $uid > 1 ) && ( $_POST['icon'] == self::FN_DISABLE ) )
					{
						$this->pdo->prepare( "UPDATE `" . \Config::T_USERS . "`
							SET `" . \Config::F_ENABLED . "` = NOT(`" . \Config::F_ENABLED . "`)
							WHERE `" . \Config::F_UID . "` = ?" )->execute( array( $uid ) );
						
						$this->cancel( $uid );
						
						echo "OK";
					}
					else
						echo "e_unknown";
					
					return;
				break;
			}
		}
		
		return parent::handle( );
	}
	
	/**
	 * Explicitly changing order and parameters of table fields. Disable
	 * superclass implicit extraction of fields and leaves explicit empty as
	 * its body is actually executed here.
	 */
	protected function implicit ( )
	{	
		$this->fields[\Config::F_UID]
			= new \io\creat\chassis\pers\field( \Config::F_UID,
												\pers::FT_INT,
												\pers::FL_FD_AUTO | \pers::FL_FD_VIEW | \pers::FL_FD_ORDER,
												$this->messages['uid'],
												'',
												\AiCfgFactory::LIST_HDRW_UID );

		$this->fields[\Config::F_LOGIN]
			= new \io\creat\chassis\pers\field( \Config::F_LOGIN,
												\pers::FT_STRING,
												\pers::FL_FD_MODIFY | \pers::FL_FD_ANCHOR | \pers::FL_FD_VIEW | \pers::FL_FD_PREVIEW | \pers::FL_FD_SEARCH | \pers::FL_FD_ORDER,
												$this->messages['login'],
												'',
												\AiCfgFactory::LIST_HDRW_LOGIN );
		
		$this->fields[\Config::F_STAMP]
			= new \io\creat\chassis\pers\field( \Config::F_STAMP,
												\pers::FT_DATETIME,
												\pers::FL_FD_ORDER | \pers::FL_FD_VIEW | \pers::FL_FD_HIDDEN,
												$this->messages['last'],
												'',
												\AiCfgFactory::LIST_HDRW_LAST );
		
		if ( $this->has( \pers::FL_PI_CREATE ) )
			$this->fields[\Config::F_PASSWD]
				= new \io\creat\chassis\pers\field( \Config::F_PASSWD,
													\pers::FT_PASSWORD,
													\pers::FL_FD_MODIFY,
													$this->messages['password'],
													'' );
		
		$this->fields[\Config::F_EMAIL]
			= new \io\creat\chassis\pers\field( \Config::F_EMAIL,
												\pers::FT_STRING,
												\pers::FL_FD_MODIFY | \pers::FL_FD_VIEW | \pers::FL_FD_SEARCH | \pers::FL_FD_ORDER,
												$this->messages['email'],
												'',
												\AiCfgFactory::LIST_HDRW_ADDRESS );
		
		$this->fields[\Config::F_ENABLED]
			= new \io\creat\chassis\pers\field(	\Config::F_ENABLED,
												\pers::FT_BOOL,
												0,
												$this->messages['enabled'] );
		
		$this->fields[self::FN_DISABLE] = new \io\creat\chassis\pers\field(	self::FN_DISABLE,
																			\pers::FT_ICON,
																			\pers::FL_FD_VIEW | \pers::FL_FD_HIDDEN,
																			'', '', '16px' );
				
		$this->index[] = \Config::F_UID;
	}
	
	/**
	 * Check password value for fulfilment of security requirements.
	 * @todo more elaborate check (character classes, etc.)
	 * @todo develop client side asymmetric encryption of password or client
	 * side check and sending already hashed password
	 * @param string $pass plain password to check
	 * @return bool 
	 */
	public static function passOk ( $pass )
	{
		return ( strlen( ( $pass ) ) >= 4 );
	}
	
	/**
	 * Checks if login value has valid format.
	 * @param string $login user account login
	 * @return bool 
	 * @todo allow use of certain non-alphanum characters, like dots, dashes, etc.
	 */
	public static function loginOk ( $login )
	{
		if ( trim( $login ) == '' )
			return false;
		
		for ( $i = 0; $i < strlen( $login ); ++$i )
		{
			if ( ( ( ( ord( $login[$i] ) >= 65 ) && ( ord( $login[$i] ) <= 90 ) ) ||	// A..Z
				( ( ord( $login[$i] ) >= 97 ) && ( ord( $login[$i] ) <= 122 ) ) ) ||	// a..z
				( ( ord( $login[$i] ) >= 48 ) && ( ord( $login[$i] ) <= 57 ) ) )		// 0..9
				continue;
			else
				return false;
		}
		
		return true;
	}
}

?>