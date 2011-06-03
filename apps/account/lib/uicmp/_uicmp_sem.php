<?php

/**
 * @file _uicmp_sem.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 * 
 * Virtual UICMP component rendering settings editor (part of SEM).
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_comp.php';

class _uicmp_sem extends _uicmp_comp
{
	/**
	 * Reference to SEM model instance.
	 *
	 * @var <sem>
	 */
	protected $sem = NULL;

	public function __construct ( &$parent, $id, $sem )
	{
		parent::__construct( $parent );
		$this->type		= __CLASS__;
		$this->id		= $id;
		$this->sem		= $sem;
		$this->renderer	= ACCTAB_UI . 'uicmp/sem.html';
		$this->jsPrefix	= 'uicmp_sem';
	}

	public function getSem ( )
	{
		return $this->sem;
	}

	/**
	 * To conform abstract parent.
	 */
	public function  generateJs ( ) { }
}

?>