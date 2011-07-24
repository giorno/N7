<?php

/**
 * @file _uicmp_login_form.php
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
class _uicmp_login_form extends _uicmp_tab
{
	/**
	 * URL for login query.
	 *
	 * @var <string>
	 */
	protected $url = NULL;

	/**
	 * Ajax server implementation identifier.
	 *
	 * @var <string>
	 */
	protected $appName = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_layout> $parent reference to parent layout
	 * @param <string> $id component Id
	 * @param <string> $url URL for login query
	 * @param <string> $appName identifier of Ajax server implementation
	 */
	public function __construct ( &$parent, $id, $url, $appName )
	{
		parent::__construct( $parent, $id );
		$this->type = __CLASS__;
		$this->renderer = APP_LOGIN_ROOT . 'ui/uicmp/login_form.html';
		$this->url = $url;
		$this->appName = $appName;
	}

	/**
	 * Compose and deliver CSS and Javascript requirements of the component.
	 */
	public function  generateJs ()
	{
		parent::generateJs( );
		
		$this->getRequirer( )->call( _uicmp_layout::RES_CSS, Array( 'inc/login/_uicmp_login_form.css', $this->id ) );
		$this->getRequirer( )->call( _uicmp_layout::RES_JS, Array( 'inc/login/_uicmp_login_form.js', $this->id ) );
		$this->getRequirer( )->call( _uicmp_layout::RES_ONLOAD, '_uicmp_login_startup(\'' . $this->getHtmlId( ) . '\', \'' . $this->url . '\', \'' . $this->appName . '\');' );
	}
}

?>