<?php

// vim: ts=4

/**
 * @file udg_ctrl.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7\installer;

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

require_once INSTALLER_LIB . 'uicmp/ugd_frm.php';

/**
 * Virtual component building up N7 upgrade screen.
 */
class ugd_ctrl extends \io\creat\chassis\uicmp\vcmp
{
	/**
	 * Instance of installer form.
	 * @var ugd_frm
	 */
	protected $uicmp = NULL;
	
	/**
	 * Button instance.
	 * @var \io\creat\chassis\uicmp\grpitem 
	 */
	protected $bt = NULL;
	
	/**
	 * Indicator in _uicmp_buttons group.
	 * @var \io\creat\chassis\uicmp\indicator
	 */
	protected $ind = NULL;
	
	public function __construct ( &$parent, $id, &$messages, $url, $params )
	{
		parent::__construct( $parent );
		$this->setAjaxProperties( $url, $params );
		
		$this->uicmp	= new ugd_frm( $this->parent->getBody( ), $id . 'Form' );
		$this->parent->getBody( )->add( $this->uicmp );
		
		$buttons = new \io\creat\chassis\uicmp\buttons( $this->parent->getHead( ), $id . 'Buttons' );
			$this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->id . '.Next', \io\creat\chassis\uicmp\grpitem::IT_BT, $messages['btUpgrade'], $this->uicmp->getJsVar( ) . '.upgrade( );' );
			$buttons->add( $this->bt );
			$this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->id . '.ind', 'ind', $messages['status'] );
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
			$js = file_get_contents( INSTALLER_LIB . 'uicmp/ugd.js' );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $js );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . ' = new ugd_ctrl( \'' . $this->uicmp->getHtmlId( ) . '\', \'' . $this->bt->getHtmlId( ) . '\', ' . $this->ind->getJsVar( ) . ', \'' . $this->url . '\', ' . $this->toJsArray( $this->params ) . ' );' );
		}
	}
}

?>