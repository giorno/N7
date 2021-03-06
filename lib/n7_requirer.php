<?php


require_once CHASSIS_LIB . '_requirer.php';
require_once CHASSIS_LIB . 'uicmp/layout.php';
require_once CHASSIS_LIB . 'apps/_app_registry.php';

/**
 * @file n7_requirer.php
 * @author giorno
 * @package N7
 *
 * Specialization of _requirer for usage in the solution. Singleton interface.
 */
class n7_requirer extends _requirer
{
	/**
	 * Singleton instance.
	 * 
	 * @var n7_requirer
	 */
	private static $instance = NULL;

	/**
	 * Exception from Singleton pattern, public constructor as in parent.
	 */
	public function  __construct ( )
	{
		parent::__construct( __CLASS__, 'inc/chassis/' );
		
		$registry = _app_registry::getInstance( );
		$this->setCb( \io\creat\chassis\uicmp\vlayout::RES_JS,		Array( &$registry, 'requireJs' ) );
		$this->setCb( \io\creat\chassis\uicmp\vlayout::RES_CSS,		Array( &$registry, 'requireCss' ) );
		$this->setCb( \io\creat\chassis\uicmp\vlayout::RES_ONLOAD,	Array( &$registry, 'requireOnLoad' ) );
		$this->setCb( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN,	Array( &$registry, 'requireJsPlain' ) );
		$this->setCb( \io\creat\chassis\uicmp\vlayout::RES_BODYCHILD,	Array( &$registry, 'requireBodyChild' ) );
	}

	/**
	 * Singleton interface.
	 * 
	 * @return n7_requirer
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new static( );

		return static::$instance;
	}
}

?>