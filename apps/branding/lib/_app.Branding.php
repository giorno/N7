<?php

require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'apps/_app.App.php';

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'apps/_wwg.Spacer.php';

require_once dirname( __FILE__ ) . '/_wwg.License.php';
require_once dirname( __FILE__ ) . '/_wwg.Promotion.php';


/**
 * @file _app.Branding.php
 * @author giorno
 * @package N7
 * @subpackage Branding
 * 
 * @todo "first of blog" feature
 * 
 * Main application creating branding related widgets.
 */
class Branding extends App
{
	/**
	 * Identifier of application instance.
	 */
	const APP_ID = '_app.Branding';
	
	/**
	 * Singleton instance.
	 */
	private static $instance = NULL;
	
	/**
	 * License widget instance.
	 * 
	 * @var <_wwg.License> 
	 */
	private $license = NULL;

	/**
	 * Promotion widget instance.
	 *
	 * @var <_wwg.Promotion>
	 */
	private $promotion = NULL;

	/**
	 * Singleton constructor.
	 */
	protected function __construct( )
	{
		$this->id = self::APP_ID;
		
		$i18n = dirname( __FILE__ ) . '/../i18n/' . n7_globals::lang( ) . '.php';
		if ( file_exists( $i18n ) )
			include $i18n;

		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'APP_BRANDING_MSG', $__msgBranding );

		/**
		 * License information widget.
		 */
		$this->license = new License( );
		$__msgBranding['licServiceName'] = BRANDING_SERVICE;
		$__msgBranding['licProvider'] = BRANDING_PROVIDER;

		/**
		 * Promotion information widget.
		 */
		$this->promotion = new Promotion( );
		$__msgBranding['promoTxt'] = BRANDING_PROMOTEXT;
		$__msgBranding['promoUrl'] = BRANDING_PROMOURL;

		/**
		 * We define our own layout for FOOTER pool.
		 */
		$spacer = new Spacer( );
		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_FOOTER, $spacer->getId( ), $spacer );
		_wwg_registry::getInstance()->setLayout( _wwg_registry::POOL_FOOTER, Array( License::ID, $spacer->getId( ), Promotion::ID ) );
	}

	/**
	 * Singleton interface.
	 */
	public static function getInstance ( )
	{
		if ( is_null ( static::$instance ) )
		{
			static::$instance = new Branding( );
			_app_registry::getInstance( )->register( static::$instance );
		}
		return static::$instance;
	}

	/**
	 * To conform abstract parent.
	 */
	public function exec ( ) { }
	public function icon ( ) { }
	public function event ( $event ) { }

}

?>