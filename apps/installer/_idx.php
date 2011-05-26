<?php

/**
 * @file _idx.php
 * @author giorno
 * @package N7
 *
 * Registration of Installer application.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';
require_once INSTALLER_LIB . '_app.InstallerMainImpl.php';
InstallerMainImpl::getInstance();

?>