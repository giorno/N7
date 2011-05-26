<?php

require_once CHASSIS_LIB . 'apps/_app.App.php';

/**
 * @file _app.N7App.php
 * @author giorno
 * @package N7
 * 
 * Specialization of framework class to provide common processing methods to all
 * solution applications.
 */
abstract class N7App extends App
{
	/**
	 * Returns identifer for _vcmp_search instance and ajax search operation.
	 *
	 * @param string $list list identifier
	 */
	public function getVcmpSearchId ( $list ) { return $this->id . '.' . $list . '.Search'; }
	

	/**
	 * Save new size (from UICMP resizer) into settings.
	 *
	 * @param int $size
	 */
	protected function saveSize ( $size )
	{
		if  ( ( ( $size == 10 ) || ( $size == 20 ) ) || ( ( $size == 30 ) || ( $size == 50 ) ) )
			n7_globals::settings( )->saveOne( 'usr.lst.len', $size );
	}
}

?>