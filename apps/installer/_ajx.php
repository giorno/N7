<?php

// vim: ts=4

/**
 * @file ajx.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Registration of Installer Ajax server implementation.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

$sql = n7_globals::getInstance( )->get( n7_globals::PDO )->query( "SHOW TABLES" );

// There are tables, so it cannot be an attempt to install.
if ( $sql->fetch( ) )
{
	require_once INSTALLER_LIB . '_app.UpgraderAjaxImpl.php';
	UpgraderAjaxImpl::getInstance();
}
else
{
	require_once INSTALLER_LIB . '_app.InstallerAjaxImpl.php';
	InstallerAjaxImpl::getInstance();
}

?>