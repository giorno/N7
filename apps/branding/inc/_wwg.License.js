
/**
 * @file _wwgLicense.js
 * @author giorno
 * @package N7
 * @subpackage Branding
 * 
 * @requires _sd.js
 * @required _srv.js
 */

var _wwgLicenseSd	= null;
var _wwgLicenseWdg	= null;

function _wwgLicenseStartup ( )
{
	_wwgLicenseSd	= new _sd_dome( 'wwgLicenseSd' );
	_wwgLicenseWdg	= new _sd_simple_ctrl( _wwgLicenseSd, 'wwgLicenseDlg' );
}

function _wwgLicenseDisplay ( )
{
	_wwgLicenseWdg.show( );
	document.getElementById( 'wwgLicenseDlgBt' ).focus( );
	window.scroll( 0,0 );
}


function _wwgLicenseClose ( )
{
	_wwgLicenseWdg.hide( );
}
