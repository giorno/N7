
/**
 * @file _uicmp.js
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires prototype.js
 * @requires _ajax_req_ad.js
 */

/**
 * Javascript logic for installer components. This file is supposed to be
 * embedded directly into <HEAD> section, not linked.
 */
inst_ctrl.prototype = new Object( );
inst_ctrl.prototype.constructor = inst_ctrl;
function inst_ctrl ( form_id, bt_id, ind, app )
{
	// Copy scope.
	var me = this;
	
	// Root of form controls ID strings.
	this.form_id = form_id;
	
	// HTML ID of button.
	this.bt_id = bt_id;
	
	// Indicator instance.
	this.ind = ind;
	
	// Application identifier.
	this.app = app;
}

/**
 * Prepares froomPrefills data for server URL, etc.
 */
inst_ctrl.prototype.startup = function ( )
{
	var loc = window.location.toString();
	var schema = ( loc.charAt( 4 ) == 's') ? 'https' : 'http' ;
	var path = loc.substr( ( schema == 'https' ) ? 8 : 7 );
	this.focus( );
		
	document.getElementById( this.form_id + '.path' ).value = path.substr( 0, path.lastIndexOf( '/' ) + 1 ); // there might be some garbage at the end, e.g. ?app=_app.SomeApp
	document.getElementById( this.form_id + '.schema' ).selectedIndex = ( schema == 'https' ) ? 1 : 0;
}
	
/**
 * Disables controls for the duration of installation.
 * 
 * @param disabled desired state
 */
inst_ctrl.prototype.disable = function ( disabled )
{
	document.getElementById( this.bt_id ).disabled = disabled;
	document.getElementById( this.form_id + '.schema' ).disabled = disabled;
	document.getElementById( this.form_id + '.path' ).disabled = disabled;
	document.getElementById( this.form_id + '.modrw' ).disabled = disabled;
	document.getElementById( this.form_id + '.tz' ).disabled = disabled;
	document.getElementById( this.form_id + '.login' ).disabled = disabled;
	document.getElementById( this.form_id + '.email' ).disabled = disabled;
	document.getElementById( this.form_id + '.password' ).disabled = disabled;
};
	
/**
 * Put cursor into root login <INPUT> element.
 */
inst_ctrl.prototype.focus = function ( ) {document.getElementById( this.form_id + '.login' ).focus( );};
	
/**
 * Send Ajax request with data to perform installation. Display returned UI
 * content.
 */
inst_ctrl.prototype.install = function ( )
{
	this.disable( true );
	this.ind.show( 'executing', '_uicmp_ind_gray' );
	
	// Collect data.
	var schema = ( document.getElementById( this.form_id + '.schema' ).selectedIndex == 1 ) ? 'https' : 'http' ;
	var path = document.getElementById( this.form_id + '.path' ).value;
	var modrw = ( document.getElementById( this.form_id + '.modrw' ).checked ) ? '1' : '2' ;
	var tz = document.getElementById( this.form_id + '.tz' ).options[document.getElementById( this.form_id + '.tz' ).selectedIndex].value;
		
	var login = document.getElementById( this.form_id + '.login' ).value;
	var password = document.getElementById( this.form_id + '.password' ).value;
	var email = document.getElementById( this.form_id + '.email' ).value;
		
	// Copy scope.
	var scope = this;
		
	// Request parameters.
	var reqParams = { app: this.app,
					action: 'install',
					schema: schema,
					site: path,
					modrw: modrw,
					tz: tz,
					login: login,
					password: password,
					email: email };
	
	function onCreate ( ) { }
	function onFailure ( ) { scope.disabled( false ); scope.ind.show( 'e_unknown', '_uicmp_ind_red' ); }
	function onSuccess ( data )
	{
		alert(data.responseText);
		if (data.responseText.substr( 0, 9 ) == 'OK' )
			scope.ind.show( 'done', '_uicmp_ind_green' );
		else
		{
			scope.disable( false );
			if (scope.ind.messages[data.responseText])
				scope.ind.show( data.responseText, '_uicmp_ind_red' );
			else
				scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
											
			scope.focus( );
		}
	}
	
	var ajax = new _ajax_req_ad( false, ( ( schema == 'http' ) ? 'http://' : 'https://'  ) + path + 'ajax.php', reqParams );
	ajax.send( null, { onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess }, null, false );
};


