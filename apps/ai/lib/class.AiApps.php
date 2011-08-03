<?php

/**
 * @file class.AiApps.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'list/_list_builder.php';

require_once N7_SOLUTION_LIB . 'n7_at.php';

/**
 * Class building visual list of applications to be sent to the client.
 */
class AiApps
{
	/**
	 * AI application instance.
	 * 
	 * @var _app.Ai 
	 */
	protected $app = NULL;
	
	/**
	 * Reference to application localization messages.
	 * 
	 * @var array 
	 */
	protected $messages = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param array $messages localization messages
	 */
	public function __construct( &$app )
	{
		$this->app		= $app;
		$this->messages	= $app->getMessages( );
	}
	
	/**
	 * Provides UI list of applications.
	 * 
	 * @param string $js_var client side instance name
	 * @return mixed
	 */
	public function search ( $js_var )
	{
		/**
		 * Extract list of applications.
		 */
		$apps = n7_at::search( );

		if ( is_array( $apps ) )
		{
			/**
			 * Define structure of the list.
			 */
			$builder = new _list_builder( $this->app->getVcmpSearchId( 'At' ), n7_globals::getInstance( )->get('io.creat.chassis.i18n') );
				$builder->addField( '_seq', $this->messages['at']['seq'], AiCfgFactory::LIST_HDRW_SEQ, 1, 'left', false );
				$builder->addField( '_flags', $this->messages['at']['flags'], AiCfgFactory::LIST_HDRW_SEQ, 1, 'left', false );
				$builder->addField( '_name', $this->messages['at']['app'], AiCfgFactory::LIST_HDRW_LOGIN, 1, 'left', false );
				$builder->addField( '_action', '', AiCfgFactory::LIST_HDRW_UID, 1, 'left', false );
				$builder->addField( '_version', $this->messages['at']['version'], AiCfgFactory::LIST_HDRW_UID, 1, 'left', false );
				$builder->addField( '_id', $this->messages['at']['id'], AiCfgFactory::LIST_HDRW_ID, 1, 'left', false );
				$builder->addField( '_path', $this->messages['at']['path'], AiCfgFactory::LIST_HDRW_FSNAME, 1, 'left', false );
				$builder->addField( '_up', '', AiCfgFactory::LIST_HDRW_ICON, 1, 'left', false );
				$builder->addField( '_down', '', AiCfgFactory::LIST_HDRW_ICON, 1, 'left', false );
				
			$builder->ComputePaging( 999999, count( $apps ), 1, 1, n7_globals::settings( )->get( 'usr.lst.pagerhalf' ) );

			for ( $i = 0; $i < count( $apps ); ++$i )
			{	
				$app = $apps[$i];
				$last = false;
				
				/**
				 * Is this last registered app?
				 */
				if ( $i == ( count( $apps ) - 1 ) )
					$last = true;
				/**
				 * Is it candidate?
				 */
				elseif ( $apps[$i][n7_at::F_EXECSEQ] == n7_at::V_CANDIDATE  )
					$last = true;
				/**
				 * Is it last registered app before first pending?
				 */
				elseif ( ( $i < ( count( $apps ) - 1 ) ) && ( ( $app[n7_at::F_EXECSEQ] >= 0 ) && ( $apps[$i+1][n7_at::F_EXECSEQ] == n7_at::V_CANDIDATE  ) ) )
					$last = true;
				
				$i18n = unserialize( $app[n7_at::F_I18N] );
				
				/**
				 * Extract application name.
				 */
				if ( is_array( $i18n ) && array_key_exists( n7_globals::lang( ), $i18n ) )
					$name = $i18n[n7_globals::lang( )];
				elseif ( is_array( $i18n ) )
					$name = $i18n['en'];
				else
					$name = '';
				
				/**
				 * Flags.
				 */
				$flags = '';
				if ( ( $app[n7_at::F_FLAGS] & n7_at::FL_UNSIGNED ) == n7_at::FL_UNSIGNED )
					$flags .= 'U';
				if ( ( $app[n7_at::F_FLAGS] & n7_at::FL_SIGNED ) == n7_at::FL_SIGNED )
					$flags .= 'S';
				if ( ( $app[n7_at::F_FLAGS] & n7_at::FL_MAINRR ) == n7_at::FL_MAINRR )
					$flags .= 'M';
				if ( ( $app[n7_at::F_FLAGS] & n7_at::FL_AJAXRR ) == n7_at::FL_AJAXRR )
					$flags .= 'A';
				
				/**
				 * Write list row.
				 */
				$builder->AddRow(	new _list_cell(	_list_cell::Text(	( $app[n7_at::F_EXECSEQ] != n7_at::V_CANDIDATE ) ? $app[n7_at::F_EXECSEQ] : '' , '', 'center' ) ),
									new _list_cell(	_list_cell::Text(	$flags, '', 'center' ) ),
									new _list_cell(	_list_cell::Text(	$name ) ),
					
									( $app[n7_at::F_EXECSEQ] == n7_at::V_CANDIDATE )
										? new _list_cell(	_list_cell::deco( $this->messages['at']['install'], '', null, '', $js_var . '.install( \'' . $app[n7_at::F_FSNAME] . '\' );'  ), _list_cell::MAN_DECO )
										: new _list_cell(	_list_cell::Text( '' ) ),

									new _list_cell(	_list_cell::Text(	$app[n7_at::F_VERSION] ) ),
									new _list_cell(	_list_cell::Text(	$app[n7_at::F_APPID] ) ),
									new _list_cell(	_list_cell::Text(	$app[n7_at::F_FSNAME] ) ),
					
									( $app[n7_at::F_EXECSEQ] > 0 )
										? new _list_cell(	_list_cell::Code( $js_var . ".up( '" . $app[n7_at::F_APPID] . "' );", $this->messages['at']['up'] ),
																									AiListCell::MAN_AI_AT_UP )
										: new _list_cell(	_list_cell::Text( '' ) ),
					
									( !$last )
										? new _list_cell(	_list_cell::Code( $js_var . ".down( '" . $app[n7_at::F_APPID] . "' );", $this->messages['at']['down'] ),
																									AiListCell::MAN_AI_AT_DOWN )
										: new _list_cell(	_list_cell::Text( '' ) ));
			}
			return $builder->export( );
		}
		else
			return false;
	}
}

?>