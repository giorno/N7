
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
		var el = document.getElementById( me.form_id + '.password' );
		if ( el )
			el.value = '';
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
		
		// Password field is not always rendered.
		var el = document.getElementById( me.form_id + '.password' );
		if ( el )
			var password = el.value;
		else
			var password = '';

		var enabled = ( document.getElementById( me.form_id + '.enabled' ).checked ) ? '1' : '0';
		
		/**
		 * @todo encode delivered content using Base64.encode()
		 */
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

/**
 * Logic for AT UI.
 */
function _uicmp_at ( my_name, cnt_id, url, params, ind )
{
	/**
	 * Copy scope.
	 */
	me = this;
	
	/**
	 * Name of Javascript variable referencing this instance.
	 */
	this.my_name = my_name;
	
	/**
	 * HTML ID of container for list of applications.
	 */
	this.cnt_id = cnt_id;
	
	/**
	 * Ajax request URL.
	 */
	this.url = url;
	
	/**
	 * Ajax request parameters.
	 */
	this.params = params;
	
	/**
	 * Indicator component.
	 */
	this.ind = ind;
	
	/**
	 * Reference to cover effect element instance.
	 * 
	 * @stolen from _uicmp_search
	 */
	this.effect = null;
	
	/**
	 * Renders semitransparent cover effect over the search container during the
	 * execution of refresh() method.
	 * 
	 * @stolen from _uicmp_search
	 */
	this.effect_show = function ( )
	{
		if ( !this.effect )
			this.effect = document.getElementById( this.cnt_id + '.effect' );
		
		if ( this.effect )
		{
			var parent = this.effect.parentNode;
			if ( parent )
			{
				this.effect.style.height = parent.offsetHeight + 'px';
				this.effect.style.width = parent.offsetWidth + 'px';
				this.effect.style.visibility = 'visible';
				this.effect.style.display = 'block';
			}
		}
	};
	
	/**
	 * Hides cover effect.
	 * 
	 * @stolen from _uicmp_search
	 */
	this.effect_hide = function ( )
	{
		if ( !this.effect )
			this.effect = document.getElementById( this.cnt_id + '.effect' );
		
		if ( this.effect )	
		{
			this.effect.style.visibility = 'hidden';
			this.effect.style.display = 'none';
			this.effect.style.width = '0px';
			this.effect.style.height = '0px';
		}
	};
	
	/**
	 * Refresh the list of applications.
	 */
	this.list = function ( )
	{
		/**
		 * Copy me into this scope. Awkward, but works.
		 */
		var scope = me;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in scope.params )
			reqParams += '&' + key + '=' + scope.params[key];

		reqParams += '&method=list' +
					 '&js_var=' + scope.my_name;

		var sender = new Ajax.Updater( scope.cnt_id, scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {
											scope.effect_show( );
											scope.ind.show( 'loading', '_uicmp_ind_gray' );
										},
										onFailure: function ( )
										{
											scope.effect_hide( );
											srch.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											scope.effect_hide( );
											scope.ind.fade( 'loaded', '_uicmp_ind_green' );
										}
									}
								);
		return sender;
	};
	
	/**
	 * Invokes installation of upgrade of an application (its database
	 * structures).
	 * 
	 * @param action 'install' or 'upgrade'
	 * @param fsname filesystem folder containing application
	 */
	this.install = function ( action, fsname )
	{
		var root;
		if ( action == 'upgrade' )
			root = 'upgrad';
		else
			if ( action == 'install' )
				root = action;
			else
				return; // not install, not upgrade
			
		/**
		 * Copy me into this scope. Awkward, but works.
		 */
		var scope = me;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in scope.params )
			reqParams += '&' + key + '=' + scope.params[key];

		reqParams += '&method=' + action +
					 '&fsname=' + fsname;

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {
											scope.effect_show( );
											scope.ind.show( root + 'ing', '_uicmp_ind_gray' );
										},
										onFailure: function ( )
										{
											scope.effect_hide( );
											srch.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											scope.effect_hide( );
											
											/**
											 * @todo implement more specific error messages
											 */
											if ( data.responseText == 'OK' )
												scope.ind.fade( root + 'ed', '_uicmp_ind_green' );
											else
												scope.ind.show( 'e_unknown', '_uicmp_ind_red' );
											scope.list( );
										}
									}
								);
		return sender;
	};
	
	this.move = function ( id, distance )
	{
		/**
		 * Copy me into this scope. Awkward, but works.
		 */
		var scope = me;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in scope.params )
			reqParams += '&' + key + '=' + scope.params[key];

		if ( distance > 0 )
			reqParams += '&method=down';
		else
			reqParams += '&method=up';
			
		reqParams += '&id=' + id;

		var sender = new Ajax.Request( scope.url,
									{
										method: 'post',
										parameters: reqParams,
										onCreate: function ( ) {
											scope.effect_show( );
											scope.ind.show( 'installing', '_uicmp_ind_gray' );
										},
										onFailure: function ( )
										{
											scope.effect_hide( );
											srch.ind.show( 'e_unknown', '_uicmp_ind_red' );
										},
										onSuccess: function ( data )
										{
											//alert(data.responseText);
											scope.effect_hide( );
											scope.ind.fade( 'installed', '_uicmp_ind_green' );
											scope.list( );
										}
									}
								);
		return sender;
	};
	
	this.up = function ( id ) { this.move( id, -1 ); };
	
	this.down = function ( id ) { this.move( id, 1 ); };
}
