<?php

/**
 * @file _vcmp_ue.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Virtual component rendering User Editor (UE) tab.
 */

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';
require_once CHASSIS_LIB . 'uicmp/headline.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';

require_once APP_AI_LIB . 'uicmp/_uicmp_ue.php';

class _vcmp_ue extends \io\creat\chassis\uicmp\vcmp
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
		$this->parent->getHead( )->add( new \io\creat\chassis\uicmp\headline( $this->parent, $this->parent->getId( ) . '.Title', $this->messages['ue']['create'] ) );
		
		$buttons = new \io\creat\chassis\uicmp\buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Back', \io\creat\chassis\uicmp\grpitem::IT_A, $this->messages['ue']['btBack'], $this->parent->getLayoutJsVar( ) . '.back( );', '_uicmp_gi_back' ) );
				$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.S1', \io\creat\chassis\uicmp\grpitem::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $this->messages['ue']['btCreate'], $this->ue->getJsVar() . '.save( );' ) );
				$this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->getId( ) . '.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, $this->messages['ue']['i'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	public function generateReqs ( )
	{
		$requirer = $this->ue->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/ai/_uicmp.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->ue->getJsVar( ) . ' = new _uicmp_ue( ' . $this->parent->getLayoutJsVar( ) . ', \'' . $this->parent->getHtmlId( ) . '\', \'' . $this->parent->getHead( )->getFirst( )->getHtmlId( ) . '\', \'' . $this->ue->getHtmlId( ) . '\', \'' . $this->bt->getHtmlId( ) . '\', ' . $this->ind->getJsVar( ) . ', \''. $this->url . '\', ' . \io\creat\chassis\uicmp\uicmp::toJsArray( $this->params ) . ' );' );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $this->parent->getLayoutJsVar( ) . '.registerTabCb( \'' . $this->parent->getHtmlId( ) . '\', \'onLoad\', ' . $this->ue->getJsVar( ) . '.startup );' );
			
		}
	}
	
	public function getJsVar () { return $this->ue->getJsVar(); }
}

?>