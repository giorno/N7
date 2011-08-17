
/**
 * @file __wwg.News.js 
 * @author giorno
 * @package N7
 * @subpackage Signed
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Client side logic for News RSS widget.
 */

/**
 * Object managing News webwidget behaviour.
 * 
 * @param string url Ajax server URL
 * @param array params Ajax request parameters
 * @param _uicmp_gi_ind ind indicator instance
 */
function _wwgNews ( url, params, ind )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Ajax request adapter.
	 */
	this.ajax_ad = new _ajax_req_ad( true, url, params );
	
	/**
	 * Instance of UICMP indicator.
	 */
	this.ind = ind;
	
	/**
	 * Actions performed in page onLoad event.
	 */
	this.startup = function ( )
	{
		me.refresh( );
		disableSelection( document.getElementById( '_wwg.News' ) );
	};
	
	/**
	 * Update data from the server.
	 */
	this.refresh = function ( )
	{
		/**
		 * Ajax request callbacks.
		 */
		function onCreate( ) { me.ind.show( 'loading', '_uicmp_ind_gray' ); };
		function onFailure( ) { me.ind.fade( 'loaded', '_uicmp_ind_green' ); };
		function onSuccess( ) { me.ind.fade( 'loaded', '_uicmp_ind_green' ); };
				 
		me.ajax_ad.update(	{ method: escape( 'refresh' ) },
							{ onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess },
							'_wwg.News:container' );
							
	};
}