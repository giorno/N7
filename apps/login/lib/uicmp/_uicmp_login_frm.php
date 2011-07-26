<?php

/**
 * @file _uicmp_login_frm.php
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_layout.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_tab.php';

/**
 * Login form component. Specialization of _uicmp_tab.
 */
class _uicmp_login_frm extends _uicmp_tab
{
	/**
	 * Localization messages for the component. Contains all languages to
	 * support language switching without reloading.
	 * 
	 * @var array 
	 */
	protected $messages = NULL;
	
	/**
	 * Constructor.
	 *
	 * @param _uicmp_layout $parent reference to parent layout
	 * @param string $id component Id
	 * @param string $url URL for login query
	 * @param array $appName base Ajax request parameters
	 */
	public function __construct ( &$parent, $id, $url, $params, &$messages )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= APP_LOGIN_ROOT . 'ui/uicmp/login_frm.html';
		$this->url		= $url;
		$this->params	= $params;
		$this->jsPrefix	= '_uicmp_login_i';
		$this->messages	= $messages;
	}
	
	/**
	 * Method allowing change for renderer. This is intented to provide users
	 * outside the Login app with ability to change way how this widget is
	 * rendered for their own purpose, e.g. public interface.
	 * 
	 * @param string $path path to new renderer template
	 */
	public function setRenderer( $path ) { $this->renderer	= $path; }

	/**
	 * Compose and deliver CSS and Javascript requirements of the component.
	 */
	public function  generateJs ()
	{
		parent::generateJs( );
		$requirer = $this->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_CSS, Array( 'inc/login/_uicmp.css', $this->id ) );
			$requirer->call( _uicmp_layout::RES_JS, Array( 'inc/login/_uicmp.js', $this->id ) );
			$requirer->call( _uicmp_layout::RES_JS, Array( $requirer->getRelative() . 'js/_ajax_req_ad.js', $this->id ) );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->getJsVar() . ' = new _uicmp_login_frm( \'' . $this->getHtmlId( ) . '\', \'' . $this->url . '\', ' . $this->getJsAjaxParams( ) . ', ' . $this->toJsArray( $this->messages ) . ' );' );
			$requirer->call( _uicmp_layout::RES_ONLOAD, $this->getJsVar() . '.startup( );' );
		}
	}
}

?>