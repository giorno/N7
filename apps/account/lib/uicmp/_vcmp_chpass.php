<?php

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi_ind.php';

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
class _vcmp_chpass extends _vcmp_comp
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
		
		$buttons = new _uicmp_buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				//$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Reset', _uicmp_gi::IT_A, $messages['sem']['btReset'], $this->uicmp->getJsVar() . '.reset( );', '_uicmp_gi_now _uicmp_blue_b' ) );
				//$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S1', _uicmp_gi::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Save', _uicmp_gi::IT_BT, $messages['chpass']['btDo'], $this->uicmp->getJsVar() . '.save( );' ) );
				
				$this->ind = new _uicmp_gi_ind( $buttons, $buttons->getId( ) . '.Ind', _uicmp_gi::IT_IND, $messages['chpass']['ind'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generates requirements and logic for client side.
	 */
	public function generateJs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_JS, array( 'inc/account/_uicmp.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . " = new _uicmp_chpass( '" . $this->uicmp->getHtmlId( ) . "', '{$this->url}', " . $this->generateJsArray( $this->params ) . ", {$this->ind->getJsVar()} );" );
		}
	}
}

?>