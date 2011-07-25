
/**
 * @file _uicmp_login_form.js
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires _ajax_req_ad.js
 */

/**
 * Client side logic for login form.
 */
function _uicmp_login_frm ( my_id, url, params, strings )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * HTML ID of my UI instance.
	 */
	this.my_id = my_id;
	
	/**
	 * Ajax request adapter.
	 */
	this.ajax_ad = new _ajax_req_ad( true, url, params );
	
	/**
	 * Localization messages for the form. This is multidimensional associative
	 * array for all languages to provide ability to switch UI language without
	 * need for reload.
	 */
	this.strings = strings;
	
	/**
	 * Cache for last message shown in Ajax request indicator.
	 */
	this.ind_cache = '';
	
	/**
	 * Startup event of the form.
	 */
	this.startup = function ( )
	{
		document.getElementById( me.my_id + '.Login' ).focus( );
		disableSelection( document.getElementById( me.my_id + '.btRememberMe' ) );
		disableSelection( document.getElementById( me.my_id + '.btSignIn' ) );
	};
	
	/**
	 * Handler for change of display language.
	 */
	this.lang_set = function ( )
	{
		var lang = document.getElementById( me.my_id + '.Language' )[document.getElementById( me.my_id + '.Language' ).selectedIndex].value;
		
		var msgs = me.strings[lang];
		
		document.getElementById( me.my_id + '.btRememberMe' ).innerHTML = msgs['loginBtRememberMe'];
		document.getElementById( me.my_id + '.btSignIn' ).innerHTML = msgs['loginBtSignIn'];
		document.getElementById( me.my_id + '.loginPromptLogin' ).innerHTML = msgs['loginPromptLogin'];
		document.getElementById( me.my_id + '.loginPromptPassword' ).innerHTML = msgs['loginPromptPassword'];
		document.getElementById( me.my_id + '.loginPromptLanguage' ).innerHTML = msgs['loginPromptLanguage'];
		document.getElementById( me.my_id + '.loginCaption' ).innerHTML = msgs['loginCaption'];

		if ( me.ind_cache != '' )
			me.ind_show( lang, me.ind_cache );
	};
	
	/**
	 * Displays Ajax request status message.
	 */
	this.ind_show = function ( lang, msg, style )
	{
		if ( lang == null )
			var lang = document.getElementById( me.my_id + '.Language' )[document.getElementById( me.my_id + '.Language' ).selectedIndex].value;
		
		me.ind_cache = msg;
		
		//var msgs = me.strings[lang];
		var el = document.getElementById( me.my_id + '.status' );
		el.innerHTML = me.strings[lang][msg];
		document.getElementById( me.my_id + '.statusTd' ).className = '_uicmp_login_message';
		if ( style )
			el.className = style;
	};
	
	/**
	 * Performs Ajax request to the server and processes response.
	 */
	this.login = function ( )
	{
		/**
		 * Ajax request callbacks.
		 */
		function onCreate( ) { me.ind_show( null, 'statusSigningIn', '_uicmp_ind_gray' ); };
		function onFailure( ) { me.ind_show( null, 'statusUnknown', '_uicmp_ind_red' ); };
		function onSuccess( data )
		{
			/**
			 * Login successful.
			 */
			if ( data.responseText == 'OK' )
			{
				me.ind_show( null, 'statusSigned', '_uicmp_ind_green' );
				location.reload( true );
			}
			else
			{
				/**
				 * Login failed.
				 */
				if ( data.responseText == 'KO' )
					me.ind_show( null, 'statusError', '_uicmp_ind_red' );
				else
					me.ind_show( null, 'statusUnknown', '_uicmp_ind_red' );

				document.getElementById( me.my_id + '.Login' ).focus( );
				document.getElementById( me.my_id + '.Login' ).select( );
			}
		};
		
		var login = document.getElementById( me.my_id + '.Login' ).value;
	
		/**
		 * This is to fix onKeyUp event triggering login prematurely when manually
		 * typing address into browser address bar.
		 */
		if ( login == '' )
			return;

		var password = document.getElementById( me.my_id + '.Password' ).value;
		var auto = document.getElementById( me.my_id + '.ChkBox' ).checked;
		var lang = document.getElementById( me.my_id + '.Language' )[document.getElementById( me.my_id + '.Language' ).selectedIndex].value;
				 
		me.ajax_ad.send(	{ login: escape( login ), password: escape( password ), auto: ( ( auto == true ) ? '1' : '0' ), lang: lang },
							{ onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess } );
	};
}
