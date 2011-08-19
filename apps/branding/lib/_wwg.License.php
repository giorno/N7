<?php


require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

/**
 * @file _wwg.License.php
 * @author giorno
 * @package N7
 * @subpackage Branding
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Widget displaying legal information about the product.
 *
 * @todo create AUTHORS badge displaying contributors
 */
class License extends Wwg
{
	const ID = '_wwg.License';

	public function __construct ( )
	{
		$this->id = static::ID;
		$this->template = dirname(__FILE__) . '/../templ/_wwg.License.html';

		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
			$smarty->assign( 'WWG_LICENSE_BADGE', dirname( __FILE__ ) . '/../templ/_wwg.License.Apache2.' . n7_globals::lang() . '.html' );

		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_FOOTER, $this->id, $this );
		_app_registry::getInstance( )->requireJs( 'inc/branding/_wwg.License.js', $this->id );
		_app_registry::getInstance( )->requireCss( 'inc/branding/_wwg.License.css', $this->id );
		_app_registry::getInstance( )->requireBodyChild( CHASSIS_UI . '_wdg.html', $this->id );
		_app_registry::getInstance( )->requireBodyChild( dirname( __FILE__ ) . '/../templ/_wwg.License.dlg.html', $this->id );
		_app_registry::getInstance( )->requireOnLoad( '_wwgLicenseStartup();' );
	}
}

?>