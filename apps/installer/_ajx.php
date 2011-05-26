<?php

/**
 * @file ajx.php
 * @author giorno
 * @package N7
 *
 * Registration of Installer Ajax server implementation.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';
require_once INSTALLER_LIB . '_app.InstallerAjaxImpl.php';
InstallerAjaxImpl::getInstance();

?>