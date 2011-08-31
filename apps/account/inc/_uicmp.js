
/**
 * @file _uicmp.js
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires sem.js
 * @requires base64.js
 * @requires XMLWriter-1.0.0-min.js
 *
 * Client side logic for Account app UICMP components.
 */

/**
 * Derive from N7 SEM client side logic.
 */
_uicmp_sem.prototype = new sem;

function _uicmp_sem ( url, params, ind )
{
	/**
	 * Ajax server implementation URL.
	 */
	this.url = url;

	/**
	 * Additional parameters for Ajax request.
	 */
	this.params = params;

	/**
	 * _uicmp_gi_ind instance.
	 */
	this.ind = ind;

	this.startup = function ( )
	{
		this.ind.hide( );
	};

	this.reset = function ( )
	{
		window.location.reload();
	};
	
	this.save = function ( )
	{
		this.set_disabled( true );
		
		/**
		 * Copy me into this scope. Awkward, but works.
		 */
		var scope = this;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in scope.params )
			reqParams += '&' + key + '=' + scope.params[key];

		reqParams += '&method=save';

		var data = this.encode( );

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										postBody: reqParams + '&data=' + data,
										onCreate: function ( ) {scope.ind.show( 'saving', '_uicmp_ind_gray' );},
										onFailure: function ( )
										{
											scope.set_disabled( false );
											scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											//alert(data.responseText);
											scope.set_disabled( false );
											
											if ( data.responseText == 'OK' )
											{
												window.location.reload();
												scope.ind.fade( 'saved', '_uicmp_ind_green' );
											}
											else
												if ( data.responseText == 'KO' )
													scope.ind.show( 'e_data', '_uicmp_ind_red' );
												else
													scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										}
									}
								);
		return sender;
	};
}

/**
 * Change password dialog logic.
 * 
 * @param my_id tab (dlg) instance identifier
 * @param frm_id form ID
 * @param url Ajax server URL
 * @param params Ajax request base parameters
 * @param ind indicator instance
 */
function account_chpass ( my_id, frm_id, url, params, ind )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Tab ID.
	 */
	this.my_id = my_id;
	
	/**
	 * Form ID.
	 */
	this.frm_id = frm_id;
	
	/**
	 * Ajax request adapter.
	 */
	this.ajax_ad = new _ajax_req_ad( true, url, params );
	
	/**
	 * _uicmp_gi_ind instance.
	 */
	this.ind = ind;
	
	/**
	 * SkyDome instance.
	 */
	this.sd = null;
	
	/**
	 * Dialog instance.
	 */
	this.dlg = null;
	
	/**
	 * Set up SkyDome and dialog instances.
	 */
	this.startup = function ( )
	{
		/**
		 * SkyDome instance.
		 */
		me.sd = new _sd_dome( me.my_id + '.sd' );

		/**
		 * Dialog rendered with SkyDome.
		 */
		me.dlg = new _sd_simple_ctrl( me.sd, me.my_id );
		
		document.getElementById( me.my_id ).style.width = '560px';
	};
	
	/**
	 * Erase form.
	 */
	this.reset = function ( )
	{
		document.getElementById( me.frm_id + '.old' ).value = '';
		document.getElementById( me.frm_id + '.new' ).value = '';
		document.getElementById( me.frm_id + '.retype' ).value = '';
	};
	
	/**
	 * Disable or enable fields.
	 * @param disabled boolean value
	 */
	this.set_disabled = function ( disabled )
	{
		document.getElementById( me.frm_id + '.old' ).disabled = disabled;
		document.getElementById( me.frm_id + '.new' ).disabled = disabled;
		document.getElementById( me.frm_id + '.retype' ).disabled = disabled;
	};
	
	/**
	 * Displays the dialog.
	 */
	this.show = function ( )
	{
		me.dlg.show( );
		me.reset( );
		document.getElementById( me.frm_id + '.old' ).focus( );
	};
	
	/**
	 * Close the dialog.
	 */
	this.close = function ( ) { me.dlg.hide( ); };
	
	/**
	 * Collect data from the form and send them to Ajax server.
	 */
	this.save = function ( )
	{
		var onCreate = function ( ) { me.ind.show( 'doing', '_uicmp_ind_gray' ); };
		var onFailure = function ( ) { me.set_disabled( false ); me.ind.show( 'e_unknown', '_uicmp_ind_red' ); };
		var onSuccess =  function ( data )
		{
			me.set_disabled( false );
									
			if ( data.responseText == 'OK' )
			{
				me.reset( );
				document.getElementById( me.frm_id + '.old' ).focus( );
				me.ind.fade( 'done', '_uicmp_ind_green' );
			}
			else
				if ( me.ind.messages[data.responseText] )
					me.ind.show( data.responseText, '_uicmp_ind_red' );
				else
					me.ind.show( 'e_unknown', '_uicmp_ind_red' );
		};
										
		this.set_disabled( true );
		
		/**
		 * Extract data.
		 */
		var o = document.getElementById( this.frm_id + '.old' ).value;
		var n = document.getElementById( this.frm_id + '.new' ).value;
		var r = document.getElementById( this.frm_id + '.retype' ).value;
		
		me.ajax_ad.send(	{ o: Base64.encode( o ), n: Base64.encode( n ), r: Base64.encode( r ), method: 'save' },
							{ onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess } );
	};
}
