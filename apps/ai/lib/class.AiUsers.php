<?php

require_once CHASSIS_LIB . 'list/_list_builder.php';
require_once CHASSIS_LIB . 'list/_list_cell.php';

require_once APP_AI_LIB . 'class.AiUser.php';
require_once APP_AI_LIB . 'class.AiCfgFactory.php';
require_once APP_AI_LIB . 'class.AiListCell.php';

/**
 * @file class.AiUsers.php
 * @author giorno
 * @package N7
 * @subpackage Ai
 * 
 * Backend for operations on list of users.
 */
class AiUsers extends AiUser
{
	/**
	 * Reference to AI application instance.
	 * 
	 * @var Ai
	 */
	protected $app = NULL;
	
	/**
	 * Localization messages from application.
	 * 
	 * @var array 
	 */
	protected $messages = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param Ai $app reference to application instance
	 */
	public function __construct( &$app )
	{
		$this->app		= $app;
		$this->messages	= $this->app->getMessages( );
	}
	
	public function search ( $ue_js_var, $keywords, $order, $dir, $page )
	{
		/**
		 * Correct page No.
		 */
		if ( (int)$page < 1 )
			$page = 1;
		
		/**
		 * Default ordering direction.
		 */
		if ( $dir != 'DESC' )
			$dir = 'ASC';
		
		/**
		 * Compose ORDER BY clause ($oc) of SQL.
		 */
		switch ($order)
		{
			case self::F_EMAIL:break;
			case self::F_LOGIN:
			break;
		
			/**
			 * Default ordering field is user ID.
			 */
			case self::F_UID:
			default:
				$order = self::F_UID;
			break;
		}
		
		$oc = "ORDER BY `" . _db_escape( $order ) . "` " . _db_escape( $dir );
		
		/*
		 * Remember list configuration into database.
		 */
		AiCfgFactory::getCfg( 'usr.lst.Users')->save( $keywords, $order, $dir, (int)$page );
		
		/**
		 * Componse WHERE clause ($wc) of SQL.
		 */
		$keywords = trim( $keywords );
		$wc = '';
		if ( $keywords != '' )
			$wc = "WHERE `" . self::F_UID . "` = \"" . _db_escape( $keywords ) . "\" OR `" . self::F_LOGIN . "` LIKE \"%" . _db_escape( $keywords ) . "%\" OR `" . self::F_EMAIL . "` LIKE \"%" . _db_escape( $keywords ) . "%\"";
		
		$count = (int) _db_1field( "SELECT COUNT(*) FROM `" . self::T_USERS . "` {$wc}" );
		
		if ( $count > 0 )
		{
			$page_size	= n7_globals::settings( )->get( 'usr.lst.len' );
			$pages		= ceil( $count / $page_size );

			if ( $page > $pages )
				$page = $pages;
			elseif ( $page < 1 )
				$page = 1;

			$first = ( $page - 1 ) * $page_size;
			
			if ( $count < 1 )
				return false;
			
			/**
			 * Define structure of lists.
			 */
			$builder = new _list_builder( $this->app->getVcmpSearchId( 'Users' ) );
				$builder->addField( self::F_UID, $this->messages['uid'], AiCfgFactory::LIST_HDRW_UID, 1, 'left', true, ( $order == self::F_UID ), $dir );
				$builder->addField( self::F_LOGIN, $this->messages['login'], AiCfgFactory::LIST_HDRW_LOGIN, 1, '', true, ( $order == self::F_LOGIN ), $dir );
				$builder->addField( self::F_EMAIL, $this->messages['address'], AiCfgFactory::LIST_HDRW_ADDRESS, 1, '', true, ( $order == self::F_EMAIL ), $dir );
				$builder->addField( '__ed', '', AiCfgFactory::LIST_HDRW_ICON, 1, '', false );

			$builder->ComputePaging( $page_size, $count, $page, $pages, n7_globals::settings( )->get( 'usr.lst.pagerhalf' ) );
			
			$res = _db_query( "SELECT * FROM `" . self::T_USERS . "` {$wc} {$oc} LIMIT {$first},{$page_size}" );
			
			if ( $res  && _db_rowcount( $res ) )
			{
				while ( $row = _db_fetchrow ( $res ) )
				{
					$builder->AddRow(	new _list_cell(	_list_cell::Text(	$row[self::F_UID] ) ),
							
										new _list_cell(	_list_cell::deco(	$row[self::F_LOGIN],
																			'',
																			null,
																			'',
																			( ( self::isRoot( $row[self::F_UID] ) )	? null : $ue_js_var . ".modify( " . $row[self::F_UID] . ", '" . Wa::JsStringEscape( $row[self::F_LOGIN] ) . "', '" . Wa::JsStringEscape( $row[self::F_EMAIL] ) . "', " . ( ( (int)$row[self::F_ENABLED] == 1 )? 'true' : 'false' ) . " );" ) ),
														_list_cell::MAN_DECO ),
							
										new _list_cell(	_list_cell::Text(	$row[self::F_EMAIL] ) ),
							
										( ( self::isRoot( $row[self::F_UID] ) )	?	new _list_cell(	_list_cell::Text(	'' ) )
																				:	new _list_cell(	_list_cell::Code(	$ue_js_var . ".toggle( _uicmp_lookup.lookup('" . $this->app->getVcmpSearchId( 'Users' ) . "'), {$row[self::F_UID]} );",
																														( ( (int)$row[self::F_ENABLED] == 1 ) ? $this->messages['hintEnabled'] : $this->messages['hintDisabled'] ) ),
																									( ( (int)$row[self::F_ENABLED] == 1 ) ? AiListCell::MAN_AI_USR_E : AiListCell::MAN_AI_USR_D ) ) ) );
					
													
				}
			}
			

			/**
			 * Render list content.
			 */
			return $builder->export( );
		}
		
		return false;
	}	
}

?>