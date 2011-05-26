<?php

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'libfw.php';

/**
 * @file class.AiUser.php
 * @author giorno
 * @package N7
 * 
 * User details handling routines.
 */

class AiUser extends Config
{
	public static function isRoot ( $uid )
	{
		if ( (int)$uid == 1 )
			return true;
		else
			return false;
	}
	
	public static function toggle ( $uid )
	{
		if ( self::isRoot( $uid ) )
			return false;
		
		_db_query( "UPDATE `" . self::T_USERS . "` SET `" . self::F_ENABLED . "` = NOT(`" . self::F_ENABLED . "`) WHERE `" . self::F_UID . "` = \"" . _db_escape( $uid ) . "\"" );
		
		return true;
	}
	
	/**
	 * Performs user account update or create action. May be used form other
	 * applications, but very carefuly.
	 * 
	 * @param int $uid ID of user account, 0 for creation or new account
	 * @param string $login login for user account, ignored for update
	 * @param string $password password, ignored for update if empty
	 * @param string $email email address
	 * @param bool $enabled indicated whether account should be enabled or not
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
			return true;
		}
		else
		{
			_db_query( "INSERT INTO `" . self::T_USERS . "` SET {$lc}{$pc}{$ec}{$nc}" );
			return ( (int)_db_lastid() > 0 );
		}
	}
	
	public static function exists ( $login )
	{
		return ( (int)_db_1field( "SELECT COUNT(*) FROM `" . self::T_USERS . "` WHERE `" . self::F_LOGIN . "` = \"" . _db_escape( /*trim*/( $login ) ) . "\"" ) > 0 );
	}
	
	/**
	 * Check password value for fulfilment of security requirements.
	 * 
	 * @todo more elaborate check (character classes, etc.)
	 * @todo develop client side asymmetric encryption of password or client
	 * side check and sending already hashed password
	 * 
	 * @param string $pass plain password to check
	 * @return bool 
	 */
	public static function passOk ( $pass )
	{
		return ( strlen( /*trim*/( $pass ) ) >= 4 );
	}
	
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