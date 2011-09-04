<?php

/**
 * @file sem.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/vcmp.php';

/**
 * Settings Editor Model (SEM). Its template generates HTML of editor and
 * provides data for its UI elements.
 */
class sem
{
	/**
	 * All managed collections.
	 *
	 * @var array
	 */
	protected $collections = NULL;

	/**
	 * Constructor.
	 */
	public function __construct ( ) { }

	/**
	 * Registers new collection subtree.
	 * 
	 * @param sem_collection $collection 
	 */
	public function addCollection ( $collection ) { $this->collections[] = $collection; }

	/**
	 * Iterator interface returning first collection.
	 * 
	 * @return sem_collection
	 */
	public function getFirst ( ) { return reset( $this->collections ); }
	
	/**
	 * Iterator interface returning next collection. Returns null on failure or
	 * reading beyond the collections array length.
	 * 
	 * @return sem_collection
	 */
	public function getNext ( ) { return next( $this->collections ); }

}

?>