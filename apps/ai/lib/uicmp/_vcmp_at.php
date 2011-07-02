<?php

/**
 * @file _vcmp_at.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_cnt.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_buttons.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi.php';

/**
 * Virtual component creating UI for Applications Table: tab displaying
 * applications and tab displaying details of single app. This VCMP is therefore
 * required to be registered within layer component.
 */
class _vcmp_at extends _vcmp_comp
{
	/**
	 * Identifier of class instance. Used to generate visual components ID's.
	 * 
	 * @var string 
	 */
	protected $id = NULL;
	
	/**
	 * Ajax request URL.
	 * 
	 * @var string 
	 */
	protected $url = NULL;
	
	/**
	 * Ajax request parameters.
	 * 
	 * @var array 
	 */
	protected $params = NULL;
	
	/**
	 * Localization messages. Reference to app localization array.
	 * 
	 * @var array
	 */
	protected $messages = NULL;
	
	/**
	 * Tab displaying list of applications.
	 * 
	 * @var _uicmp_tab 
	 */
	protected $tab = NULL;
	
	/**
	 * Indicator component for list of applications.
	 * 
	 * @var _uicmp_gi_ind
	 */
	protected $ind = NULL;
	
	/**
	 * Container component for list of applications.
	 * 
	 * @var _uicmp_srch_cnt
	 */
	protected $cnt = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param _uicmp_layer $parent layer instance
	 */
	public function __construct ( &$parent, $id, $url, $params, &$messages )
	{
		parent::__construct( $parent );
		$this->id		= $id;
		$this->messages	= $messages;
		$this->jsPrefix	= '_uicmp_at';
		$this->url		= $url;
		$this->params	= $params;
		
		/**
		 * Tab to display list of applications.
		 */
		$this->tab = $this->parent->createTab( $this->id . '.List' );
			$this->tab->createFold( $this->messages['at']['fold'] );
			$this->tab->getHead( )->add( new _uicmp_title( $this->tab->getHead( ), $this->tab->getId( ) . '.Title', $this->messages['at']['title'] ) );
			$this->tab->getHead( )->add( $buttons = new _uicmp_buttons( $this->tab->getHead( ), $this->tab->getId( ) . '.Buttons' ) );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.Refresh', _uicmp_gi::IT_A, $this->messages['at']['refresh'], $this->getJsVar( ) . '.list( );', '_uicmp_gi_refresh _uicmp_blue_b' ) );
				$this->ind = new _uicmp_gi_ind( $buttons, $buttons->getId( ) . '.Ind', _uicmp_gi::IT_IND, $this->messages['at']['i'] );
					$buttons->add( $this->ind );
					
			$this->tab->getBody( )->add( $this->cnt = new _uicmp_srch_cnt( $this->tab->getBody( ), $this->tab->getBody( )->getId( ) . '.Cnt' ) );
			$this->tab->getBody( )->add( $gap = new _uicmp_buttons( $this->tab->getBody( ), $this->tab->getBody( )->getId( ) . '.Gap' ) );
				$gap->add( new _uicmp_gi( $gap, $gap->getId( ) . '.Dummy', _uicmp_gi::IT_TXT, '' ) );
	}
	
	/**
	 * Generates requirements.
	 */
	public function generateJs()
	{
		$requirer = $this->parent->getRequirer( );

		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->getJsVar( ) . ' = new _uicmp_at( \''. $this->getJsVar( ) .'\', \''. $this->cnt->getHtmlId( ) .'\', \'' . $this->url . '\', ' . $this->generateJsArray( $this->params ) .  ', ' . $this->ind->getJsVar( ) . ' );' );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, $this->parent->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onShow\', ' . $this->getJsVar( ) . '.list );' );
		}
	}
}

?>