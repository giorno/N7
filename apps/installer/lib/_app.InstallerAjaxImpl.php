<?php

/**
 * @file _app.InstallerAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'libpdo.php';
require_once CHASSIS_LIB . 'libfw.php';
require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once N7_SOLUTION_LIB . 'n7_at.php';

require_once N7_SOLUTION_APPS. 'ai/_cfg.php';
require_once N7_SOLUTION_APPS. 'ai/lib/pers/users.php';

require_once INSTALLER_LIB. '_app.Installer.php';

/** 
 * Ajax server implementation for Installer application.
 */
class InstallerAjaxImpl extends Installer
{
	/**
	 * Main execution body of Installer. This actually analyses data from client
	 * side, performs installation and produces result.
	 */
	public function exec ( )
	{
		// Check database connection data.
		if ( is_null( $this->pdo ) )
		{
			echo 'e_connect';
			return;
		}
		// Check database if it is empty.
		$sql = $this->pdo->query( "SHOW TABLES" );
		if ( $sql->fetch( PDO::FETCH_NUM ) )
		{
			echo 'e_empty';
			return;
		}

		// Check validity of address.
		$validator = new EmailAddressValidator( );
		if ( $validator->check_email_address( $_POST['email'] ) )
		{
			// Check for correct login.
			if ( !\io\creat\n7\ai\users::loginOk( $_POST['login'] ) )
			{
				echo 'e_login';
				return;
			}
							
			// Check for password in case of new entry or any password supplied.
			if ( !\io\creat\n7\ai\users::passOk( $_POST['password'] ) )
			{
				echo 'e_pass';
				return;
			}
		}
		else
		{
			echo 'e_address';
			return;
		}
		
		// We made it here, so provided data have to be correct.
		// Installing database
		$tables = file_get_contents( CHASSIS_ROOT . 'sql/tables.sql' );
		$at = file_get_contents( INSTALLER_ROOT . 'sql/tables.sql' );
		$settings = file_get_contents( INSTALLER_ROOT . 'sql/settings.sql' );
		
		$body = $tables . "\n\r" . $at . "\n\r" . $settings;
		$table = Config::T_SETTINGS;
		$ns = N7_SOLUTION_ID;
		
		/**
		 * Remove comments and bind namespace.
		 */
		$comments = array( '/\s*--.*\n/' );
		$script = preg_replace( $comments, "\n", $body );
		$script = str_replace( '{$__1}', $table, $script );
		$script = str_replace( '{$__2}', $ns, $script );
		$statements = explode( ";\n", $script );

		$this->pdo->beginTransaction( );

		// Execute scripts.
		if ( is_array( $statements ) )
			foreach( $statements as $statement )
				if ( trim( $statement ) !== '' )
					$this->pdo->query( $statement );
			
		$sql = $this->pdo->prepare( "UPDATE `{$table}`
				SET `value` = ?
				WHERE `scope` = 'G'
					AND `ns` = '{$ns}'
					AND `key` = ?" );
			
		// Updating server and user global properties.
		$sql->execute( array( $_POST['site'], 'server.url.site' ) );
		$sql->execute( array( $_POST['schema'], 'server.url.scheme' ) );
		$sql->execute( array( $_POST['modrw'], 'server.url.modrw' ) );
		$sql->execute( array( $_POST['tz'], 'server.tz' ) );
		$sql->execute( array( $_POST['tz'], 'usr.tz' ) );
			
		// Set N7 version information.
		$sql->execute( array( N7_SOLUTION_VERSION, 'server.version' ) );
		$sql->execute( array( N7_SOLUTION_VERSION, 'server.magic' ) );
			
		// Create admin account.
		$this->pdo->prepare( "INSERT INTO `" . Config::T_USERS . "` SET `" . Config::F_LOGIN . "` = ?, `" . Config::F_PASSWD . "` = ?, `" . Config::F_EMAIL . "` = ?, `" . Config::F_ENABLED. "` = ?" )->execute( array( $_POST['login'], _fw_hash_passwd( $_POST['password'] ), $_POST['email'], 1 ) );
			
		// Workaround root ID.
		$this->pdo->query( "UPDATE `" . Config::T_USERS . "` SET `" . Config::F_UID . "` = 1" );
			
		// Populate applications table with bundled applications.
		$apps = array ( 'branding', 'signed', 'account', 'ai', 'login' );
		foreach( $apps as $app )
		{
			$man = NULL;
			include N7_SOLUTION_APPS . $app . '/_man.php';
			n7_at::register( $man['id'] , $app, $man['version'], serialize( $man['i18n'] ), $man['flags'] );
		}

		// Check and produce output
		if ( (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT COUNT(*) FROM `" . Config::T_USERS . "` WHERE `" . Config::F_UID . "` = 1" ) ) > 0 )
			echo 'OK';
		else
		{
			require_once N7_SOLUTION_APPS . 'signed/lib/_wwg.News.php';
			
			// Should be safe as database was empty before we started to mess
			// with it.
			$this->pdo->query( "DROP TABLE `" . Config::T_SETTINGS . "`" );
			$this->pdo->query( "DROP TABLE `" . Config::T_SIGNTOKENS . "`" );
			$this->pdo->query( "DROP TABLE `" . Config::T_SESSIONS . "`" );
			$this->pdo->query( "DROP TABLE `" . Config::T_LOGINS . "`" );
			
			$this->pdo->query( "DROP TABLE `" . n7_at::T_APPS . "`" );
			$this->pdo->query( "DROP TABLE `" . \io\creat\n7\apps\Signed\News::T_RSSCACHE . "`" );
			$this->pdo->query( "DROP TABLE `" . Config::T_USERS . "`" );
			$this->pdo->query( "DROP TABLE `" . XmlRpcSrv::T_RPCSESS . "`" );
			
			echo 'e_unknown';
		}
		
		$this->pdo->commit( );
	}
}

?>
