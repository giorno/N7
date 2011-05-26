<?php

require_once CHASSIS_LIB . 'uicmp/_uicmp_title.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_info.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';
require_once N7_SOLUTION_LIB . 'n7_timezone.php';

require_once INSTALLER_LIB. '_app.Installer.php';
require_once INSTALLER_LIB. 'uicmp/_vcmp_inst_ctrl.php';

/**
 * @file _app.InstallerMainImpl.php
 * @author giorno
 * @package N7
 * 
 * Main RR implementation of Installer application.
 */
class InstallerMainImpl extends Installer
{
	/**
	 * Reference to UICMP Layout.
	 *
	 * @var _uicmp_layout
	 */
	protected $layout = NULL;
	
	protected function __construct ( )
	{
		parent::__construct( );
		
		include INSTALLER_ROOT . 'i18n/en.php';
		$this->messages = &$_msgInstaller;
	}


	public function exec ( )
	{
		$registry = _app_registry::getInstance( );
		if ( $registry )
		{
			$registry->requireCssPlain( 'td._inst_msg,td._inst_no{vertical-align:middle;font-size:14px;padding:12px;padding-top:8px;padding-bottom:8px;}' );
			$registry->requireCssPlain( 'td._inst_no{font-size:18px;font-weight:bold;width:1px;padding-right:0px;}' );
		}
		_smarty_wrapper::getInstance( )->setContent( INSTALLER_UI . 'index.html' );
		
		$this->layout = new _uicmp_layout( n7_requirer::getInstance( ) );
			$tab = $this->layout->createTab( $this->id . '.Tab', FALSE );
			$tab->getHead()->add( new _uicmp_title( $tab, $tab->getId() . '.Title', $this->messages['title'] ) );
			$tab->getHead()->add( new _uicmp_info( $tab, $tab->getId() . '.Info', $this->messages['info'] ) );
			$tab->addVcmp( new _vcmp_inst_ctrl( $tab, $this->id . '.Ctrl', n7_timezone::allZones( ), $this->messages ) );
			$this->layout->init( );
			
		/**
		 * Set Smarty resources for the application interface.
		 */
		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
			$smarty->assignByRef( 'APP_INST_CTRL',	$this->layout );
			$smarty->assignByRef( 'APP_INST_MSG',	$this->messages );
				
	}
}

?>