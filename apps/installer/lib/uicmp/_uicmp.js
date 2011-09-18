
/**
 * @file _uicmp.js
 * @author giorno
 * @package N7
 * @subpackage Installer
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Javascript logic for installer components. This file is supposed to be
 * embedded directly into <HEAD> section, not linked.
 */

function _vcmp_inst_ctrl ( form_id, bt_id, ind )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Root of form controls ID strings.
	 */
	this.form_id = form_id;
	
	/**
	 * HTML ID of button.
	 */
	this.bt_id = bt_id;
	
	/**
	 * Indicator instance.
	 */
	this.ind = ind;
	
	/**
	 * Prepares froomPrefills data for server URL, etc.
	 */
	this.startup = function ( )
	{
		var loc = window.location.toString();
		var schema = ( loc.charAt( 4 ) == 's') ? 'https' : 'http' ;
		var path = loc.substr( ( schema == 'https' ) ? 8 : 7 );
		this.focus( );
		
		document.getElementById( this.form_id + '.path' ).value = path.substr( 0, path.length - 11 );
		document.getElementById( this.form_id + '.schema' ).selectedIndex = ( schema == 'https' ) ? 1 : 0;
	};
	
	/**
	 * Disables controls for the duration of installation.
	 * 
	 * @param disabled desired state
	 */
	this.disable = function ( disabled )
	{
		document.getElementById( me.bt_id ).disabled = disabled;
		document.getElementById( me.form_id + '.schema' ).disabled = disabled;
		document.getElementById( me.form_id + '.path' ).disabled = disabled;
		document.getElementById( me.form_id + '.modrw' ).disabled = disabled;
		document.getElementById( me.form_id + '.tz' ).disabled = disabled;
		document.getElementById( me.form_id + '.login' ).disabled = disabled;
		document.getElementById( me.form_id + '.email' ).disabled = disabled;
		document.getElementById( me.form_id + '.password' ).disabled = disabled;
	};
	
	/**
	 * Put cursor into root login <INPUT> element.
	 */
	this.focus = function ( ) {document.getElementById( this.form_id + '.login' ).focus( );};
	
	/**
	 * Send Ajax request with data to perform installation. Display returned UI
	 * content.
	 */
	this.install = function ( )
	{
		
		
		/**
		 * Collect data.
		 */
		var schema = ( document.getElementById( this.form_id + '.schema' ).selectedIndex == 1 ) ? 'https' : 'http' ;
		var path = document.getElementById( this.form_id + '.path' ).value;
		var modrw = ( document.getElementById( this.form_id + '.modrw' ).checked ) ? '1' : '2' ;
		var tz = document.getElementById( this.form_id + '.tz' ).options[document.getElementById( this.form_id + '.tz' ).selectedIndex].value;
		
		var login = document.getElementById( this.form_id + '.login' ).value;
		var password = document.getElementById( this.form_id + '.password' ).value;
		var email = document.getElementById( this.form_id + '.email' ).value;
		
		/**
		 * Copy scope.
		 */
		var scope = me;
		
		/**
		 * Ajax request URL.
		 */
		var url = ( ( schema == 'http' ) ? 'http://' : 'https://'  ) + path + 'install.php';

		/**
		 * Request parameters.
		 */
		var reqParams = 'action=install' +
						'&schema=' + schema +
						'&site=' + path +
						'&modrw=' + modrw +
						'&tz=' + tz +
						'&login=' + login +
						'&password=' + password +
						'&email=' + email;
		
		var sender = new Ajax.Request(  url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( )
										{
											scope.disable( true );
											scope.ind.show( 'executing', '_uicmp_ind_gray' )
										},
										onFailure: function ( )
										{
											scope.disabled( false );
											scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											if (data.responseText.substr( 0, 9 ) == '<!--OK-->' )
											{
												scope.ind.show( 'done', '_uicmp_ind_green' );
											}
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
									}
								);
		return sender;
	};
}

