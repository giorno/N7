<?php

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

require_once INSTALLER_LIB . '_app.InstallerMainImpl.php';
require_once INSTALLER_LIB . 'uicmp/_uicmp_inst_frm.php';

/**
 * @file _vcmp_inst_ctrl.php
 * @author giorno
 * @package N7
 * 
 * Virtual component creating user interface of installer app.
 */
class _vcmp_inst_ctrl extends \io\creat\chassis\uicmp\vcmp
{
	/**
	 * Instance of installer form.
	 * 
	 * @var _uicmp_inst_frm
	 */
	protected $uicmp = NULL;
	
	/**
	 * Button instance.
	 * 
	 * @var _uicmp_gi 
	 */
	protected $bt = NULL;
	
	/**
	 * Indicator in _uicmp_buttons group.
	 * 
	 * @var _uicmp_gi_ind 
	 */
	protected $ind = NULL;
	
	/**
	 * Reference to localization strings array.
	 * 
	 * @var array 
	 */
	protected $messages = NULL;
	
	public function __construct ( &$parent, $id, $zones, &$messages )
	{
		parent::__construct( $parent );
		$this->messages	= $messages;
		
		$this->uicmp	= new _uicmp_inst_frm( $this->parent->getBody( ), $id . 'Form', $zones );
		$this->parent->getBody( )->add( $this->uicmp );
		
		$buttons = new \io\creat\chassis\uicmp\buttons( $this->parent->getHead( ), $id . 'Buttons' );
			$this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->id . '.Next', \io\creat\chassis\uicmp\grpitem::IT_BT, $this->messages['btInstall'], $this->uicmp->getJsVar( ) . '.install( );' );
			$buttons->add( $this->bt );
			$this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->id . '.ind', 'ind', $this->messages['status'] );
			$buttons->add( $this->ind );
			$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generate client side content and requirements.
	 */
	public function generateReqs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		
		if ( $requirer )
		{
			$js = file_get_contents( INSTALLER_LIB . 'uicmp/inst.js' );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $js );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_ONLOAD, $this->uicmp->getJsVar( ) . '.startup( );' );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . ' = new inst_ctrl( \'' . $this->uicmp->getHtmlId( ) . '\', \'' . $this->bt->getHtmlId( ) . '\', ' . $this->ind->getJsVar( ) . ', \'' . InstallerMainImpl::ID . '\' );' );
		}
	}
}

?>