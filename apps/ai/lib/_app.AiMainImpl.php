<?php

/**
 * @file _appAiMainImpl.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Main implementation of AI application.
 */

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

require_once N7_SOLUTION_LIB . 'wwg/_wwg.Menu.php';
require_once N7_SOLUTION_LIB . 'wwg/_wwg.MenuItem.php';
require_once N7_SOLUTION_LIB . 'n7_requirer.php';
require_once N7_SOLUTION_LIB . 'n7_ui.php';

require_once APP_AI_LIB . '_app.Ai.php';
require_once APP_AI_LIB . 'uicmp/_vcmp_at.php';

class AiMainImpl extends Ai
{

	/**
	 * UICMP layout container instance.
	 * @var <_uicmp_layout>
	 */
	protected $layout = NULL;
	
	protected function __construct ( )
	{
		parent::__construct( );
		$this->indexTemplatePath = APP_AI_UI . 'index.html';
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'APP_AI_UI', APP_AI_UI );
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'APP_AI_MSG', $this->messages );
	}
	
	public function exec ( )
	{
		if ( ( $registry = _app_registry::getInstance( ) ) != NULL )
		{
			$registry->requireCss( "inc/ai/ai.css",	$this->id );
		}
		
		$page_size	= n7_globals::settings( )->get( 'usr.lst.len' );
		$url		= n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php';	// Ajax server URL
		$params		= Array( 'app' => $this->id, 'action' => 'ue' );					// Ajax request parameters
			
		$this->layout = n7_ui::getInstance( )->getLayout( );
		$this->layout->createSep( );

		$this->getPi( )->tableUi( )->show( );
		$this->getPi( )->client( );
		
		$params['action'] = 'at';
		$this->layout->addVcmp( new _vcmp_at( $this->layout, $this->getVcmpSearchId( 'At' ), $url, $params, $this->messages ) );

		$this->layout->createSep( );
		$this->layout->init( );
		
		if ( $this->getPi( )->has( pers::FL_PI_CREATE ) )
			n7_ui::getInstance( )->getMenu( )->register(	new MenuItem(	MenuItem::TYPE_JS,
															$this->messages['pi_users']['tui']['anchor'],
															$this->getPi( )->jsVar( ) . '.create( );',
															'_uicmp_blue' ) );
	}
	
	/**
	 * Providing structured information used later to render application icon.
	 */
	public function icon ( )
	{
		return Array( 'id' => $this->id,
					  'title' => $this->messages['icon']['text'] );
	}
}

?>