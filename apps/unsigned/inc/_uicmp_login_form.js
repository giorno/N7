
/**
 * @file _uicmp_login_form.js
 * @author giorno
 *
 * Client side logic for login form.
 */

/**
 * Base Id for HTML elements of the login form.
 */
var _uicmp_login_id = null;

/**
 * URL for sending login data to.
 */
var _uicmp_login_url = null;

/**
 * Login application Id to match Ajax server side implementation.
 */
var _uicmp_login_app_name = null;

/**
 * Cache for message displayed in the form. Used for dynamic change of language.
 */
var _uicmp_login_last_msg = null;

/**
 * Initialization of variables and the form.
 *
 * @param id base Id for HTML elements
 * @param url URL for login query
 * @param appName Ajax server side implementation match
 */
function _uicmp_login_startup( id, url, appName )
{
	_uicmp_login_id = id;
	_uicmp_login_url = url;
	_uicmp_login_app_name = appName;
	
	document.getElementById( _uicmp_login_id + '.Login' ).focus( );
	disableSelection( document.getElementById( _uicmp_login_id + '.btRememberMe' ) );
	disableSelection( document.getElementById( _uicmp_login_id + '.btSignIn' ) );
}

/**
 * Toggle status of checkbox.
 *
 * @param id HTML Id of the checkbox element
 */
function _uicmp_login_toggle_checkbox ( id )
{
	var el = document.getElementById( id );
	if ( el )
		el.click( );
}

/**
 * Send query to the Ajax server.
 */
function _uicmp_login_login ( )
{
	var login = document.getElementById( _uicmp_login_id + '.Login' ).value;
	var password = document.getElementById( _uicmp_login_id + '.Password' ).value;
	var rememberMe = document.getElementById( _uicmp_login_id + '.ChkBox' ).checked;
	var lang = document.getElementById( _uicmp_login_id + '.Language' )[document.getElementById( _uicmp_login_id + '.Language' ).selectedIndex].value;

	var parameters = 'app=' + _uicmp_login_app_name +
					 '&login=' + escape( login ) +
					 '&password=' + escape( password ) +
					 '&rememberMe=' + ( ( rememberMe == true ) ? '1' : '0' ) +
					 '&lang=' + lang;


	var sender = new Ajax.Request( _uicmp_login_url,
					{
						asynchronous: true,
						method: 'post',
						parameters: parameters,
						onCreate: function ( )
						{
							_uicmp_login_show_message( 'statusSigningIn', '_uicmp_ind_gray' );
						},
						onComplete: function ( )
						{
						},
						onFailure: function ( )
						{
							_uicmp_login_show_message( 'statusUnknown', '_uicmp_ind_red' );
						},
						onSuccess: function ( data )
						{
							/**
							 * Login successful.
							 */
							if ( data.responseText == 'OK' )
							{
								_uicmp_login_show_message( 'statusSigned', '_uicmp_ind_green' );
								location.reload( true );
							}
							else
							{
								/**
								 * Login failed.
								 */
								if ( data.responseText == 'KO' )
									_uicmp_login_show_message( 'statusError', '_uicmp_ind_red' );
								else
									_uicmp_login_show_message( 'statusUnknown', '_uicmp_ind_red' );
								
								document.getElementById( _uicmp_login_id + '.Login' ).focus( );
								document.getElementById( _uicmp_login_id + '.Login' ).select( );
							}
						}
					}
				);
}

/**
 * Returns proper localization messages subarray based on selected language.
 */
function _uicmp_login_strings ( )
{
	var lang = document.getElementById( _uicmp_login_id + '.Language' )[document.getElementById( _uicmp_login_id + '.Language' ).selectedIndex].value;
	return _appLoginMsg[lang];
}

/**
 * Displays request status message in the form.
 *
 * @param msg key from strings array
 */
function _uicmp_login_show_message ( msg, style )
{
	_uicmp_login_last_msg = msg;
	var strings = _uicmp_login_strings( );
	var el = document.getElementById( _uicmp_login_id + '.status' );
	el.innerHTML = strings[msg];
	document.getElementById( _uicmp_login_id + '.statusTd' ).className = '_uicmp_login_message';
	if ( style )
		el.className = style;

}

/**
 * Callback for change of the language. Performs update on the localized
 * content.
 */
function _uicmp_login_set_lang ( )
{
	var strings = _uicmp_login_strings( );
//	alert(strings['loginBtRememberMe']);
	document.getElementById( _uicmp_login_id + '.btRememberMe' ).innerHTML = strings['loginBtRememberMe'];
	document.getElementById( _uicmp_login_id + '.btSignIn' ).innerHTML = strings['loginBtSignIn'];
	document.getElementById( _uicmp_login_id + '.loginPromptLogin' ).innerHTML = strings['loginPromptLogin'];
	document.getElementById( _uicmp_login_id + '.loginPromptPassword' ).innerHTML = strings['loginPromptPassword'];
	document.getElementById( _uicmp_login_id + '.loginPromptLanguage' ).innerHTML = strings['loginPromptLanguage'];
	document.getElementById( _uicmp_login_id + '.loginCaption' ).innerHTML = strings['loginCaption'];
	
	if ( _uicmp_login_last_msg != null )
		_uicmp_login_show_message( _uicmp_login_last_msg );
}
