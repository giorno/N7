
// vim: ts=4

/**
 * @file ugd.js
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Javascript logic for Upgrader client. This file is supposed to be
 * embedded directly into <HEAD> section, not linked.
 */

ugd_ctrl.prototype = new Object( );
ugd_ctrl.prototype.constructor = ugd_ctrl;
function ugd_ctrl ( form_id, bt_id, ind, url, params )
{	
	// Root of form controls ID strings.
	this.form_id = form_id;
	
	// HTML ID of button.
	this.bt_id = bt_id;
	
	// Indicator instance.
	this.ind = ind;
	
	// Ajax request adaptor instance.
	this.ajax = new _ajax_req_ad( false, url, params );
}

/**
 * Focus username input field.
 */
ugd_ctrl.prototype.startup = function ( ) { this.focus( ); }

/**
 * Disables controls for the duration of installation.
 * @param disabled desired state
 */
ugd_ctrl.prototype.disable = function ( disabled )
{
	document.getElementById( this.bt_id ).disabled = disabled;
	document.getElementById( this.form_id + '.login' ).disabled = disabled;
	document.getElementById( this.form_id + '.password' ).disabled = disabled;
}
	
/**
 * Put cursor into root login <INPUT> element.
 */
ugd_ctrl.prototype.focus = function ( ) { document.getElementById( this.form_id + '.login' ).focus( ); }
	
/**
 * Send Ajax request with data to perform installation. Display returned UI
 * content.
 */
ugd_ctrl.prototype.upgrade = function ( )
{
	// Copy class scope to method scope.
	var scope = this;
	
	function onCreate ( ) { scope.disable( true ); scope.ind.show( 'executing', '_uicmp_ind_gray' ); }
	function onFailure ( ) { scope.disabled( false ); scope.ind.show( 'e_unknown', '_uicmp_ind_red' ); }
	function onSuccess ( data )
	{
		if ( data.responseText.substr( 0, 9 ) == 'done' )
			scope.ind.show( 'done', '_uicmp_ind_green' );
		else
		{
			scope.disable( false );
			if ( scope.ind.messages[data.responseText] )
				scope.ind.show( data.responseText, '_uicmp_ind_red' );
			else
				scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										
			scope.focus( );
		}
	}
										
	var login = document.getElementById( this.form_id + '.login' ).value;
	var password = document.getElementById( this.form_id + '.password' ).value;
				
	return this.ajax.send(	{ login: login, password: password },
							{ onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess },
							null, false );
}
