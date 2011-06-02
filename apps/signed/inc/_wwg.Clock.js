/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Object maintaining information about current datetime. By this variable
 * UI is changing image for the clock and calendar leaf.
 */
var uiDateTime = null;

/*
 * Object containing images for actual time and two one-minutes steps forward.
 */
var uiClockImages = new Array( );
	uiClockImages['now'] = new Image( );
	uiClockImages['plus1'] = new Image( );
	uiClockImages['plus2'] = new Image( );

/**
 * Perform startup of the widget.
 */
function _wwgClockStartup ( )
{
	_wwgClockUpdate( );
	_wwgClockRefresh( );
	disableSelection( document.getElementById( 'wwgClock' ) );
}

/*
 * Provide path to certain clock image.
 */
function uiClockPath ( hour, minute, increment )
{
	if ( ( minute + increment ) >= 60 )
	{
		hour++;
		minute = increment;
	}
	if ( hour > 11) hour = hour - 12;
	return './inc/signed/_wwg.Clock.img/' + sprintf( "%02d", hour ) + sprintf( "%02d", minute ) + '.png';
}

/*
 * Swap source paths of images.
 */
function _wwgClockSwap ( )
{
	if ( uiClockImages['plus1'].src != '' )
	{
		uiClockImages['now'].src = uiClockImages['plus1'].src;
		document.getElementById( 'wwgClockImg' ).src = uiClockImages['now'].src;
		uiClockImages['plus1'].src = uiClockImages['plus2'].src;
		uiClockImages['plus2'].src = '';
	}
}

/*
 * Redraw clock image. And some tricks also :-)
 */
function _wwgClockUpdate ( )
{

	var sender = new Ajax.Request( './ajax.php',
								{
									//asynchronous: false,
									method: 'post',
									parameters: 'app=signed&action=_wwg.Clock:update',
									/*onCreate: function ( )
									{
									},*/
									onFailure: function ( )
									{
										_wwgClockSwap( );
									},
									onSuccess: function ( data )
									{
										var parser = new DOMImplementation( );
										var domDoc = parser.loadXML( data.responseText );
										
										var docRoot = domDoc.getDocumentElement();
										var time = docRoot.getElementsByTagName( 'time' ).item( 0 );

										uiDateTime = new Array( );
										uiDateTime['hour'] = Number( time.getAttribute( 'zb' ) );	// zero-based hour
										uiDateTime['minute'] = Number( time.getAttribute( 'm' ) );

										var hour = uiDateTime['hour'];
										var minute = uiDateTime['minute'];

										uiClockImages['now'].src = uiClockPath( hour, minute, 0 );
										uiClockImages['plus1'].src = uiClockPath( hour, minute, 1 );
										uiClockImages['plus2'].src = uiClockPath( hour, minute, 2 );
										
										document.getElementById( 'wwgClockImg' ).src = uiClockImages['now'].src;

										var date = docRoot.getElementsByTagName( 'date' ).item( 0 );

										uiDateTime['day'] = Number( date.getAttribute( 'd' ) );
										uiDateTime['monthAbbr'] = date.getAttribute( 'mA' );
										uiDateTime['dayAbbr'] = date.getAttribute( 'dA' );

										document.getElementById( 'wwgClockTime' ).innerHTML = time.getAttribute( 'C' ).toString( );
										document.getElementById( 'wwgClockDate' ).innerHTML = date.getAttribute( 'C' ).toString( );
										document.getElementById( 'wwgClockZone' ).innerHTML = docRoot.getAttribute( 'zone' ).toString( );
									}
								}
							);
	return sender;
}

/*
 * This method is ran every 1000ms = 1s, when seconds portion of date is equal
 * zero, uiUpdateTime() routine is ran.
 */
function _wwgClockRefresh ( )
{
	var now = new Date( );
	if ( now.getSeconds( ) == 1 ) _wwgClockUpdate( );

	setTimeout( "_wwgClockRefresh()", 1000 );
}