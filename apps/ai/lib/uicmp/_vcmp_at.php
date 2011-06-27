<?php

/**
 * @file _vcmp_at.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';

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
	 * Constructor.
	 * 
	 * @param _uicmp_layer $parent layer instance
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent );
		$this->id = $id;
		
		$listTab = $this->parent->createTab( $this->id . '.List' );
	}
	
	
}

?>