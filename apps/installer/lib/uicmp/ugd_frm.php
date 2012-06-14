<?php

// vim: ts=4

/**
 * @file udg_frm.php
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7\installer;

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * UICMP form rendering upgrade UI.
 */
class ugd_frm extends \io\creat\chassis\uicmp\uicmp
{
	/**
	 * Constructor.
	 * @param \io\creat\chassis\uicmp\body $parent parent element
	 * @param string $id unique component identifier
	 */
	public function __construct( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= INSTALLER_UI . 'uicmp/ugd_frm.html';
	}
	
	/**
	 * Registers resources requirements.
	 */
	public function  generateReqs ( )
	{
		/**
		 * User login application stylesheet for special purposes.
		 */
		$this->getRequirer( )->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( 'inc/login/_uicmp.css', $this->id ) );
	}
}

?>