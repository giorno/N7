<?php

/**
 * @file n7_url.php
 * @author giorno
 * @package N7
 *
 * URL provider for the solution.
 */
class n7_url
{
	/**
	 * Constants for url schema.
	 */
	const SCH_HTTP	= 'http';
	const SCH_HTTPS	= 'https';

	/**
	 * Constants for mod_rewrite status indication.
	 */
	const MODRW_ON	= 1;
	const MODRW_OFF	= 2;

	/**
	 * http or https schema.
	 *
	 * @var string
	 */
	private $schema = self::SCH_HTTP;

	/**
	 * Indicator of mod_rewrite usage.
	 *
	 * @var int
	 */
	private $mode = self::MODRW_OFF;

	/**
	 * Site and path part of URL.
	 *
	 * @var string
	 */
	private $site = NULL;

	/**
	 * Internal cache for base URL string.
	 *
	 * @var string
	 */
	private $cache = NULL;

	/**
	 * Constructor.
	 *
	 * @param string $site solution site including trailing path
	 * @param string $schema http/https
	 * @param int $mode indicates if Apache mod_rewrite is turned on
	 */
	public function __construct ( $site, $schema = self::SCH_HTTP, $mode = self::MODRW_OFF )
	{
		if ( ( $schema == static::SCH_HTTP ) || ( $schema == static::SCH_HTTPS ) )
			$this->schema = $schema;
		
		if ( ( $mode == static::MODRW_ON ) || ( $mode == static::MODRW_OFF ) )
			$this->mode = $mode;

		$this->site = $site;

		$this->build( );
	}

	/**
	 * Rebuilds cached base URL.
	 */
	private function build ( ) { $this->cache = $this->schema . '://' . $this->site . ( ( substr( $this->site, -1 ) != '/' ) ? '/' : '' ); }

	/**
	 * Provides composed URL string. Either empty (cached one) or with attached
	 * parameters.
	 *
	 * @param string $app application parameter value
	 * @param string $action application action parameter value
	 * @return string 
	 */
	public function myUrl ( $app = NULL, $action = NULL )
	{
		if ( is_null( $app ) )
			return $this->cache;
		else
		{
			switch ( $this->mode )
			{
				case static::MODRW_ON:
					if ( is_null( $action ) )
						return $this->cache . $app . '/';
					else
						return $this->cache . $app . '/' . $action . '/';
				break;

				default:
					if ( is_null( $action ) )
						return $this->cache . '?app=' . $app;
					else
						return $this->cache . '?app=' . $app . '&action=' . $action;
				break;
			}

		}
	}
}

?>