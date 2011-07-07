<?php

/**
 * @file __wwg.Menu.php
 * @author giorno
 * @package N7
 * @subpackage WWg
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';

/**
 * Object representing group of MenuItem widgets.
 */
class Menu extends Wwg
{
	/*
	 * Default id used for the widget when no ID is specified in constructor.
	 */
	const DEFAULT_ID = '_wwg.Menu';

	/**
	 * Internal storage for menu items.
	 * 
	 * @var array
	 */
	private $items = NULL;

	/**
	 * Constructor.
	 *
	 * @param string $id widget id
	 */
	public function __construct ( $id = NULL )
	{
		if ( !is_null( $id ) )
			$this->id = $id;
		else
			$this->id = static::DEFAULT_ID;
		
		$this->template = N7_SOLUTION_ROOT . 'smarty/templates/_wwg.Menu.html';
		n7_requirer::getInstance()->call( _vcmp_layout::RES_CSS, array( n7_requirer::getInstance()->getRelative() . 'css/_uicmp.css' , __CLASS__ ) );

		$this->type = __CLASS__;
	}

	/**
	 * Registers new menu item.
	 *
	 * @param MenuItem $item menu item widget
	 */
	public function register( &$item )
	{
		$this->items[$item->getId( )] = $item;
	}

	/**
	 * Returns first item and resets pointer to beginning of array.
	 *
	 * @return MenuItem
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->items ) )
			return reset( $this->items );
		
		return NULL;
	}

	/**
	 * Move pointer forwards and returns next item.
	 *
	 * @return MenuItem
	 */
	public function getNext ( )
	{
		if ( is_array( $this->items ) )
			return next( $this->items );

		return NULL;
	}
}

?>