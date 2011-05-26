<?php

require_once N7_SOLUTION_LIB . "n7_settings.php";

/**
 * @file n7_timezone.php
 * @author giorno
 * @package N7
 *
 * Timezone handler for application GTDtab.com. Some of methods are already
 * built-in in PHP > 5.3.0, but project is going to be hosted on 5.2.0 site.
 */
class n7_timezone extends DateTimeZone
{
	/**
	 * Plaintext information containing obsolete timezones. This is just copied
	 * text from http://us2.php.net/manual/en/timezones.others.php, so it should
	 * not be that hard to maintain.
	 *
	 * They must not be used!
	 */
	const BLACKLIST = "	Brazil/Acre  	Brazil/DeNoronha  	Brazil/East  	Brazil/West  	Canada/Atlantic
						Canada/Central 	Canada/East-Saskatchewan 	Canada/Eastern 	Canada/Mountain 	Canada/Newfoundland
						Canada/Pacific 	Canada/Saskatchewan 	Canada/Yukon 	CET 	Chile/Continental
						Chile/EasterIsland 	CST6CDT 	Cuba 	EET 	Egypt
						Eire 	EST 	EST5EDT 	Etc/GMT 	Etc/GMT+0
						Etc/GMT+1 	Etc/GMT+10 	Etc/GMT+11 	Etc/GMT+12 	Etc/GMT+2
						Etc/GMT+3 	Etc/GMT+4 	Etc/GMT+5 	Etc/GMT+6 	Etc/GMT+7
						Etc/GMT+8 	Etc/GMT+9 	Etc/GMT-0 	Etc/GMT-1 	Etc/GMT-10
						Etc/GMT-11 	Etc/GMT-12 	Etc/GMT-13 	Etc/GMT-14 	Etc/GMT-2
						Etc/GMT-3 	Etc/GMT-4 	Etc/GMT-5 	Etc/GMT-6 	Etc/GMT-7
						Etc/GMT-8 	Etc/GMT-9 	Etc/GMT0 	Etc/Greenwich 	Etc/UCT
						Etc/Universal 	Etc/UTC 	Etc/Zulu 	Factory 	GB
						GB-Eire 	GMT 	GMT+0 	GMT-0 	GMT0
						Greenwich 	Hongkong 	HST 	Iceland 	Iran
						Israel 	Jamaica 	Japan 	Kwajalein 	Libya
						MET 	Mexico/BajaNorte 	Mexico/BajaSur 	Mexico/General 	MST
						MST7MDT 	Navajo 	NZ 	NZ-CHAT 	Poland
						Portugal 	PRC 	PST8PDT 	ROC 	ROK
						Singapore 	Turkey 	UCT 	Universal 	US/Alaska
						US/Aleutian 	US/Arizona 	US/Central 	US/East-Indiana 	US/Eastern
						US/Hawaii 	US/Indiana-Starke 	US/Michigan 	US/Mountain 	US/Pacific
						US/Pacific-New 	US/Samoa 	UTC 	W-SU 	WET
						Zulu";
	
	/**
	 * Zero offset timezone reference.
	 * 
	 * @var DateTimeZone
	 */
	private $tzGmt = null;

	/**
	 * UCT/GMT timestamp of stored datetime.
	 * 
	 * @var int
	 */
	private $gmtStamp = null;

	/**
	 * Constructor.
	 * 
	 * @param string $tz timezone identifier
	 */
    public function __construct ( $tz )
	{
		$this->tzGmt = new DateTimeZone( "GMT" );
		parent::__construct( $tz );
	}

	/**
	 * Set timestamp in values for instanced timezone. It is recomputed
	 * to UCS/GMT.
	 *
	 * @param $int $datetime timestamp
	 */
	public function importTzDateTime ( $datetime )
	{
		$dt = new DateTime( $datetime, $this );
		$this->gmtStamp = strtotime( $datetime) - $this->getOffset( $dt );
	}

	/**
	 * Set stamp to GMT value.
	 *
	 * @param int $stamp UNIX timestamp (in GMT)
	 */
	public function importStamp ( $stamp ) { $this->gmtStamp = $stamp; }

	/**
	 * Read access to GMT timestamp;
	 * 
	 * @return int
	 */
	public function exportStamp ( ) { return $this->gmtStamp; }

	/**
	 * Export stamp increased by timezone offset.
	 * 
	 * @return int
	 */
	public function exportTzStamp ( )
	{
		/*
		 * strtotime() compatible string has to be created from the GMT stamp
		 * to have valit date/time for DST offset calculation.
		 */
		$dt = new DateTime( date( "Y-m-d H:i:s", $this->gmtStamp ), $this );
		return $this->gmtStamp + $this->getOffset( $dt );
	}

	/**
	 * Provide offset information in human readable form.
	 * 
	 * @param int $offset offset in seconds
	 * @return string
	 */
	public static function humanOffset ( $offset )
	{
		return ( ( $offset < 0 ) ? '-' : '+' ) . sprintf( "%02d", floor( abs( $offset ) / 3600 ) ) . ':' . sprintf( "%02d", floor( abs( $offset ) % 3600 ) );
	}
	
	/**
	 * Backward compatibility interface. Usage should be replaced with static
	 * method use.
	 * 
	 * @return array
	 * @deprecated 
	 */
	public function all ( ) { return static::allZones( ); }

	/**
	 * Provide array structured information about available timezones ordered
	 * by offset and alphabet. Some of obsolete timezones and special ones
	 * should be removed here.
	 *
	 * @param string $format format for display string, should contain two %s tokens to be replaced by offset and zone name
	 * @return array
	 */
	public static function allZones ( $format = "[GMT%s] %s" )
	{
		$input = timezone_identifiers_list( );

		if ( is_array( $input ) )
		{
			foreach ( $input as $zone )
			{
				if ( strpos( self::BLACKLIST, $zone ) )
					continue;

				$tz = new DateTimeZone( $zone );
				$dt = new DateTime( "now", $tz );
				$info['zone'] = $zone;
				$info['offset'] = $dt->getOffset( );
				$info['human'] = self::humanOffset( $info['offset'] );
				$output[$info['offset']][] = $info;
			}
		}
		ksort( $output );
		
		foreach ( $output as $offset )
			foreach ( $offset as $zone )
			{
				$formatted[$zone['zone']] = Array(	'id' => $zone['zone'],
													'offset' => $zone['offset'],
													'human' => $zone['human'],
													'display' => sprintf( $format, $zone['human'], $zone['zone'] )  );
			}
			
		return $formatted;
	}

	/**
	 * Provide array structured information about actual date and time. This is
	 * used e.g. for UI clock and calendar leaf widgets.
	 *
	 * @return array actual datetime details for current timezone
	 */
	public function actualDateTime ( )
	{
		$this->importTzDateTime( "now" );
		$ts = $this->exportTzStamp( );
		
		/*
		 * Zero based hour (12 hours is interpreted as 00) used for clock image.
		 */
		$zb = date( "h", $ts );
		if ( (int)$zb == 12 )
			$zb = "00";

		return Array(	'zone' => $this->getName( ),
						'stamp' => $ts,
						'time' => Array(	'h' => date( "H", $ts ),
											'm' => date( "i", $ts ),
											'zb' => $zb ),
						'date' => Array(	'd' => date( "j", $ts ),
											'dA' => strftime( "%A", $ts ),
											'mA' => strftime( "%b", $ts ) ) );
	}

	

	/**
	 * Provide Javascript Date() object acceptable string for today in actual
	 * timezone.
	 *
	 * @return string
	 */
	public function jsToday ( )
	{
		$this->importTzDateTime( "now" );
		return date( "M j, Y", $this->exportTzStamp( ) );
	}

	/**
	 * Provide day of month number for today. Used e.g. for highlighted day
	 * number in stuff editor 3-months calendar.
	 *
	 * @return int
	 */
	public function jsTodayDay ( )
	{
		$this->importTzDateTime( "now" );
		return (int)date( "d", $this->exportTzStamp( ) );
	}

	/**
	 * Same functionality as ::jsToday(), but for tomorrow.
	 *
	 * @return string
	 */
	public function jsTomorrow ( )
	{
		$this->importTzDateTime( "now" );
		$this->gmtStamp += 24 * 3600;
		return date( "M j, Y", $this->exportTzStamp( ) );
	}
	
}

?>
