<?php

/**
 * @file class.AiUser.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'libfw.php';

require_once N7_SOLUTION_LIB . 'class.XmlRpcSrv.php';
/**
 * User record manipulation routines.
 */
class AiUser extends Config
{
	/**
	 * Checks if user with given ID is root (an administrator) or not.
	 * @param int $uid user ID
	 * @return bool
	 */
	public static function isRoot ( $uid )
	{
		if ( (int)$uid == 1 )
			return true;
		else
			return false;
	}
	
	/**
	 * Deletes all sessions for given user ID if their account was disabled.
	 * Does not apply to root user.
	 * @param int $uid 
	 */
	protected static function cancel ( $uid )
	{
		if ( self::isRoot( $uid ) )
			return;
		
		// Delete sessions and autologin tokens for disabled user. This
		// will effectively cut them off.
		$enabled = (int)_db_1field( "SELECT `" . self::F_ENABLED . "` FROM `" . self::T_USERS . "` WHERE `" . self::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );
		if ( $enabled != 1 )
		{
			_db_query( "DELETE FROM `" . self::T_SESSIONS . "` WHERE `" . self::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );
			_db_query( "DELETE FROM `" . self::T_SIGNTOKENS . "` WHERE `" . self::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );
			_db_query( "DELETE FROM `" . XmlRpcSrv::T_RPCSESS . "` WHERE `" . XmlRpcSrv::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );
		}
	}
	
	/**
	 * Toggle ENABLED flag. Must not be performed for root user(s).
	 * @param int $uid user ID
	 * @return bool 
	 */
	public static function toggle ( $uid )
	{
		if ( self::isRoot( $uid ) )
			return false;
		
		_db_query( "UPDATE `" . self::T_USERS . "` SET `" . self::F_ENABLED . "` = NOT(`" . self::F_ENABLED . "`) WHERE `" . self::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );

		self::cancel( $uid );
		
		return true;
	}
	
	/**
	 * Performs user account update or create action. May be used from other
	 * applications as well, but be very careful when doing so.
	 * @param int $uid ID of user account, 0 for creation or new account
	 * @param string $login login for user account, ignored for update
	 * @param string $password password, ignored for update if empty
	 * @param string $email email address
	 * @param bool $enabled indicates whether account should be enabled or not
	 * @return bool
	 */
	public static function save ( $uid, $login, $password, $email, $enabled )
	{
		/**
		 * Password clause.
		 */
		$pc = '';
		if ( ( $password ) != '' )
			$pc = "`" . self::F_PASSWD . "` = \"" . _db_escape ( (_fw_hash_passwd ($password ) ) ) . "\",";
		
		/**
		 * Login clause.
		 */
		$lc = '';
		if ( ( $login ) != '' )
			$lc = "`" . self::F_LOGIN . "` = \"" . _db_escape ( ( $login ) ) . "\",";
		
		$ec = "`" . self::F_EMAIL . "` = \"" . _db_escape ( trim( $email ) ) . "\",";
		$nc = "`" . self::F_ENABLED . "` = \"" . _db_escape ( (int)$enabled ) . "\"";
		$ic = "`" . self::F_UID . "` = \"" . _db_escape ( (int)$uid ) . "\"";
			
		if ( (int)$uid > 0 )
		{
			_db_query( "UPDATE `" . self::T_USERS . "` SET {$pc}{$ec}{$nc} WHERE {$ic}" );
			self::cancel( $uid );
			return true;
		}
		else
		{
			_db_query( "INSERT INTO `" . self::T_USERS . "` SET {$lc}{$pc}{$ec}{$nc}" );
			return ( (int)_db_lastid() > 0 );
		}
	}
	
	/**
	 * Checks if login already exists in the table.
	 * @param string $login user account login
	 * @return bool 
	 */
	public static function exists ( $login )
	{
		return ( (int)_db_1field( "SELECT COUNT(*) FROM `" . self::T_USERS . "` WHERE `" . self::F_LOGIN . "` = \"" . _db_escape( /*trim*/( $login ) ) . "\"" ) > 0 );
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
