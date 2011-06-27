<?php

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
	const T_APPS		= 'tApps';
	
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
				$ret[$app[self::F_APPID]] = $app;
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
	 * Creates list of applications registered in the system and candidates for
	 * installation.
	 */
	public static function search ( )
	{
		/**
		 * List installed applications.
		 */
		
		
		/**
		 * Scan for candidates in apps directory.
		 */
	}
	
	/**
	 * Registers new application or its new version into the table.
	 * 
	 * @todo optimize query to compute inst_seq value internally
	 * 
	 * @param string $id indetifier of application, value of APP_ID member
	 * @param string $dir name of application folder in solution apps folder
	 * @param string $version version of the application
	 * @param int $flags flags of the application
	 */
	public static function register ( $id, $dir, $version, $flags = 0 )
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
							`" . self::F_STAMP . "` = NOW(),
							`" . self::F_FLAGS . "` = \"" . _db_escape( $flags ) . "\"" );
	}
	
}

?>