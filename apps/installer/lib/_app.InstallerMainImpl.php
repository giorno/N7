<?php

// vim: ts=4

/**
 * @file _app.InstallerMainImpl.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/headline.php';
require_once CHASSIS_LIB . 'uicmp/info.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';
require_once N7_SOLUTION_LIB . 'n7_timezone.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';

require_once INSTALLER_LIB. '_app.Installer.php';
require_once INSTALLER_LIB. 'uicmp/_vcmp_inst_ctrl.php';

/**
 * Main RR implementation of Installer application.
 */
class InstallerMainImpl extends Installer
{	
	protected function __construct ( )
	{
		parent::__construct( );
		
		include INSTALLER_ROOT . 'i18n/en.php';
		$this->messages = &$_msgInstaller;
	}


	public function exec ( )
	{
		_smarty_wrapper::getInstance( )->setContent( INSTALLER_UI . 'index.html' );
		$layout = n7_ui::getInstance( )->getLayout( );
			$tab = $layout->createTab( $this->id . '.Tab', FALSE );
			$tab->getHead()->add( new \io\creat\chassis\uicmp\headline( $tab, $tab->getId() . '.Title', $this->messages['inst']['title'] ) );
			$tab->getHead()->add( new \io\creat\chassis\uicmp\info( $tab, $tab->getId() . '.Info', $this->messages['inst']['info'] ) );
			$tab->addVcmp( new _vcmp_inst_ctrl( $tab, $this->id . '.Ctrl', n7_timezone::allZones( ), $this->messages ) );
			$layout->init( );
			
		/**
		 * Set Smarty resources for the application interface.
		 */
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
			$smarty->assignByRef( 'APP_INST_CTRL',	$layout );
			$smarty->assignByRef( 'APP_INST_MSG',	$this->messages );
				
	}
}

?>