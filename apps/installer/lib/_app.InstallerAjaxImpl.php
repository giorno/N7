<?php

require_once CHASSIS_LIB . 'libdb.php';
require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once N7_SOLUTION_LIB . 'n7_at.php';
require_once N7_SOLUTION_APPS. 'ai/lib/class.AiUser.php';

require_once INSTALLER_LIB. '_app.Installer.php';

/**
 * @file _app.InstallerAjaxImpl.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 * 
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
		/**
		 * All roads lead to Rome.
		 */
		
		/**
		 * Check database connection data.
		 */
		$db_ok = @_db_connect( N7_MYSQL_HOST, N7_MYSQL_USER, N7_MYSQL_PASS, N7_MYSQL_DB );
		if ( !$db_ok )
		{
			echo 'e_connect';
			return;
		}
		
		/**
		 * Check database if it is empty.
		 */
		if( (int)_db_rowcount( _db_query( "SHOW TABLES;" ) ) > 0 )
		{
			echo 'e_empty';
			return;
		}

		/**
		 * Check validity of address.
		 */
		$validator = new EmailAddressValidator( );
		if ( $validator->check_email_address( $_POST['email'] ) )
		{
			/**
			 * Check for correct login.
			 */
			if ( !AiUser::loginOk( $_POST['login'] ) )
			{
				echo 'e_login';
				return;
			}
							
			/**
			 * Check for password in case of new entry or any password supplied.
			 */
			if ( !AiUser::passOk( $_POST['password'] ) )
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
		
		/**
		 * We made it here, so provided data have to be correct.
		 * 
		 * Installing database
		 */
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

			_db_query( "BEGIN" );

			/**
			 * Execute scripts.
			 */
			if ( is_array( $statements ) )
				foreach( $statements as $statement )
					_db_query( $statement );
			
			/**
			 * Updating server and user global properties.
			 */
			_db_query( "UPDATE `{$table}` SET `value` = \"" . _db_escape( $_POST['site']) . "\" WHERE `scope` = \"G\" AND `ns` = \"{$ns}\" AND `key` = \"server.url.site\"" );
			_db_query( "UPDATE `{$table}` SET `value` = \"" . _db_escape( $_POST['schema']) . "\" WHERE `scope` = \"G\" AND `ns` = \"{$ns}\" AND `key` = \"server.url.scheme\"" );
			_db_query( "UPDATE `{$table}` SET `value` = \"" . _db_escape( $_POST['modrw']) . "\" WHERE `scope` = \"G\" AND `ns` = \"{$ns}\" AND `key` = \"server.url.modrw\"" );
			_db_query( "UPDATE `{$table}` SET `value` = \"" . _db_escape( $_POST['tz']) . "\" WHERE `scope` = \"G\" AND `ns` = \"{$ns}\" AND `key` = \"server.tz\"" );
			_db_query( "UPDATE `{$table}` SET `value` = \"" . _db_escape( $_POST['tz']) . "\" WHERE `scope` = \"G\" AND `ns` = \"{$ns}\" AND `key` = \"usr.tz\"" );

			/**
			 * Create admin account.
			 */
			
			AiUser::save( 0, $_POST['login'], $_POST['password'], $_POST['email'], true );
			_db_query ( "UPDATE `" . Config::T_USERS . "` SET`" . Config::F_UID . "` = \"1\"" );
			
			/**
			 * Populate applications table with bundled applications.
			 */
			require_once N7_SOLUTION_APPS . 'branding/_cfg.php';
			require_once N7_SOLUTION_APPS . 'branding/lib/_app.Branding.php';
			n7_at::register( Branding::APP_ID , 'branding', 'base', n7_at::FL_MAINRR | n7_at::FL_SIGNED );
			
			require_once N7_SOLUTION_APPS . 'unsigned/_cfg.php';
			require_once N7_SOLUTION_APPS . 'unsigned/lib/_app.Login.php';
			n7_at::register( Login::APP_ID , 'unsigned', 'base', n7_at::FL_MAINRR | n7_at::FL_AJAXRR | n7_at::FL_UNSIGNED );
			
			require_once N7_SOLUTION_APPS . 'signed/_cfg.php';
			require_once N7_SOLUTION_APPS . 'signed/lib/_app.Signed.php';
			n7_at::register( Signed::APP_ID , 'signed', 'base', n7_at::FL_MAINRR | n7_at::FL_AJAXRR | n7_at::FL_SIGNED );
			
			require_once N7_SOLUTION_APPS . 'account/_cfg.php';
			require_once N7_SOLUTION_APPS . 'account/lib/_app.Account.php';
			n7_at::register( Account::APP_ID , 'account', 'base', n7_at::FL_MAINRR | n7_at::FL_AJAXRR | n7_at::FL_SIGNED );
			
			require_once N7_SOLUTION_APPS . 'ai/_cfg.php';
			require_once N7_SOLUTION_APPS . 'ai/lib/_app.Ai.php';
			n7_at::register( Ai::APP_ID , 'ai', 'base', n7_at::FL_MAINRR | n7_at::FL_AJAXRR | n7_at::FL_SIGNED );
			

			_db_query( "COMMIT" );
		
		/**
		 * Check and produce output
		 */
		$res = _db_query ( "SELECT * FROM `" . Config::T_USERS . "` WHERE `" . Config::F_UID . "` = \"1\"" );
		if ( (int)_db_rowcount( $res ) > 0 )
		{
			echo '<!--OK-->';
		}
		else
		{
			/**
			 * Should be safe as database was empty before we started to mess
			 * with it.
			 */
			_db_query( "DROP TABLE `" . Config::T_SETTINGS . "`" );
			_db_query( "DROP TABLE `" . Config::T_SIGNTOKENS . "`" );
			_db_query( "DROP TABLE `" . Config::T_SESSIONS . "`" );
			_db_query( "DROP TABLE `" . Config::T_LOGINS . "`" );
			_db_query( "DROP TABLE `" . Config::T_USERS . "`" );
			
			echo 'e_unknown';
		}
	}
}

?>