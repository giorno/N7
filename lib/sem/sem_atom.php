<?php

/**
 * @file sem_atom.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 *
 * Model of settings atom, de-facto single setting, for Settings Editor Model
 * (SEM).
 */
class sem_atom
{
	/**
	 * Atom type of discrete values.
	 */
	const AT_SELECT	= 1;

	/**
	 * Text value atom type.
	 */
	const AT_TEXT	= 2;

	/**
	 * Defines type of atom. See AT_* member constants for details.
	 *
	 * @var int
	 */
	protected $type = NULL;

	/**
	 * String to display as atom name.
	 *
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * Identifier in settings table and namespace.
	 *
	 * @var string
	 */
	protected $key = NULL;

	/**
	 * Informative messages (e.g. purpose of the atom).
	 * 
	 * @var string
	 */
	protected $hint = NULL;
	
	/**
	 * Subparticles of atoms, e.g. single setting can be composed from two or
	 * more control elements. If this array has more than one entry, atom value
	 * is supposed to be written into database as serialized associative array.
	 *
	 * First member is always reference to itself. This array is used to
	 * generate control elements.
	 * 
	 * @var array
	 */
	protected $particles = NULL;

	/**
	 * Current value of the atom.
	 *
	 * @var mixed
	 */
	protected $value = NULL;

	/**
	 * Associative array or discrete values for selectable atom.
	 * 
	 * @var type
	 */
	protected $values = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param int $type see member constants for description
	 * @param string $key identifier of atom in the collection
	 * @param mixed $value value of atom
	 * @param string $name name to be displayed in UI
	 * @param string $desc description flag, can be used by UI
	 */
	public function __construct ( $type, $key, $value, $name, $desc = '' )
	{
		$this->type		= $type;
		$this->key		= $key;
		$this->value	= $value;
		$this->name		= $name;
		$this->desc		= $desc;
		$this->addParticle( $this );
	}

	/**
	 * Registers subatom. Each atom always contains reference to itself as first
	 * subatom.
	 * 
	 * @param sem_atom $atom 
	 */
	public function addParticle ( $atom ) { $this->particles[$atom->key] = $atom; }

	/**
	 * Usable only for AT_SELECT type.
	 * 
	 * @param mixed $value value of the option
	 * @param string $name name to be displayed in UI
	 */
	public function addOption ( $value, $name ) { $this->values[$value] = $name; }

	/**
	 * Read interface for type information.
	 * 
	 * @return int 
	 */
	public function getType ( ) { return $this->type; }

	/**
	 * Read interface for name.
	 * 
	 * @return string
	 */
	public function getName ( ) { return $this->name; }
	
	/**
	 * Read interface for value.
	 * 
	 * @return mixed
	 */
	public function getValue ( ) { return $this->value; }

	/**
	 * Read interface for key identifier.
	 * 
	 * @return string
	 */
	public function getKey ( ) { return $this->key; }

	/**
	 * Read interface for description field.
	 * 
	 * @return string
	 */
	public function getHint ( ) { return $this->desc; }

	/**
	 * Iterator interface returning first subatom.
	 * 
	 * @return sem_atom
	 */
	public function getFirst ( ) { return reset( $this->particles); }
	
	/**
	 * Iterator interface returning next subatom. NULL after all particles have
	 * been read.
	 * 
	 * @return sem_atom
	 */
	public function getNext ( ) { return next( $this->particles); }

	/**
	 * Read interface returning associative array of options. Applicable only
	 * for AT_SELECT type.
	 * 
	 * @return array
	 */
	public function getOptions( ) {	 return $this->values; }

	/**
	 * Serialization interface. Single particle atoms values are returned as
	 * scalars, otherwise as serialized structures.
	 * 
	 * @return mixed 
	 */
	public function get (  )
	{
		/**
		 * Return plain value for atom without subparticles.
		 */
		if ( count( $this->particles ) <= 1 )
			return $this->value;
		else	// otherwise serialize
		{
			foreach ( $this->particles as $key => $atom)
				$plain[$key] = $atom->getValue;

			return serialize( $plain );
		}
	}

}

?>