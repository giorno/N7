<?php

/**
 * @file _vcmp_ue.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Virtual component rendering User Editor (UE) tab.
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi_ind.php';

require_once APP_AI_LIB . 'uicmp/_uicmp_ue.php';

class _vcmp_ue extends _vcmp_comp
{
	/**
	 * Reference to User Editor (UE) instance.
	 * 
	 * @var _uicmp_ue 
	 */
	protected $ue = NULL;
	
	/**
	 * Reference to Save button.
	 * 
	 * @var _uicmp_gi 
	 */
	protected $bt = NULL;
	
	/**
	 * Reference to indicator item.
	 * 
	 * @var _uicmp_gi_ind
	 */
	protected $ind = NULL;
	
	/**
	 * Localization messages used for UICMP components.
	 * 
	 * @var array 
	 */
	protected $messages = NULL;
	
	/**
	 * Ajax server URL.
	 * 
	 * @var string 
	 */
	protected $url = NULL;
	
	/**
	 * Ajax request parameters. Associative array.
	 * 
	 * @var array 
	 */
	protected $params = NULL;
	
	public function __construct ( &$parent, $id, &$messages, $url, $params )
	{
		parent::__construct( $parent );
		
		$this->messages	= $messages;
		$this->url		= $url;
		$this->params	= $params;
		
		$this->ue		= new _uicmp_ue( $this->parent->getBody( ), $id, $url, $params );
		
		$this->parent->getBody( )->add( $this->ue );
		$this->parent->getHead( )->add( new _uicmp_title( $this->parent, $this->parent->getId( ) . '.Title', $this->messages['ue']['create'] ) );
		
		$buttons = new _uicmp_buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.Back', _uicmp_gi::IT_A, $this->messages['ue']['btBack'], $this->parent->getLayoutJsVar( ) . '.back( );', '_uicmp_gi_back' ) );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S1', _uicmp_gi::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Save', _uicmp_gi::IT_BT, $this->messages['ue']['btSave'], $this->ue->getJsVar() . '.save( );' ) );
				$this->ind = new _uicmp_gi_ind( $buttons, $buttons->getId( ) . '.Ind', _uicmp_gi::IT_IND, $this->messages['ue']['i'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	public function generateJs ( )
	{
		$requirer = $this->ue->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_JS, array( 'inc/ai/_uicmp.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->ue->getJsVar( ) . ' = new _uicmp_ue( ' . $this->parent->getLayoutJsVar( ) . ', \'' . $this->parent->getHtmlId( ) . '\', \'' . $this->parent->getHead( )->getFirst( )->getHtmlId( ) . '\', \'' . $this->ue->getHtmlId( ) . '\', \'' . $this->bt->getHtmlId( ) . '\', ' . $this->ind->getJsVar( ) . ', \''. $this->url . '\', ' . $this->generateJsArray( $this->params ) . ' );' );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, $this->parent->getLayoutJsVar( ) . '.registerTabCb( \'' . $this->parent->getHtmlId( ) . '\', \'onLoad\', ' . $this->ue->getJsVar( ) . '.startup );' );
			
		}
	}
	
	public function getJsVar () { return $this->ue->getJsVar(); }
}

?>