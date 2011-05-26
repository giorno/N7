<?php

/**
 * @file sem_collection.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 *
 * Class representing all settings for particular owner, e.g. app.
 */
class sem_collection
{
	/**
	 * Identifier of the collection entity, e.g. application instance ID.
	 *
	 * @var string
	 */
	protected $id = NULL;

	/**
	 * Name of the collection. Usually owner of the collection, e.g.
	 * application. If not set, it is not displayed.
	 *
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * Collection of atoms. Associative array.
	 *
	 * @var array
	 */
	protected $atoms = NULL;

	/**
	 * Constructor.
	 * 
	 * @param string $id identifier of collection (e.g. app name)
	 * @param string $name name to display in UI, empty names are not displayed
	 */
	public function  __construct ( $id, $name = NULL )
	{
		$this->id	= $id;
		$this->name	= $name;
	}

	/**
	 * Registers new atom into the collection.
	 * 
	 * @param sem_atom $atom atom instance
	 */
	public function add ( $atom ) { $this->atoms[$atom->getKey( )] = $atom; }

	/**
	 * Read interface for identifier of collection.
	 * 
	 * @return string
	 */
	public function getId ( ) { return $this->id; }

	/**
	 * Read interface for name of collection.
	 * 
	 * @return string 
	 */
	public function getName ( ) { return $this->name; }

	/**
	 * Iterator interface returning first atom. Returns zero in case of empty
	 * array.
	 * 
	 * @return sem_atom 
	 */
	public function getFirst ( ) { return reset( $this->atoms ); }

	/**
	 * Iterator interface returning next atom. Return zero when reached end of
	 * array.
	 * 
	 * @return sem_atom 
	 */
	public function getNext ( ) { return next( $this->atoms ); }

	/**
	 * Index interface returning atom instance by its identifier.
	 * 
	 * @param string $id identifier of atom
	 * @return sem_atom 
	 */
	public function getById ( $id ) { return $this->atoms[$id]; }
}

?>