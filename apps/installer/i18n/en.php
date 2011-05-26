<?php

/**
 * @file en.php
 * @author giorno
 * @package N7
 * 
 * Localization file for English language.
 */

$_msgInstaller['title']						= 'N7 Installation';
$_msgInstaller['info']						= 'Provide actual settings for this installation and details for administrator account to begin.';

$_msgInstaller['url']						= 'Site URL';
$_msgInstaller['modrw']						= 'mod-rewrite';
$_msgInstaller['tz']						= 'Server timezone';
$_msgInstaller['root']						= 'Administrator account details';
$_msgInstaller['login']						= 'Login';
$_msgInstaller['password']					= 'Password';
$_msgInstaller['email']						= 'E-mail';

$_msgInstaller['btInstall']					= 'Install';

$_msgInstaller['status']['executing']		= 'Executing...';
$_msgInstaller['status']['done']			= 'Done';
$_msgInstaller['status']['e_unknown']		= 'Error: invalid data or unknown failure!';
$_msgInstaller['status']['e_login']			= 'Error: invalid login name!';
$_msgInstaller['status']['e_pass']			= 'Error: invalid password!';
$_msgInstaller['status']['e_address']		= 'Error: invalid email address!';
$_msgInstaller['status']['e_connect']		= 'Error: could not connect to database!';
$_msgInstaller['status']['e_empty']			= 'Error: database is not empty!';

/**
 * Stage 1.
 */
$_msgInstaller['msg']['intro']				= 'This interface will steer your way to fully operational N7 installation. We hope you have read our documentation and have your hammer ready.';

/**
 * Stage 1.
 */
$_msgInstaller['msg']['conn_ok']			= 'Database connect details are correct.';
$_msgInstaller['msg']['conn_ko']			= 'Database connect details are incorrect! Could not connect to database.';

/**
 * Stage 2.
 */
$_msgInstaller['msg']['db_empty']			= 'Database is OK.';
$_msgInstaller['msg']['db_used']			= 'Database is not empty! Product can be installed only on empty database.';

/**
 * Stage 3.
 */
$_msgInstaller['msg']['db_used']			= 'Database is not empty! Product can be installed only on empty database.';

?>