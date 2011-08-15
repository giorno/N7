<?php

/**
 * @file _app.N7App.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . '_cdes.php';
require_once CHASSIS_LIB . 'apps/_app.App.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_cdes_cloud.php';

/** 
 * Specialization of framework class to provide common processing methods to all
 * solution applications.
 * 
 * @todo loading of localization in separate method or constructor
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
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
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
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
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
				$cdes = new _cdes( _session_wrapper::getInstance( )->getUid( ), $table, n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
				$cdes->remove( $_POST['ctx'] );
			break;
		}
	}
	
	/**
	 * Creates cloud of contexts and send to to client.
	 * 
	 * @param string $table database table to take contexts from
	 * @param string $js_var client side instance variable name
	 * @param string $prefix prefix for HTML ID's
	 * @param string $error_msg text to display when no contexts are available
	 */
	protected function getCdesCloud ( $table, $js_var, $prefix, $error_msg )
	{
		$cloud = new _uicmp_cdes_cloud( NULL, NULL, $js_var, _cdes::allCtxs( _session_wrapper::getInstance( )->getUid( ), $table ), $prefix );
		$cloud->setErrorMsg( $error_msg );
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_UICMP_CMP', $cloud );
		_smarty_wrapper::getInstance( )->setContent( $cloud->getRenderer( ) );
		_smarty_wrapper::getInstance( )->render( );
	}
	
	/**
	 * Handles 'save textarea height' requests data.
	 * 
	 * @param _settings $settings application settings instance with proper namespace
	 * @param string $key setting identifier
	 * @param int $tah value to set
	 */
	protected function handleTah ( &$settings, $key, $tah )
	{
		$min = n7_globals::getInstance()->get( 'config' )->get( 'usr.ta.h.min');
		if ( $tah < $min )
			$tah = $min;
						
		$settings->saveOne( $key, $tah );
	}
}

?>