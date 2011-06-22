
/**
 * @file _uicmp_account.js
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
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

function _uicmp_chpass ( id, url, params, ind )
{
	/**
	 * Form component HTML ID.
	 */
	this.id = id;
	
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
	
	this.save = function ( )
	{
		//this.set_disabled( true );
		
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
		
		/**
		 * Extract data.
		 */
		var o = document.getElementById( this.id + '.old' ).value;
		var n = document.getElementById( this.id + '.new' ).value;
		var r = document.getElementById( this.id + '.retype' ).value;

		reqParams += '&method=save' +
					 '&o=' + Base64.encode( o ) +
					 '&n=' + Base64.encode( n ) +
					 '&r=' + Base64.encode( r );

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {scope.ind.show( 'doing', '_uicmp_ind_gray' );},
										onFailure: function ( )
										{
											//scope.set_disabled( false );
											scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											
											//alert(data.responseText);
											//scope.set_disabled( false );
											
											if ( data.responseText == 'OK' )
											{
												window.location.reload();
												scope.ind.fade( 'done', '_uicmp_ind_green' );
											}
											else
												if ( scope.ind.messages[data.responseText] )
													scope.ind.show( data.responseText, '_uicmp_ind_red' );
												else
													scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
										}
									}
								);
		return sender;
	};
}
