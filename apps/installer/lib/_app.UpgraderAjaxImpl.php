<?php

// vim: ts=4

/**
 * @file _app.UpgraderAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'libpdo.php';
require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once N7_SOLUTION_LIB . 'n7_at.php';
require_once N7_SOLUTION_LIB . 'n7_globals.php';

require_once INSTALLER_LIB. '_app.Upgrader.php';

/** 
 * Ajax server implementation for Upgrader application.
 */
class UpgraderAjaxImpl extends Upgrader
{
	/**
	 * Counter used for generated names of temporary objects.
	 * @var int
	 */
	protected $counter = 0;
	
	/**
	 * Creates an array of separate SQL statements from bulk of plaintext
	 * statements. Effectively removes comments.
	 * @param string $sql plaintext SQL statements separated by semicolon
	 * @return array
	 */
	protected function sanitize ( &$sql )
	{
		$comments = array( '/\s*--.*\n/' );
		$script = preg_replace( $comments, "\n", $sql );
		return explode( ";\n", $script );
	}
	
	/**
	 * Performs single upgrade step.
	 * @param string $from version to upgrade from
	 * @param string $to version to upgrade to
	 */
	protected function incrUpgrade ( $from, $to )
	{
		switch ( $from )
		{
			case '0.1.1-beta':
				switch ( $to )
				{
					// From some ancient version to 0.1.1-beta.
					case '0.1.2-dev':
						
						// Prepare table of settings for new index.
						//`scope`, `id`, `ns`, `key`
						++$this->counter;
						$this->pdo->query( "CREATE TEMPORARY TABLE incrUpgrade{$this->counter}
											AS SELECT * FROM `" . Config::T_SETTINGS . "`
											GROUP by `" . Config::F_SCOPE . "`, `" . Config::F_ID . "`, `" . Config::F_NS . "`, `" . Config::F_KEY . "`" );
						
						$this->pdo->query( "DELETE FROM `" . Config::T_SETTINGS . "`" );
						$this->pdo->query( "INSERT INTO `" . Config::T_SETTINGS . "` SELECT * FROM incrUpgrade{$this->counter}" );
						
						// Upgrade.
						$script = file_get_contents( INSTALLER_ROOT . 'sql/delta/0.1.1-betato0.1.2-dev.sql' );
						$sqls = $this->sanitize( $script );
						foreach ( $sqls as $sql )
							$this->pdo->query( $sql );
					break;
				}
			break;
		
			// This is a case when some ancient version did not put its version
			// tag into global 'server.version' setting.
			case '';
			default:
				switch ( $to )
				{
					// From some ancient version to 0.1.1-beta.
					case '0.1.1-beta':
						// Upgrade.
						$script = file_get_contents( INSTALLER_ROOT . 'sql/delta/0to0.1.1-beta.sql' );
						$sqls = $this->sanitize( $script );
						foreach ( $sqls as $sql )
							$this->pdo->query( $sql );
					break;
				}
			break;
		}
	}
	
	/**
	 * Main execution body of Installer. This actually analyses data from client
	 * side, performs installation and produces result.
	 */
	public function exec ( )
	{
		if ( \io\creat\chassis\session::getInstance( )->login( N7_SOLUTION_ID, $_POST['login'], $_POST['password'] ) )
		{
			// Build matrix/tree of possible upgrades.
			$upgrades[''] = array( '0.1.1-beta' ); // from any ancient version to 0.0.1-beta (first tagged)
			$upgrades['0.1.1-beta'] = array( '0.1.2-dev' );
			
		
			$from = (string)n7_globals::getInstance( )->get( 'config' )->get( 'server.version' );
			
			// Find the upgrade path.
			$next = $from;
			$path = NULL;
			$stack[] = array( $next );
			while( !is_null( $next ) )
			{
				// Solution is found, trace back, write path and
				// terminate.
				if ( $next == N7_SOLUTION_VERSION )
				{
					foreach ( $stack as $entry )
						$path[] = $entry[0];
					
					break;
				}
						
				// Shift.
				if ( array_key_exists( $next, $upgrades ) )
				{
					$stack[] = $upgrades[$next];
					$next = $upgrades[$next][0];
					continue;
				}
						
				// Reduce.
				array_pop( $stack );
						
				if ( count( $stack ) == 0 )
					$next = $stack[count( $stack ) - 1][0];
				else
					$next = NULL; // nothing left on the stack
			}
					
			// Upgrade is not possible.
			if ( is_null( $path ) )
			{
				echo 'e_path';
				return;
			}
			
			// Feuer!
			$this->pdo->beginTransaction( );
			
			// Apply delta changes.
			for ( $i = count( $path ) - 1; $i > 0 ; --$i )
				$this->incrUpgrade( $path[$i - 1], $path[$i] );
			
			// Update application table.
			$apps = array ( 'branding', 'signed', 'account', 'ai', 'login' );
			foreach( $apps as $app )
			{
				$man = NULL;
				include N7_SOLUTION_APPS . $app . '/_man.php';
				
				// Manifest version should be the same as N7_SOLUTION_VERSION.
				n7_at::register( $man['id'] , $app, $man['version'], serialize( $man['i18n'] ), $man['flags'] );
			}
				
			// Update 'server.version' record.
			n7_globals::getInstance( )->get( 'config' )->set( 'server.version', N7_SOLUTION_VERSION );
			
			$this->pdo->commit( );
			
			// Report success to the client logic.
			echo 'done';
		}
		else
			echo 'e_root'; // Admin login failed.
	}
}

?>
