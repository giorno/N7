<?php

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * @file _uicmp_chpass.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * 
 * Form component for change password feature.
 */
class _uicmp_chpass extends \io\creat\chassis\uicmp\uicmp
{
	/**
	 * Constructor.
	 * 
	 * @param _uicmp_body $parent reference to parent oomponent
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id = NULL )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= ACCTAB_UI . 'uicmp/chpass.html';
		$this->jsPrefix	= 'uicmp_chpass';
	}
	
	/**
	 * To conform abstract parent.
	 */
	public function  generateReqs ( ) { }
}

?>