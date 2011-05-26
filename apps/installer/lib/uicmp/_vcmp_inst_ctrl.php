<?php

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_buttons.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi_ind.php';

require_once INSTALLER_LIB . 'uicmp/_uicmp_inst_frm.php';

/**
 * @file _vcmp_inst_ctrl.php
 * @author giorno
 * @package N7
 * 
 * Virtual component creating user interface of installer app.
 */
class _vcmp_inst_ctrl extends _vcmp_comp
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
		
		$buttons = new _uicmp_buttons( $this->parent->getHead( ), $id . 'Buttons' );
			$this->bt = new _uicmp_gi( $buttons, $buttons->id . '.Next', _uicmp_gi::IT_BT, $this->messages['btInstall'], $this->uicmp->getJsVar( ) . '.install( );' );
			$buttons->add( $this->bt );
			$this->ind = new _uicmp_gi_ind( $buttons, $buttons->id . '.ind', 'ind', $this->messages['status'] );
			$buttons->add( $this->ind );
			$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generate client side content and requirements.
	 */
	public function generateJs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		
		if ( $requirer )
		{
			$js = file_get_contents( INSTALLER_LIB . 'uicmp/_uicmp.js' );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, $js );
			$requirer->call( _uicmp_layout::RES_ONLOAD, $this->uicmp->getJsVar( ) . '.startup( );' );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . ' = new _vcmp_inst_ctrl( \'' . $this->uicmp->getHtmlId( ) . '\', \'' . $this->bt->getHtmlId( ) . '\', ' . $this->ind->getJsVar( ) . ' );' );
		}
	}
}

?>