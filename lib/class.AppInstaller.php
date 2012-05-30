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
	 * @var array 
	 */
	protected $apps = NULL;
	
	/**
	 * Name of application folder under solution apps directory.
	 * @var string 
	 */
	protected $fs_name = NULL;
	
	/**
	 * Namespace for settings entries.
	 * @var string 
	 */
	protected $ns = NULL;
	
	/**
	 * Name of database table for settings.
	 * @var string 
	 */
	protected $table = NULL;
	
	/**
	 * Statements to execute.
	 * @var array 
	 */
	protected $stmts = NULL;
	
	/**
	 * Matrix of possible upgrades. Indexed by version from and valued by
	 * version to.
	 * @var array
	 */
	protected $upgrades = NULL;
	
	/**
	 * System PDO (from global repository).
	 * @var PDO
	 */
	protected $pdo = NULL;
	
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
		$this->pdo = n7_globals::getInstance( )->get( n7_globals::PDO );
	}
	
	/**
	 * Beginning of SQL transaction.
	 */
	protected function begin ( ) { $this->pdo->beginTransaction( ); }
	
	/**
	 * Commit of SQL transaction.
	 */
	protected function commit ( ) { $this->pdo->commit( ); }
	
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
							$this->pdo->query( $statement );

					/**
					 * Register application into AT.
					 */
					n7_at::register( $app[n7_at::F_APPID], $app[n7_at::F_FSNAME], $app[n7_at::F_VERSION], $app[n7_at::F_I18N], $app[n7_at::F_FLAGS] );
					
					return true;
				}
				
		return false;
	}
	
	/**
	 * Performs upgrade by using SQL statements. Information about source and
	 * target versions is provided by AT class (n7_at).
	 * 
	 * @return bool success of operation
	 */
	public function upgrade ( )
	{
		if ( is_array( $this->apps ) )
			foreach ( $this->apps as $app )
				if ( ( $app[n7_at::F_FSNAME] == $this->fs_name ) && ( $app[n7_at::F_EXECSEQ] == n7_at::V_CONFLICT ) )
				{
					/**
					 * Find the upgrade path.
					 */
					$next = $app[n7_at::F_VERSION];
					$path = NULL;
					$stack[] = array( $next );
					while( !is_null( $next ) )
					{
						// Solution is found, trace back, write path and
						// terminate.
						if ( $next == $app['man']['version'] )
						{
							foreach ( $stack as $entry )
								$path[] = $entry[0];
							
							break;
						}
						
						// Shift.
						if ( array_key_exists( $next, $this->upgrades ) )
						{
							$stack[] = $this->upgrades[$next];
							$next = $this->upgrades[$next][0];
							continue;
						}
						
						// Reduce.
						array_pop( $stack );
						
						if ( count( $stack ) == 0 )
							$next = $stack[count( $stack ) - 1][0];
						else
							$next = NULL; // nothing left on the stack
					}
					
					if ( is_null( $path ) )
						return FALSE;
					
					// Upgrade.
					$this->begin( );
					
						for ( $i = count( $path ) - 1; $i > 0 ; --$i )
							$this->incrUpgrade( $path[$i - 1], $path[$i] );

						// If above calls prepared SQl statements.
						if ( is_array( $this->stmts ) )
							foreach( $this->stmts as $statement )
								$this->pdo->query( $statement );
					
					$this->commit( );

					// Register application into AT.
					n7_at::register( $app[n7_at::F_APPID], $app[n7_at::F_FSNAME], $app['man']['version'], $app[n7_at::F_I18N], $app[n7_at::F_FLAGS] );
					
					return true;
				}
				
		return false;
	}
}

?>