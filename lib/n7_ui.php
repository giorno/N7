<?php

/**
 * @file n7_ui.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'uicmp/layout.php';
require_once CHASSIS_LIB . 'uicmp/dialogs.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';

require_once N7_SOLUTION_LIB . 'wwg/_wwg.Menu.php';
require_once N7_SOLUTION_LIB . 'wwg/_wwg.MenuItem.php';

/**
 * Provider of UI widgets instances.
 */
class n7_ui
{
	/**
	 * Instance of UICMP layout.
	 * 
	 * @var layout 
	 */
	protected $layout = NULL;
	
	/**
	 * Instance of UICMP layout for dynamic dialogs.
	 * 
	 * @var dialogs
	 */
	protected $dlgs = NULL;
	
	/**
	 * Instance of Menu widget.
	 * 
	 * @var Menu 
	 */
	protected $menu = NULL;
	
	/**
	 * Singleton instance.
	 * 
	 * @var n7_ui
	 */
	protected static $instance = NULL;
	
	/**
	 * Hide constructor implementation.
	 */
	protected function __construct ( ) { }
	
	/**
	 * Singleton interface.
	 * 
	 * @return n7_ui 
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new static( );
		
		return static::$instance;
	}
	
	/**
	 * Provider of _uicmp_layout instance.
	 * 
	 * @todo use of language setting
	 * 
	 * @return _uicmp_layout 
	 */
	public function getLayout ( )
	{
		if ( is_null( $this->layout ) )
			$this->layout = new \io\creat\chassis\uicmp\layout( n7_requirer::getInstance( ), n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
		
		return $this->layout;
	}
	
	/**
	 * Provider of _uicmp_dlgs instance.
	 * 
	 * @todo use of language setting
	 * 
	 * @return _uicmp_dlgs 
	 */
	public function getDlgs ( )
	{
		if ( is_null( $this->dlgs ) )
			$this->dlgs = new \io\creat\chassis\uicmp\dialogs( n7_requirer::getInstance( ), n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
		
		return $this->dlgs;
	}
	
	/**
	 * Provider of Menu widget instance.
	 * 
	 * @return Menu 
	 */
	public function getMenu ( )
	{
		if ( is_null( $this->menu ) )
		{
			$this->menu = new Menu( );
			_wwg_registry::getInstance( )->register( _wwg_registry::POOL_MENU, $this->menu->getId( ), $this->menu );
		}
		
		return $this->menu;
	}
	
	/**
	 * Perform pre-prender phase actions. Initializes layouts.
	 */
	public function preRender ( )
	{
		/**
		 * This order is important as static UI may depend on dynamic.
		 */
		$this->getDlgs( );
		$this->getLayout( );
		
		$this->dlgs->init( );
		$this->layout->init( );
			
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_UICMP_DLGS', $this->dlgs );
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_UICMP_LAYOUT', $this->layout );
	}
}

?>