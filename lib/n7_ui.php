<?php

require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_layout.php';

require_once N7_SOLUTION_LIB . 'wwg/_wwg.Menu.php';

/**
 * @file n7_ui.php
 * @author giorno
 * @package N7
 * 
 * Provider of UI widgets instances.
 */
class n7_ui
{
	/**
	 * Instance of UICMP layout.
	 * 
	 * @var _uicmp_layout 
	 */
	protected $layout = NULL;
	
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
			$this->layout = new _uicmp_layout ( n7_requirer::getInstance( ) );
		
		return $this->layout;
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
}

?>