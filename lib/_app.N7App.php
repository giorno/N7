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
	
	/**
	 * Ajax channel for CDES. This is parametrized by application specific
	 * arguments values.
	 * 
	 * @param string $table name of database table to operate on
	 * @param _list_cfg $list_cfg list configuration instance
	 * @param string $rm_cb optional callback for removal operation
	 */
	protected function handleCdes ( $table, $list_cfg, $rm_cb = '' )
	{
		switch ( $_POST['method'] )
		{
			/**
			 * This comes from CDES search part, so it should be handled
			 * same way as normal search.
			 */
			case 'refresh':
				require_once CHASSIS_LIB . '_cdes.php';
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::lang( ) );
				$cdes->display( $list_cfg, $_POST['id'], $_POST['cdes_ed'], n7_globals::settings( )->get( 'usr.lst.len' ), n7_globals::settings( )->get( 'usr.lst.pagerhalf' ), $_POST['keywords'], $_POST['page'], $_POST['order'], $_POST['dir'], $rm_cb );
			break;

			/**
			 * Copy of standard UICMP logic handling resize event.
			 */
			case 'resize':
				$this->saveSize( (int)$_POST['size'] );
			break;

			/**
			 * Save context editor data.
			 */
			case 'save':
				require_once CHASSIS_LIB . '_cdes.php';
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::lang( ) );
				if ( trim( $_POST['disp'] ) == '' )
					echo "e_format";
				elseif ( ( $_POST['ctx'] == 0 ) && ( $cdes->exists( $_POST['disp'] ) ) )
					echo "e_exists";
				elseif ( $cdes->add( $_POST['ctx'], $_POST['sch'], $_POST['disp'], $_POST['desc'] ) )
					echo "saved";
				else
					echo "e_unknown";
			break;

			/**
			 * Remove context.
			 */
			case 'remove':
				require_once CHASSIS_LIB . '_cdes.php';
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::lang( ) );
				$cdes->remove( $_POST['ctx'] );
			break;
		}
	}
}

?>