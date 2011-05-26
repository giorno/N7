
/**
 * @file _wwg.LogMeOut.js
 * @author giorno
 *
 * Logic for LogMeOut widget.
 */

function _wwgLogMeOut ( )
{
	var sender = new Ajax.Request( './ajax.php',
								{
									//asynchronous: false,
									method: 'post',
									parameters: 'app=signed&action=_wwg.LogMeOut:logout',
									/*onCreate: function ( )
									{
									},*/
									onFailure: function ( )
									{
										//_wwgClockSwap( );
									},
									onSuccess: function ( data )
									{
										//alert(data.responseText);
										if ( data.responseText == 'OK' )
											location.reload( true );
									}
								}
							);
	return sender;
}
