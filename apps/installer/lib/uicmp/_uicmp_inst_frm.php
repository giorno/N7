<?php

require_once CHASSIS_LIB . 'uicmp/_uicmp_comp.php';

/**
 * @file _uicmp_inst_frm.php
 * @author giorno
 * @package N7
 * 
 * UICMP form rendering UI of installer.
 */
class _uicmp_inst_frm extends _uicmp_comp
{
	/**
	 * List of timezones.
	 * 
	 * @var array 
	 */
	protected $zones = NULL;
	
	public function __construct( &$parent, $id, $zones )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->zones	= $zones;
		$this->renderer	= INSTALLER_UI . 'uicmp/inst_frm.html';
	}
	
	/**
	 * Registers resources requirements.
	 */
	public function  generateJs ( )
	{
		/**
		 * User login application stylesheet for special purposes.
		 */
		$this->getRequirer( )->call( _uicmp_layout::RES_CSS, Array( 'inc/unsigned/_uicmp_login_form.css', $this->id ) );
	}
	
	/**
	 * Returns list of available timezones.
	 * 
	 * @return array 
	 */
	public function getZones ( ) { return $this->zones; }
	
	/**
	 * Return actual timezone. For install it should be Europe/Brussels.
	 * 
	 * @return string 
	 */
	public function getZone ( ) { return date_default_timezone_get( ); }
}

?>