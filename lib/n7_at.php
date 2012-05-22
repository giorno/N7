<?php

// vim: ts=4

/**
 * @file n7_at.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_CFG . 'class.Config.php';

/**
 * Library class handling manipulation with Applications Table (AT).
 */
class n7_at
{
	
	/**
	 * Database table name.
	 */
	const T_APPS		= 'n7_at';
	
	/**
	 * DB field. Solution identifier. Has value of N7_SOLUTION_ID constant.
	 */
	const F_NS			= Config::F_NS;
	
	/**
	 * DB field. Identifier of application. Has value of application APP_ID
	 * member.
	 */
	const F_APPID		= 'app_id';
	
	/**
	 * DB field. Folder where application is installed.
	 */
	const F_FSNAME		= 'fs_name';
	
	/**
	 * DB field. Version of the application.
	 */
	const F_VERSION		= 'version';
	
	/**
	 * DB field. Installation sequence.
	 */
	const F_INSTSEQ		= 'inst_seq';
	
	/**
	 * DB field. Execution sequence.
	 */
	const F_EXECSEQ		= 'exec_seq';
	
	/**
	 * DB field. Localized app names.
	 */
	const F_I18N		= 'i18n';
	
	/**
	 * DB field. Automatic timestamp of record creation.
	 */
	const F_STAMP		= Config::F_STAMP;
	
	/**
	 * DB field. Flags.
	 */
	const F_FLAGS		= 'flags';
	
	/**
	 * Application will be executed for unsigned users.
	 */
	const FL_UNSIGNED	= 1;
	
	/**
	 * Application will be executed for signed users.
	 */
	const FL_SIGNED		= 2;
	
	/**
	 * Application will be executed in main Request-Response.
	 */
	const FL_MAINRR		= 4;
	
	/**
	 * Application will be executed in Ajax Request-Response.
	 */
	const FL_AJAXRR		= 8;
	
	/**
	 * Application has primitives to handle XML RPC calls.
	 */
	const FL_XMLRPC		= 16;
	
	/** Value for execution sequence marking app as cadidate. */
	const V_CANDIDATE	= -1;
	
	/** Value for execution sequence marking app as in conflict (version
	 * mismatch -> upgrade/downgrade). */
	const V_CONFLICT	= -2;
	
	/**
	 * Provides array of applications matching given flags.
	 * 
	 * @param int $flags flags to filter applications
	 * @return mixed 
	 */
	public static function get ( $flags )
	{
		$ret = FALSE;
		$apps = _db_query( "SELECT * FROM `" . self::T_APPS . "`
								WHERE `" . self::F_FLAGS . "` & " . (string)(int)$flags . " = " . (string)(int)$flags . "
								GROUP BY `" . self::F_APPID . "`,`" . self::F_INSTSEQ . "`
								DESC ORDER BY `" . self::F_EXECSEQ . "`" );
		
		if ( $apps && _db_rowcount( $apps ) )
		{
			while ( $app = _db_fetchrow( $apps ) )
			{
				$man = self::man( N7_SOLUTION_APPS . '/' . $app[self::F_FSNAME] );
				
				// Record in the table matches sources.
				//if ( $man[self::F_VERSION] == $app[self::F_VERSION] )
					$ret[$app[self::F_APPID]] = $app;
			}
		}
		
		return $ret;
	}
	
	/**
	 * Include file from application folder for each application matching flags.
	 * 
	 * @param int $flags flags to filter applications
	 * @param string $script path to script relative to the application folder
	 */
	public static function run ( $flags, $script = '_idx.php')
	{
		$apps = self::get( $flags );
		if ( is_array( $apps ) )
			foreach ( $apps as $app )
			{
				$path = N7_SOLUTION_APPS . $app[n7_at::F_FSNAME] .'/' . $script;
				if ( file_exists( $path ) )
					include $path;
			}
	}
	
	/**
	 * Imports manifest script for application given by path and returns it as
	 * an array.
	 * 
	 * @param string $path 
	 * @return array
	 */
	public static function man ( $path )
	{
		$man = NULL;
		
		/**
		 * This file should define $man variable, an associative array
		 * containing following keys: 'id', 'version', 'flags' and 'i18n'. Index
		 * 'i18n' is optional and contains array of localized application names,
		 * indexed by two-character language codes.
		 */
		$file = $path . '/_man.php';
		if ( file_exists( $file ) )
			include $file;
		
		return $man;
	}
	
	/**
	 * Creates list of applications registered in the system and candidates for
	 * installation.
	 * @return mixed
	 */
	public static function search ( )
	{
		$app = NULL;
		
		/**
		 * List installed applications.
		 */
		$res = _db_query( "SELECT * FROM
							( SELECT * FROM `" . self::T_APPS . "`
								GROUP BY `" . self::F_APPID . "`, `" . self::F_INSTSEQ . "` DESC
								ORDER BY `" . self::F_EXECSEQ . "` ASC, `" . self::F_INSTSEQ . "` DESC ) t1
							GROUP BY `" . self::F_APPID . "`" );
		if ( $res && _db_rowcount( $res ) )
			while ( $app = _db_fetchrow( $res ) )
				$apps[] = $app;
		
		/**
		 * Scan for candidates in apps directory.
		 */
		$dh = opendir( N7_SOLUTION_APPS );
		if ( $dh )
		{
			while ( ( $cand = readdir( $dh ) ) !== false )
			{
				if ( ( $cand == '.' ) || ( $cand == '..' ) )
					continue;
				
				$match = FALSE;
				$man = self::man( N7_SOLUTION_APPS . '/' . $cand );
				
				if ( is_array( $apps ) )
					foreach ( $apps as $seq => $app )
						if ( array_key_exists( self::F_FSNAME, $app) && ( $app[self::F_FSNAME] == $cand ) )
						{
							// Version mismatch (upgrade/downgrade is due).
							if ( is_array( $man ) && ( $man['version'] != $app[self::F_VERSION] ) )
							{
								$app[self::F_APPID] = $man['id'];
								$app[self::F_EXECSEQ] = self::V_CONFLICT;
								$app[self::F_FLAGS] = $man['flags'];
								$app[self::F_I18N] = serialize( $man['i18n'] );
								$app['man'] = $man;
								$apps[$seq] = $app;
							}
							
							$match = TRUE;
							break;
						}
						
				if ( !$match )
				{
					if ( !is_array( $man ) )
						continue;
					
					$apps[] = array(	self::F_APPID	=> $man['id'],
										self::F_EXECSEQ	=> self::V_CANDIDATE,
										self::F_FLAGS	=> $man['flags'],
										self::F_FSNAME	=> $cand,
										self::F_I18N	=> serialize( $man['i18n'] ),
										self::F_VERSION	=> $man['version'],
										self::F_NS		=> N7_SOLUTION_ID );
				}
			}
		}
		
		return $apps;
	}
	
	/**
	 * Registers new application or its new version into the table.
	 * 
	 * @todo optimize query to compute inst_seq value internally
	 * 
	 * @param string $id indetifier of application, value of APP_ID member
	 * @param string $dir name of application folder in solution apps folder
	 * @param string $version version of the application
	 * @param string $i18n serialized localizations of app names
	 * @param int $flags flags of the application
	 */
	public static function register ( $id, $dir, $version, $i18n, $flags = 0 )
	{
		$latest = _db_1line( "SELECT *
								FROM `" . self::T_APPS . "`
								WHERE `" . self::F_NS . "` = \"" . _db_escape( N7_SOLUTION_ID ) . "\"
									AND `" . self::F_APPID . "` = \"" . _db_escape( $id ) . "\"
								ORDER BY `" . self::F_INSTSEQ . "` DESC
								LIMIT 0,1" );
		
		if ( is_array( $latest ) )
		{
			$inst_seq = $latest[self::F_INSTSEQ] + 1;
			$exec_seq = $latest[self::F_EXECSEQ];
		}
		else
		{
			$inst_seq = 0;
			$exec_seq = _db_1field( "SELECT COUNT(DISTINCT `" . self::F_APPID . "`) FROM `" . self::T_APPS . "`
										WHERE `" . self::F_NS . "` = \"" . _db_escape( N7_SOLUTION_ID ) . "\"" );
		}
		
		_db_query( "INSERT INTO `" . self::T_APPS . "`
						SET `" . self::F_NS . "` = \"" . _db_escape( N7_SOLUTION_ID ) . "\",
							`" . self::F_APPID . "` = \"" . _db_escape( $id ) . "\",
							`" . self::F_FSNAME . "` = \"" . _db_escape( $dir ) . "\",
							`" . self::F_VERSION . "` = \"" . _db_escape( $version ) . "\",
							`" . self::F_INSTSEQ . "` = \"" . _db_escape( $inst_seq ) . "\",
							`" . self::F_EXECSEQ . "` = \"" . _db_escape( $exec_seq ) . "\",
							`" . self::F_I18N . "` = \"" . _db_escape( $i18n ) . "\",
							`" . self::F_STAMP . "` = NOW(),
							`" . self::F_FLAGS . "` = \"" . _db_escape( $flags ) . "\"" );
	}
	
	/**
	 * Swaps positions in execution order.
	 * 
	 * @param string $id ID of application to move
	 * @param int $dist -1 or +1, direction
	 * @return bool 
	 */
	public static function move ( $id, $dist )
	{
		$apps = self::search( );
		
		if ( is_array( $apps ) )
		{
			for ( $i = 0; $i < count( $apps ); ++$i )
			{
				$app = $apps[$i];
				if ( $app[self::F_APPID] == $id )
				{
					$j = $i + $dist;
					if ( ( $j >= 0) && ( $j < count( $apps ) ) )
					{
						$swap = $apps[$j];
						if ( $swap[self::F_EXECSEQ] >= 0 )
						{
							_db_query( "UPDATE `" . self::T_APPS . "`
											SET `" . self::F_EXECSEQ . "` = " . _db_escape( $swap[self::F_EXECSEQ] ) . "
											WHERE `" . self::F_NS . "` = \"" . _db_escape( N7_SOLUTION_ID ) . "\"
												AND `" . self::F_APPID . "` = \"" . _db_escape( $app[self::F_APPID] ) . "\"
												AND `" . self::F_INSTSEQ . "` = \"" . _db_escape( $app[self::F_INSTSEQ] ) . "\"" );
							
							_db_query( "UPDATE `" . self::T_APPS . "`
											SET `" . self::F_EXECSEQ . "` = " . _db_escape( $app[self::F_EXECSEQ] ) . "
											WHERE `" . self::F_NS . "` = \"" . _db_escape( N7_SOLUTION_ID ) . "\"
												AND `" . self::F_APPID . "` = \"" . _db_escape( $swap[self::F_APPID] ) . "\"
												AND `" . self::F_INSTSEQ . "` = \"" . _db_escape( $swap[self::F_INSTSEQ] ) . "\"" );
							
							return true;
						}
					}
					break;
				}
			}
		}
		return false;
	}
	
}

?>