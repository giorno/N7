<?php

/**
 * @file _uicmp_ue.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * UICMP component rendering content of User Editor (UE) form.
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_comp.php';

class _uicmp_ue extends _uicmp_comp
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
	public function generateJs ( ) { }

	/**
	 * Hack to allow virtual component _vcmp_cpe to extract requirer
	 * object from _uicmp_layout instance.
	 *
	 * @return <_requirer>
	 */
	//public function getRequirer ( ) { return parent::getRequirer( ); }
}

?>