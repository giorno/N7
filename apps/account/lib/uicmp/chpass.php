<?php

/**
 * @file chpass.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7\apps\account\uicmp;

require_once CHASSIS_LIB . 'uicmp/tab.php';
require_once CHASSIS_LIB . 'uicmp/headline.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/simplefrm.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

/** 
 * Object rendering change password dialog.
 */
class chpass extends \io\creat\chassis\uicmp\tab
{
	/**
	 * Localization messages.
	 * 
	 * @var array
	 */
	protected $messages = NULL;
	
	/**
	 * Form instance.
	 * 
	 * @var \io\creat\chassis\uicmp\simplefrm
	 */
	protected $frm = NULL;
	
	/**
	 * Indicator component.
	 * 
	 * @var \io\creat\chassis\uicmp\indicator
	 */
	protected $ind = NULL;
	
	public function __construct ( &$parent, $id, $url, $params, &$messages )
	{
		parent::__construct( $parent, $id );
		$this->setAjaxProperties( $url, $params );
		$this->renderer	= CHASSIS_UI . 'uicmp/dlg.html';
		$this->messages = $messages;
		$this->jsPrefix	= 'account_chpass_i';
		
		$this->getHead( )->add( new \io\creat\chassis\uicmp\headline( $this->getHead( ), $this->id . '.Title', $this->messages['capChPass'] ) );
		$this->getHead( )->add( $buttons = new \io\creat\chassis\uicmp\buttons( $this->getHead( ), $this->id . '.Buttons' ) );
			$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $this->messages['chpass']['btDo'], $this->getJsVar() . '.save( );' ) );
			$buttons->add( $this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->getId( ) . '.Ind', '', $messages['chpass']['ind'] ) );
			
			$this->frm = new \io\creat\chassis\uicmp\simplefrm( $this->getBody( ), $this->id . '.Frm' );
			$this->getBody( )->add( $this->frm );
				$this->frm->add( new \io\creat\chassis\uicmp\frmitem( $this->frm, 'old', $this->messages['chpass']['old'], '', '', \_uicmp::FIT_PASSWORD ) );
				$this->frm->add( new \io\creat\chassis\uicmp\frmitem( $this->frm, 'new', $this->messages['chpass']['new'], '', '', \_uicmp::FIT_PASSWORD ) );
				$this->frm->add( new \io\creat\chassis\uicmp\frmitem( $this->frm, 'retype', $this->messages['chpass']['retype'], '', '', \_uicmp::FIT_PASSWORD ) );
			
		$this->show( );
		$this->parent->addUicmp( $this );
	}
	
	/**
	 * Generates requirements and logic for client side.
	 */
	public function generateReqs ( )
	{
		parent::generateReqs( );
		$requirer = $this->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			/**
			 * These 3 resources are required for our _uicmp.js file.
			 */
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative( ) . '3rd/XMLWriter-1.0.0-min.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative( ) . '3rd/base64.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/js/sem.js' , __CLASS__ ) );
			
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/account/_uicmp.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->getJsVar( ) . " = new account_chpass( '" . $this->getHtmlId( ) . "', '" . $this->frm->getHtmlId( ) . "', '{$this->url}', " . \io\creat\chassis\uicmp\uicmp::toJsArray( $this->params ) . ", {$this->ind->getJsVar()} );" );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_ONLOAD, $this->getJsVar( ) . '.startup();' );
		}
	}
}

?>