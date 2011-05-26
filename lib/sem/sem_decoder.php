<?php

require_once CHASSIS_LIB . 'class.Wa.php';

require_once N7_SOLUTION_LIB . 'sem/sem.php';
require_once N7_SOLUTION_LIB . 'sem/sem_atom.php';
require_once N7_SOLUTION_LIB . 'sem/sem_collection.php';

/**
 * @file sem_decoder.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 *
 * XML decoder for data coming from client. Only decoder is needed for server
 * side. It is a counterpart for client side implementation of encoder.
 */
class sem_decoder
{
	/**
	 * Parses XML and produces SEM instance.
	 *
	 * @param text $xml
	 * @return sem
	 */
	public static function decode ( $xml )
	{
		if ( ( $doc = simplexml_load_string( str_replace( ' standalone="false"', '', Wa::PlusSignWaDecode( $xml ) ) ) ) !== false )
		{
			$sem = new sem( );

			$root = $doc->xpath( '//sem' );
			foreach( $root[0] as $cnode )
			{

				$coll = new sem_collection( base64_decode($cnode[0]['id'] ) );
					foreach( $cnode[0] as $anode )
						$coll->add( new sem_atom ( 0, base64_decode( $anode[0]['id'] ), base64_decode( $anode[0]['val']), '' ) );

				$sem->addCollection( $coll );
			}
			return $sem;
		}

		return false;
	}
}
?>