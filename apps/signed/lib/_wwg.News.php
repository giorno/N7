<?php

/**
 * @file _wwg.News.php
 * @author giorno
 * @package N7
 * @subpackage Signed
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7\apps\Signed;

require_once CHASSIS_LIB . 'class.Wa.php';
require_once CHASSIS_LIB . 'libpdo.php';

require_once CHASSIS_LIB . 'apps/_wwg_registry.php';
require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

require_once CHASSIS_3RD . 'SimplePie.compiled.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';

/**
 * Webwidget displaying few latest headlines from blog. Configurable for
 * specific set of languages.
 */
class News extends \Wwg
{
	/**
	 * Name of database table for RSS cache.
	 */
	const T_RSSCACHE	= 'signed_news';
	
	/**
	 * Field for two-character language code.
	 */
	const F_LANG		= 'lang';
	
	/**
	 * URL of the entry.
	 */
	const F_URL			= 'url';
	
	/**
	 * Headline of the entry.
	 */
	const F_HEADLINE	= 'title';
	
	/**
	 * Timestamp field. The time of the fetch.
	 */
	const F_STAMP		= 'ts';
	
	/**
	 * Authentic RSS channel.
	 */
	const CH_AUTH		= 'A';
	
	/**
	 * Link to another RSS channel.
	 */
	const CH_LINK		= 'L';
	
	/**
	 * Token for URL field of the row in the database table, which holds
	 * timestamp of last update. When language field contains this value, other
	 * fields are interpreted as: 'ts' is timestamp of last fetch, 'lang' is
	 * language code of the channel, 'title' is ignored.
	 */
	const FETCH_STOP	= '__';
	
	/**
	 * Delay between two attempts to fetch channel again. In minutes.
	 */
	const FETCH_DELAY	= 15;
	
	/**
	 * Number of items returned from get() operation.
	 */
	const GET_SIZE		= 3;
	
	/**
	 * System PDO instance.
	 * @var PDO
	 */
	protected $pdo = NULL;

	/**
	 * Hard-coded configuration of the webwidget.
	 * 
	 * @todo it should allow more flexible configuration
	 * 
	 * @var array
	 */
	protected static $cfg = array(	'en' => array( self::CH_AUTH, 'http://blog.creat.io/n7dev/feed/' ),
									'sk' => array( self::CH_LINK, 'en' ),
									'cs' => array( self::CH_LINK, 'en' ) );
	
	/**
	 * Constructor. Provides content for both, main and Ajax requests.
	 * 
	 * @param Signed $app reference to parent application instance
	 * @param array $messages array with parent app localization
	 * @param bool $ajax determines whether this call is from Ajax server or main implementation
	 */
	public function __construct ( $app, $messages, $ajax = false )
	{
		$this->pdo = \n7_globals::getInstance( )->get( \n7_globals::PDO );
		
		/**
		 * For SignedMainImpl app we build whole UI and deliver it to the
		 * template engine.
		 */
		if ( $ajax === false )
		{
			$this->id = 'NewsRss';
			$this->template = SIGNEDTAB_ROOT . 'templ/_wwg.News.html';
			
			/**
			 * Instance of indicator is not connected to any particular layout
			 * hierarchy.
			 */
			$ind = new \io\creat\chassis\uicmp\grpitem( $this, '_wwg.News.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, '', $messages['news']['i'] );
			$ind->generateReqs( );
			
			$apps	= \_app_registry::getInstance( );
			$url	= \n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php';	// Ajax server URL
			$params	= Array( 'app' => $app->getId( ), 'action' => '_wwg.News:update' );	// Ajax request parameters

			$apps->requireJsPlain( 'var ' . $ind->getJsVar( ) . ' = new _uicmp_ind( \'' . $ind->getHtmlId( ) . '\', null, ' . \io\creat\chassis\uicmp\uicmp::toJsArray( $messages['news']['i'] ) . ' );' );
			$apps->requireJsPlain( 'var _wwgNews_i = new _wwgNews( \'' . $url . '\', ' . \io\creat\chassis\uicmp\uicmp::toJsArray( $params ) . ', ' . $ind->getJsVar( ) . ' );' );
			$apps->requireJs( 'inc/chassis/js/_ajax_req_ad.js' , __CLASS__ );
			$apps->requireJs( 'inc/signed/_wwg.News.js' , __CLASS__ );
			//$apps->requireCss( 'inc/signed/_wwg.News.css' , __CLASS__ );
			
			$apps->requireOnLoad( '_wwgNews_i.startup();' );

			\_wwg_registry::getInstance( )->register( \_wwg_registry::POOL_BOTTOM, $this->id, $this );
			
			\_smarty_wrapper::getInstance( )->getEngine( )->assign( 'WWG_NEWS_RSS',	$this->get( \n7_globals::lang( ) ) );
			\_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'WWG_NEWS_IND', $ind );
		}
		/**
		 * For Ajax app we only fetch news and deliver content.
		 */
		else
		{
			\_smarty_wrapper::getInstance( )->getEngine( )->assign( 'WWG_NEWS_RSS',	$this->get( \n7_globals::lang( ), true ) );
			echo \_smarty_wrapper::getInstance( )->getEngine( )->fetch( SIGNEDTAB_ROOT . 'templ/_wwg.News.ajx.html' );
		}
	}
	
	/**
	 * Queries control record in the table for particular language and triggers
	 * appropriate action.
	 * 
	 * @param string $lang two-character language code
	 * @param string $url RSS channel URL
	 */
	public function fetch ( $lang, $url )
	{
		$this->pdo->beginTransaction( );
		
		$last = \io\creat\chassis\pdo1f(
					$this->pdo->prepare( "SELECT `" . self::F_STAMP . "`
						FROM `" . self::T_RSSCACHE . "`
						WHERE `" . self::F_URL . "` = '" . self::FETCH_STOP . "'
							AND `" . self::F_LANG . "` = ?
							AND `" . self::F_STAMP . "` > ( NOW() - INTERVAL( " . self::FETCH_DELAY . " MINUTE ) )" ),
							array( $lang ) );
		
		if ( !(int)$last )
		{
			$this->pdo->prepare( "DELETE FROM `" . self::T_RSSCACHE . "`
				WHERE `" . self::F_URL . "` = '" . self::FETCH_STOP . "'
				AND `" . self::F_LANG . "` = ?" )->execute( array( $lang ) );

			$feed = new \SimplePie();
			$feed->enable_cache( false );	// SimplePie cache would be redundant we use our own cache implementation
			$feed->set_feed_url( $url );
			$feed->init( );
			$feed->handle_content_type( );
			
			$posts = $feed->get_items( );
			if (is_array( $posts ) )
			{
				$sql1 = $this->pdo->prepare( "SELECT `" . self::F_STAMP . "`
							FROM `" . self::T_RSSCACHE . "`
							WHERE `" . self::F_URL . "` = ?
								AND `" . self::F_LANG . "` = ?" );
				
				$sql2 = $this->pdo->prepare( "INSERT INTO `" . self::T_RSSCACHE . "`
									SET `" . self::F_LANG . "` = ?,
										`" . self::F_URL . "` = ?,
										`" . self::F_HEADLINE . "` = ?,
										`" . self::F_STAMP . "` = ?" );
				foreach ( $posts as $post )
				{
					$i_title	= $post->get_title( );
					$i_url		= $post->get_link( );
					$i_ts		= $post->get_date( "Y-m-d H:i:s" );
					
					/**
					 * Write only unique posts.
					 */
					if ( !(int)\io\creat\chassis\pdo1f( $sql1, array( $i_url, $lang ) ) )
						$sql2->execute( array( $lang, $i_url, $i_title, $i_ts ) );
				}
			}
			
			$this->pdo->prepare( "INSERT INTO `" . self::T_RSSCACHE . "`
								SET `" . self::F_URL . "` = '" . self::FETCH_STOP . "',
								`" . self::F_LANG . "` = ?,
								`" . self::F_STAMP . "` = NOW()" )->execute( array( $lang ) );			
		}
		$this->pdo->commit( );
	}
	
	/**
	 * Returns content data to be displayed in the webwidget.
	 * 
	 * @param string $lang two-character language code
	 * @param bool $fetch defines whether method should attempt to fetch new data or not, true value is used for Ajax calls
	 * @return mixed
	 */
	public function get ( $lang, $fetch = false )
	{
		$ret = false;
		if ( array_key_exists( $lang, self::$cfg ) )
		{
			/**
			 * Check for link.
			 */
			if ( self::$cfg[$lang][0] == self::CH_LINK )
				$lang = self::$cfg[$lang][1];
			
			/**
			 * Update the resources.
			 */
			if ( $fetch === true )
				$this->fetch( $lang, self::$cfg[$lang][1] );
			
			$sql = $this->pdo->prepare( "SELECT * FROM `" . self::T_RSSCACHE . "`
								WHERE `" . self::F_LANG . "` = ?
									AND `" . self::F_URL . "` != '" . self::FETCH_STOP . "'
									ORDER BY `" . self::F_STAMP . "` DESC
									LIMIT 0," . self::GET_SIZE . "" );
			
			if ( $sql->execute( array( $lang ) ) )
				return $sql->fetchAll( \PDO::FETCH_ASSOC );
		}
		else
			return false;
	}
	
	/**
	 * Public interface used by build systems to alter configuration by
	 * code outside the class (see bellow).
	 * 
	 * @param array $cfg new configuration
	 */
	public static function setCfg( $cfg ) { self::$cfg = $cfg; }
}

/* Initialization of the class with alternate configuration. */
/*__CFG__ News::setCfg( array( 'en' => array( News::CH_AUTH, 'http://blog.creat.io/n7dev/feed/' ) ), 'sk' => array( News::CH_LINK, 'en' ) ) );*/

?>