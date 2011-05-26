
/**
 * @file _uicmp.js
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Client side logic for AI application UICMP components.
 */

function _uicmp_ue ( layout, tab_id, cap_id, form_id, bt_id, ind, url, params )
{
	var me = this;
	this.layout		= layout;
	this.tab_id		= tab_id;
	this.cap_id		= cap_id;
	this.form_id	= form_id;
	this.bt_id		= bt_id;
	this.ind		= ind;
	this.url		= url;
	this.params		= params;
	this.strings	= new Array( );
	
	/**
	 * Non-zero value means editor is in Edit mode.
	 */
	this.uid		= 0;
	
	this.startup = function ( )
	{
		me.strings['create'] = me.extract_string( me.form_id + '.msg.create' );
		me.strings['modify'] = me.extract_string( me.form_id + '.msg.modify' );
		me.strings['auto'] = me.extract_string( me.form_id + '.msg.auto' );
		me.strings['bt.create'] = me.extract_string( me.form_id + '.msg.btCreate' );
		me.strings['bt.update'] = me.extract_string( me.form_id + '.msg.btUpdate' );
	};
	
	

	/**
	 * Extracts single localization string from <div> element embedded in the
	 * form template.
	 */
	this.extract_string = function ( html_id )
	{
		var el = document.getElementById( html_id );
		if ( el )
			return el.innerHTML;
	};
	
	this.reset = function ( uid, login, email, enabled )
	{
		me.uid = uid;
		document.getElementById( me.form_id + '.uid' ).value = ( me.uid == 0 ) ? me.strings['auto'] : me.uid;
		document.getElementById( me.form_id + '.login' ).value = login;
		document.getElementById( me.form_id + '.email' ).value = email;
		document.getElementById( me.form_id + '.password' ).value = '';
		document.getElementById( me.form_id + '.enabled' ).checked = enabled;
		me.ind.hide( );
	};
	
	this.preview = function ( )
	{
		var caption;

		if ( me.uid == 0 ) caption = this.strings['create'];
		else caption = this.strings['modify'];

		var login = document.getElementById( this.form_id + '.login' ).value;

		document.getElementById( me.cap_id ).innerHTML = caption + ' <i>' + login + '</i>';

		return login;
	};
	
	this.show = function ( )
	{
		me.layout.show( me.tab_id );
		document.getElementById( me.form_id + '.login' ).focus( );
	};
	
	this.create = function ( )
	{
		me.reset( 0, '', '', true );
		document.getElementById( me.bt_id ).value = me.strings['bt.create'];
		document.getElementById( me.form_id + '.login' ).removeAttribute( 'readonly' );
		me.show( );
		me.preview( );
	};
	
	this.modify = function ( uid, login, email, enabled )
	{
		me.reset( uid, login, email, enabled );
		document.getElementById( me.bt_id ).value = me.strings['bt.update'];
		document.getElementById( me.form_id + '.login' ).setAttribute( 'readonly', 'readonly' );
		me.show( );
		me.preview( );
	};
	
	this.toggle = function ( srch, uid )
	{
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

		reqParams += '&method=toggle' + '&uid=' + uid;

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {srch.ind.show( 'executing', '_uicmp_ind_gray' );},
										onFailure: function ( )
										{
											srch.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											if ( data.responseText == 'OK' )
												srch.ind.fade( 'executed', '_uicmp_ind_green' );
											else
												srch.ind.show( 'e_executed', '_uicmp_ind_red' );
											srch.refresh( );
										}
									}
								);
		return sender;
	};
	
	/**
	 * @todo encryption of sensitive (or all) data
	 */
	this.save = function ( )
	{
		
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
		
		var email = document.getElementById( me.form_id + '.email' ).value;
		var password = document.getElementById( me.form_id + '.password' ).value;
		var enabled = ( document.getElementById( me.form_id + '.enabled' ).checked ) ? '1' : '0';
		
		reqParams += '&method=save' +
					 '&uid=' + scope.uid +
					 '&login=' + me.preview( ) +
					 '&email=' + email +
					 '&password=' + password +
					 '&enabled=' + enabled;

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {scope.ind.show( 'saving', '_uicmp_ind_gray' );},
										onFailure: function ( )
										{
											srch.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											if ( data.responseText == 'OK' )
											{
												scope.ind.fade( 'saved', '_uicmp_ind_green' );
												scope.layout.back( );
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
