<?php

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * @file _uicmp_sem.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 * 
 * Virtual UICMP component rendering settings editor (part of SEM).
 */
class _uicmp_sem extends \io\creat\chassis\uicmp\uicmp
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

	/**
	 * Read interface for SEM model.
	 * @return type 
	 */
	public function getSem ( ) { return $this->sem; }

	/**
	 * To conform abstract parent.
	 */
	public function  generateReqs ( ) { }
}

?>