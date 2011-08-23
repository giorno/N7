<?php

/**
 * @file _uicmp_ue.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * UICMP component rendering content of User Editor (UE) form.
 */

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

class _uicmp_ue extends \io\creat\chassis\uicmp\uicmp
{
	public function __construct ( &$parent, $id = NULL )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= APP_AI_UI . 'uicmp/ue.html';
		$this->jsPrefix	= '_uicmp_ai_ue_i';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

?>