<?php

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

require_once ACCTAB_LIB . 'uicmp/_uicmp_chpass.php';

/**
 * @file _vcmp_chpass.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Object rendering change password tab content.
 */
class _vcmp_chpass extends \io\creat\chassis\uicmp\vcmp
{
	/**
	 * Form component.
	 * 
	 * @var _uicmp_chpass
	 */
	protected $uicmp = NULL;
	
	/**
	 * Indicator component.
	 * 
	 * @var _uicmp_gi_ind
	 */
	protected $ind = NULL;
	
	/**
	 * URL for Ajax requests.
	 * 
	 * @var string
	 */
	protected $url = NULL;
	
	/**
	 * Associative array of Ajax request parameters.
	 * @var array
	 */
	protected $params = NULL;
	
	public function __construct ( &$parent, $id, $url, $params, &$messages )
	{
		parent::__construct( $parent );
		$this->url		= $url;
		$this->params	= $params;
		
		$this->uicmp	= new _uicmp_chpass( $this->parent->getBody( ), $id );

		$this->parent->getBody( )->add( $this->uicmp );
		
		$buttons = new \io\creat\chassis\uicmp\buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				//$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Reset', _uicmp_gi::IT_A, $messages['sem']['btReset'], $this->uicmp->getJsVar() . '.reset( );', '_uicmp_gi_now _uicmp_blue_b' ) );
				//$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S1', _uicmp_gi::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $messages['chpass']['btDo'], $this->uicmp->getJsVar() . '.save( );' ) );
				
				$this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->getId( ) . '.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, $messages['chpass']['ind'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generates requirements and logic for client side.
	 */
	public function generateReqs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/account/_uicmp.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . " = new _uicmp_chpass( '" . $this->uicmp->getHtmlId( ) . "', '{$this->url}', " . \io\creat\chassis\uicmp\uicmp::toJsArray( $this->params ) . ", {$this->ind->getJsVar()} );" );
		}
	}
}

?>