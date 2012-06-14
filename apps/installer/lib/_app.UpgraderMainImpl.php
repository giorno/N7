<?php

// vim: ts=4

/**
 * @file _app.UpgraderMainImpl.php
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

require_once INSTALLER_LIB. '_app.Upgrader.php';
require_once INSTALLER_LIB. 'uicmp/ugd_ctrl.php';

/**
 * Main RR implementation of Upgrader application.
 */
class UpgraderMainImpl extends Upgrader
{	
	protected function __construct ( )
	{
		parent::__construct( );
		
		include INSTALLER_ROOT . 'i18n/en.php';
		$this->messages = &$_msgInstaller;
	}


	public function exec ( )
	{
		$url		= n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php';	// Ajax server URL
		$params		= Array( 'app' => $this->id, 'action' => 'ue' );					// Ajax request parameters
		_smarty_wrapper::getInstance( )->setContent( INSTALLER_UI . 'index.html' );
		$layout = n7_ui::getInstance( )->getLayout( );
			$tab = $layout->createTab( $this->id . '.Tab', FALSE );
			$tab->getHead()->add( new \io\creat\chassis\uicmp\headline( $tab, $tab->getId() . '.Title', $this->messages['ugd']['title'] ) );
			$tab->getHead()->add( new \io\creat\chassis\uicmp\info( $tab, $tab->getId() . '.Info', $this->messages['ugd']['info'] ) );
			$tab->addVcmp( new \io\creat\n7\installer\ugd_ctrl( $tab, $this->id . '.Ctrl', $this->messages, n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php', array( 'app' => $this->id, 'action' => 'upgrade' ) ) );
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