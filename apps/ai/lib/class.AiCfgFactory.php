<?php

// vim: ts=4

require_once CHASSIS_LIB . 'list/_list_cfg.php';

/**
 * @file class.AiCfgFactory.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Settings abstraction for AI application.
 */
class AiSettings extends _settings
{
	public function __construct ( ) { parent::__construct( _settings::SCOPE_USER, N7_SOLUTION_ID . '.Ai' ); }
}

/**
 * @file class.AiCfgFactory.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Factory providing settings and configuration instances for AI application.
 */
class AiCfgFactory
{
	/**
	 * Width of UID field in Ai lists.
	 */
	const LIST_HDRW_UID		= '96px';
	
	/**
	 * Width of login field in Ai lists.
	 */
	const LIST_HDRW_LOGIN	= '*';
	/**
	 * Width of UID field in Ai lists.
	 */
	const LIST_HDRW_ADDRESS	= '320px';
	
	/**
	 * Width of icon/action field in Ai lists.
	 */
	const LIST_HDRW_ICON	= '16px';
	
	/**
	 * Execution sequence field in AT.
	 */
	const LIST_HDRW_SEQ		= '32px;';
	
	/**
	 * App ID in AT.
	 */
	const LIST_HDRW_ID		= '240px;';
	
	/**
	 * App folder in AT.
	 */
	const LIST_HDRW_FSNAME	= '240px;';

	const LIST_HDRW_APPVER	= '64px';

	const LIST_HDRW_APPID	= '160px';

	const LIST_HDRW_APPPATH	= self::LIST_HDRW_APPID;
	
	/**
	 * Cache used for created instances.
	 *
	 * @var array
	 */
	protected static $cfgs = NULL;

	/**
	 * Singleton instance.
	 * @var AiSettings 
	 */
	protected static $instance = NULL;

	/**
	 * Hide constructors in accordance to Singleton guidelines.
	 */
	private function  __construct ( ) { }
	private function  __clone ( ) { }

	/**
	 * Settings singleton interface.
	 * 
	 * @return AiSettings 
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new AiSettings ( );

		return static::$instance;
	}

	/**
	 * List configuration isntances factory method.
	 * 
	 * @param string $key identifier of clist configuration
	 * @return _list_cfg 
	 */
	public static function getCfg ( $key )
	{
		if ( !is_array( static::$cfgs ) || !array_key_exists( $key, static::$cfgs ) )
			static::$cfgs[$key] = new _list_cfg ( static::getInstance( ), $key );
		
		return static::$cfgs[$key];
	}
}

?>
