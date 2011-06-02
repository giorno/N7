<?php
/**
 * @file _wwg.Clock.php
 * @author giorno
 *
 * Web widget for analog clock.
 *
 * @todo http://php.net/manual/en/function.strftime.php %P is not supported on
 * MacOsX
 */

require_once CHASSIS_3RD . 'class.SimonsXmlWriter.php';

require_once CHASSIS_LIB . 'apps/_app_registry.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';
require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';

class Clock extends Wwg
{
	const ID = '_wwg.Clock';

	/**
	 * Constructor and initializer for Clock web widget.
	 *
	 * @param <string> $appId id of application handling this widget
	 */
	public function __construct ( $appId = NULL )
	{
		/**
		 * Application Id is specified only during RR phase.
		 */
		if ( !is_null( $appId ) )
		{
			$this->appId = $appId;
			$this->id = static::ID;
			$this->template = dirname(__FILE__) . '/../templ/_wwg.Clock.html';

			_wwg_registry::getInstance( )->register( _wwg_registry::POOL_BOTTOM, $this->id, $this );

			$requirer = n7_requirer::getInstance( );
			$requirer->call( _uicmp_layout::RES_JS, array( $requirer->getRelative() .  '3rd/tinyxmlsax.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JS, array( $requirer->getRelative() .  '3rd/tinyxmlw3cdom.js' , __CLASS__ ) );
			
			_app_registry::getInstance( )->requireJs( 'inc/signed/_wwg.Clock.js', $this->id );
			_app_registry::getInstance( )->requireCss( 'inc/signed/_wwg.Clock.css', $this->id );
			_app_registry::getInstance( )->requireOnLoad( '_wwgClockStartup();' );

			_smarty_wrapper::getInstance( )->getEngine( )->assign( 'WWG_CLOCK_DT', $this->dt( ) );
		}
	}

	public function dt ( )
	{	
		date_default_timezone_set( n7_globals::settings( )->get( 'usr.tz' ) );
		$dt = n7_globals::userTz( )->actualDateTime( );

		$messages = _app_registry::getInstance( )->getById( $this->appId )->getMessages( );

		$dt['date']['C'] = strftime( $messages['wwgClockDate'], $dt['stamp'] );
		$dt['time']['C'] = strftime( $messages['wwgClockTime'], $dt['stamp'] );

		return $dt;
	}

	public function xml ( )
	{
		$dt = $this->dt( );

		$writer = new SimonsXmlWriter( "\t" );
			$writer->push( 'dt', Array( 'zone' => $dt['zone'] ) );
				$writer->push( 'time', $dt['time'] );
				$writer->pop( );
				$writer->push( 'date', $dt['date'] );
				$writer->pop( );
			$writer->pop( );

		return $writer->getXml( );
	}
}

?>