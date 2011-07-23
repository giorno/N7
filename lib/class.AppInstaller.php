<?php

/**
 * @file _app.Ab.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_CFG . 'class.Config.php';

require_once N7_SOLUTION_LIB . 'n7_at.php';

/**
 * Executor of application installation procedure. This class is not involved
 * in installation of core applications. Those are installed using solution
 * Installer application.
 */
abstract class AppInstaller
{
	/**
	 * List of applications, both, installed and pending.
	 * 
	 * @var array 
	 */
	protected $apps = NULL;
	
	/**
	 * Name of application folder under solution apps directory.
	 * 
	 * @var string 
	 */
	protected $fs_name = NULL;
	
	/**
	 * Namespace for settings entries.
	 * 
	 * @var string 
	 */
	protected $ns = NULL;
	
	/**
	 * Name of database table for settings.
	 * 
	 * @var string 
	 */
	protected $table = NULL;
	
	/**
	 * Statements to execute.
	 * 
	 * @var array 
	 */
	protected $stmts = NULL;
	
	/**
	 * Constructor. Initializes installer.
	 * 
	 * @param string $fs_name name of app folder under apps dir
	 * @param string $ns application settings namespace
	 * @param string $table database table for settings, optional
	 */
	public function __construct ( $fs_name, $ns, $table = Config::T_SETTINGS )
	{
		$this->fs_name	= $fs_name;
		$this->ns		= $ns;
		$this->table	= $table;
		
		$this->apps  = n7_at::search( );
	}
	
	/**
	 * Beginning of SQL transaction.
	 */
	protected function begin ( ) { _db_query( 'BEGIN' ); }
	
	/**
	 * Commit of SQL transaction.
	 */
	protected function commit ( ) { _db_query( 'COMMIT' ); }
	
	/**
	 * Main installation method. This may be overridden in derived classes to
	 * provide specific structure or order of execution.
	 * 
	 * @return bool success of operation
	 */
	public function exec ( )
	{
		$this->prepare( );
		$this->begin( );
			$success  = $this->install( );
		$this->commit( );
		
		return $success;
	}
	
	/**
	 * Load application SQL scripts and compose SQL statements array.
	 */
	protected function prepare ( )
	{
		$tables_file	= N7_SOLUTION_APPS . $this->fs_name . '/inst/tables.sql';
		$settings_file	= N7_SOLUTION_APPS . $this->fs_name . '/inst/settings.sql';
		
		$tables_sql		= '';
		$settings_sql	= '';
		
		if ( file_exists( $tables_file ) )
			$tables_sql = file_get_contents ( $tables_file );
		
		if ( file_exists( $settings_file ) )
			$settings_sql = file_get_contents ( $settings_file );
		
		$sql = $tables_sql . "\n\r" . $settings_sql;
		
		/**
		 * Remove comments and bind namespace and table name.
		 */
		$comments = array( '/\s*--.*\n/' );
		$script = preg_replace( $comments, "\n", $sql );
		$script = str_replace( '{$__1}', $this->table, $script );
		$script = str_replace( '{$__2}', $this->ns, $script );
		$this->stmts = explode( ";\n", $script );
	}
	
	/**
	 * Performs installation by using SQL statements.
	 * 
	 * @return bool success of operation
	 */
	protected function install ( )
	{
		if ( is_array( $this->apps ) )
			foreach ( $this->apps as $app )
				if ( ( $app[n7_at::F_FSNAME] == $this->fs_name ) && ( $app[n7_at::F_EXECSEQ] == n7_at::V_CANDIDATE ) )
				{
					/**
					 * Execute database scripts.
					 */
					if ( is_array( $this->stmts ) )
						foreach( $this->stmts as $statement )
							_db_query( $statement );

					/**
					 * Register application into AT.
					 */
					n7_at::register( $app[n7_at::F_APPID], $app[n7_at::F_FSNAME], $app[n7_at::F_VERSION], $app[n7_at::F_I18N], $app[n7_at::F_FLAGS] );
					
					return true;
				}
				
		return false;
	}
}

?>