<?php

/**
 * @file n7_globals.php
 * @author giorno
 *
 * Specialization of framework class to provide solution wide global variables.
 * Global in this context means solution scope, not scope of settings database
 * table.
 */

require_once CHASSIS_LIB . 'i18n/i18n.php';

require_once CHASSIS_3RD . 'libip2country.php';

require_once CHASSIS_LIB . 'session/repo.php';
require_once CHASSIS_LIB . 'session/session.php';
require_once CHASSIS_LIB . 'session/authbe.php';
require_once CHASSIS_LIB . 'session/_settings.php';

require_once N7_SOLUTION_LIB . 'libtz.php';
require_once N7_SOLUTION_LIB . 'n7_config.php';
require_once N7_SOLUTION_LIB . 'n7_settings.php';
require_once N7_SOLUTION_LIB . 'n7_timezone.php';
require_once N7_SOLUTION_LIB . 'n7_url.php';

class n7_globals extends io\creat\chassis\session\repo
{
	/**
	 * Name of cookie carrying HTTP session language code. Cookie name may not
	 * contain dots.
	 */
	const COOKIE_LANG = 'io_creat_n7_lang';
	
	/**
	 * Constructor. Called from Singleton interface. Initializes PDO instance.
	 */
	protected function __construct ( )
	{
		try
		{
			$pdo = new PDO(	"mysql:host=" . N7_MYSQL_HOST . ";dbname=" . N7_MYSQL_DB,
							N7_MYSQL_USER,
							N7_MYSQL_PASS );

			$this->set( self::PDO, $pdo );
		}
		catch ( PDOException $e )
		{
			// raise error to the UI/ajax superstruct and disable further processing
		}
	}
	
	//Called from Singleton interface. Initializes storage.
	protected function post( )
	{
		/**
		 * Database connection is required for accessing configuration.
		 * @deprecated
		 */
		_db_connect( N7_MYSQL_HOST, N7_MYSQL_USER, N7_MYSQL_PASS, N7_MYSQL_DB );

		/**
		 * Loading configuration and setting database connection timezone.
		 *
		 * @todo rethink about setting connection timezone using query to do it
		 * completely within SQL
		 */
		$this->storage['config'] = new n7_config( );
		$serverTz = $this->storage['config']->get( 'server.tz' );
		
		/**
		 * During the installation we need to use n7_ui singleton, which uses
		 * this one, but as there are not database tables, some configurations
		 * are missing. It also serves as fallback value.
		 */
		if ( is_null( $serverTz ) )
			$serverTz = 'Europe/Brussels';
		
		_tz_setsqltz( $serverTz );
		
		/*
		 * Global variable containing codes and names of languages for
		 * displaying. This is suposed to be changed only during development.
		 */
		$this->storage['languages'] = Array( 'cs' => 'Čeština', 'en' => 'English', 'sk' => 'Slovenčina' );

		/**
		 * Instance providing links.
		 */
		$this->storage['url'] = new n7_url( $this->storage['config']->get( 'server.url.site' ),
											$this->storage['config']->get( 'server.url.scheme' ),
											$this->storage['config']->get( 'server.url.modrw' ) );


		/**
		 * Configure globals for signed user.
		 */
		if ( \io\creat\chassis\session::getInstance( )->isSigned( ) )
		{
			$this->storage['settings'] = new n7_settings( );
			$this->storage['usr.lang'] = $this->storage['settings']->get( 'usr.lang' );
			$this->storage['usr.tz'] = new n7_timezone( $this->storage['settings']->get( 'usr.tz' ) );
		}

		/**
		 * Set server timezone.
		 */
		$this->storage['server.tz'] = new n7_timezone( $serverTz );
		date_default_timezone_set( $serverTz );
		
		/**
		 * Language is not set in user settings. Let's try browser preferences.
		 */
		if ( ( !array_key_exists( 'usr.lang', $this->storage ) ) || ( !array_key_exists( $this->storage['usr.lang'], $this->storage['languages'] ) ) )
		{
			/**
			 * Language is not provided in the HTTP cookie.
			 */
			if ( !$this->langFromCookie( ) )
			{
				/**
				 * There was nothing useful in browser settings. Let's use IP address.
				 */
				if ( !$this->langFromBrowser( ) )
				{
					/**
					 * Unable to retrieve language from IP address. Fallback to
					 * English.
					 */
					if ( !$this->langFromIp( ) )
						$this->storage['usr.lang'] = 'en';
				}
			}
		}

		/**
		 * Still no language. Let's derive it from IP address.
		 */
		if ( !array_key_exists( $this->storage['usr.lang'], $this->storage['languages'] ) )
			$this->langFromIp( );
		
		/**
		 * Configure Chassis framework localization Singleton.
		 */
		$this->storage['io.creat.chassis.i18n'] = i18n::getInstance( $this->storage['usr.lang'] );
	}

	/**
	 * Tries to load language preferences from the browser supplied list of
	 * acceptable content languages.
	 * @return bool
	 */
	private function langFromBrowser ( )
	{
		if ( !array_key_exists( 'HTTP_ACCEPT_LANGUAGE', $_SERVER ) )
			return;
		
		/*
		 * Browser preferrence (Accept-Language header). This is only very simple
		 * solution. For best fit also quality fragment and full normalized language
		 * codes should be checked.
		 */
		$languages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		if ( is_array( $languages ) )
		{
			foreach ( $languages as $lang )
			{
				$frags = explode( ';', $lang );

				if ( is_array( $frags ) )
				{
					$code = strtolower( substr( $frags[0], 0, 2 ) );

					if ( array_key_exists( $code, $this->storage['languages'] ) )
					{
						$this->storage['usr.lang'] = $code;
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Tries to load language from IP address of the client.
	 * @return bool
	 */
	private function langFromIp ( )
	{
		$country = iptocountry( $_SERVER['REMOTE_ADDR'] );

		switch ( $country )
		{
			case 'us':
			case 'gb':
				$this->storage['usr.lang'] = 'en';
				return true;
			break;

			case 'sk':
				$this->storage['usr.lang'] = 'sk';
				return true;
			break;

			case 'cs':
				$this->storage['usr.lang'] = 'cs';
				return true;
			break;
		}
		return false;
	}
	
	/**
	 * Tries to retrieve specific language from HTTP cookies.
	 * @return bool 
	 */
	private function langFromCookie ( )
	{
		if ( array_key_exists( self::COOKIE_LANG, $_COOKIE ) )
			$code = $_COOKIE[self::COOKIE_LANG];
		else
			return false;
		
		if ( array_key_exists( $code, $this->storage['languages'] ) )
		{
			$this->storage['usr.lang'] = $code;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Lazy instantiation of authentication backend plugin. It is needed only
	 * in specific cases (login, administration UI rendering, etc.), therefore
	 * no need to have it created for each request.
	 * @return \io\creat\chassis\authbe
	 */
	public function authbe ( )
	{
		$abe = $this->get( 'server.authbe' ); // this is not a setting, but a key to globals storage array
		
		if ( !( $abe instanceof \io\creat\chassis\authbe ) )
		{
			// read global scope setting for the authentication plugin location
			$lib = N7_SOLUTION_LIB . 'auth/' . trim( $this->storage['config']->get( 'server.auth.lib' ) ) . '.php';
			if ( ( $lib != '' ) && file_exists( $lib ) )
			{
				include $lib; // should populate globals
				$abe = $this->get( 'server.authbe' ); // this is not a setting, but a key to globals storage array
				\io\creat\chassis\session::getInstance( )->setAuthBe( $abe ); // link to session tracker
				return $abe;
			}
			return NULL; 
		}
		
		return $abe;
	}

	/**
	 * Shortcut for accesing settings instance.
	 * @return n7_settings
	 */
	public static function settings ( ) { return static::getInstance( )->get( 'settings' ); }

	/**
	 * Shortcut for accesing language code for request/session language.
	 * @return string
	 */
	public static function lang ( ) { return static::getInstance( )->get( 'usr.lang' ); }

	/**
	 * Shortcut for accesing array of supported localizations.
	 * @return array
	 */
	public static function languages ( ) { return static::getInstance( )->get( 'languages' ); }

	/**
	 * Shortcut for accesing user timezone instance. May be NULL for unsigned
	 * user.
	 * @return n7_timezone
	 */
	public static function userTz ( ) { return static::getInstance( )->get( 'usr.tz' ); }

	/**
	 * Shortcut for accesing server timezone instance.
	 * @return n7_timezone
	 */
	public static function serverTz ( ) { return static::getInstance( )->get( 'server.tz' ); }
}

?>