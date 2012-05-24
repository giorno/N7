
/**
 * @file n7_ajax.js
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires _ajax_req_ad.js
 * @augments _ajax_req_ad.js
 * 
 * _ajax_req_ad.js must not be loaded after this script!
 */

// Performs triage of received commands and interprets them according to the
// table.
function n7_ajax ( responseText )
{
	// Is there a comment?
	if ( responseText.substr( 0, 4) == '<!--' )
	{
		//alert(responseText);
		try
		{
			// Is it a serialized command?
			var cmdend = responseText.indexOf( '-->' );
			var tag = JSON.parse( responseText.substr( 4, cmdend - 4 ) );
			responseText = responseText.substr( cmdend + 3 );
			
			var core = tag.n7;
			
			if ( core )
			{
				//alert(core.length);
				for ( i = 0; i < core.length; ++i )
				{
					switch ( core[i] )
					{
						// Page indicates that user is signed, but Ajax command
						// tag says that sesion has expired -> refresh should
						// logut the user.
						case 'ssexp':
							if ( n7_signed === true )
								window.location.reload();
						break;
					}
				}
					
			}
		}
		catch ( e )
		{
			// todo error
		}
	}
	
	return responseText;
}

// Inject alternate behaviour to provide interceptor of Ajax command tag.
_ajax_req_ad.prototype.send2 = _ajax_req_ad.prototype.send;
_ajax_req_ad.prototype.update2 = _ajax_req_ad.prototype.update;
_ajax_req_ad.prototype.update = function ( extra, cbs, target_id, data, async )
{
	var original = cbs.onSuccess;
	cbs.onSuccess = function ( data )
	{
		data.responseText = n7_ajax( data.responseText );
		original( data );
	}
	
	// Call original method
	this.update2( extra, cbs, target_id, data, async );
}
_ajax_req_ad.prototype.send = function ( extra, cbs, data, async )
{
	var original = cbs.onSuccess;
	cbs.onSuccess = function ( data )
	{
		//alert(data.responseText);
		
		data.responseText = n7_ajax( data.responseText );
		//alert(data.responseText);
		original( data );
	}
	
	// Call original method
	this.send2( extra, cbs, data, async );
}