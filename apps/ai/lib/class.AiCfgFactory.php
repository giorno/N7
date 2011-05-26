<?php

/**
 * @file class.AiCfgFactory.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Settings and configuration instances for AI application.
 */

class AiSettings extends _settings
{
	public function __construct ( ) { parent::__construct( _settings::SCOPE_USER, N7_SOLUTION_ID . '.Ai' ); }
}

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
	 * Cache used for created instances.
	 *
	 * @var <array>
	 */
	protected static $cfgs = NULL;

	protected static $instance = NULL;

	private function  __construct ( ) { }
	private function  __clone ( ) { }

	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new AiSettings ( );

		return static::$instance;
	}

	public static function getCfg ( $key )
	{
		if ( !is_array( static::$cfgs ) || !array_key_exists( $key, static::$cfgs ) )
			static::$cfgs[$key] = new _list_cfg ( static::getInstance( ), $key );
		
		return static::$cfgs[$key];
	}
}

?>