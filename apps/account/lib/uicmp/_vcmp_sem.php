<?php

/**
 * @file _vcmp_sem.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/grpitem.php';
require_once CHASSIS_LIB . 'uicmp/indicator.php';

require_once ACCTAB_LIB . 'uicmp/_uicmp_sem.php';

/**
 * Virtual UICMP component rendering settings editor (part of SEM) and
 * corresponding parts of tab element (buttons group, etc.).
 * 
 * @todo separate SEM specific part from other Account app specific UI (e.g.
 * buttons row)
 */
class _vcmp_sem extends \io\creat\chassis\uicmp\vcmp
{
	/**
	 * Reference to SEM model instance.
	 *
	 * @var <sem>
	 */
	protected $sem = NULL;

	/**
	 * Indicator component.
	 *
	 * @var <_uimcp_gi_ind>
	 */
	protected $ind = NULL;

	/**
	 * Reference to SEM editor UICMP component.
	 *
	 * @var <_uimp_sem>
	 */
	protected $uicmp = NULL;

	/**
	 * Ajax server URL.
	 *
	 * @var <string>
	 */
	protected $url = NULL;

	/**
	 * Ajax request parameters.
	 *
	 * @var <array>
	 */
	protected $params = NULL;

	/**
	 *
	 * @param \io\creat\chassis\uicmp\tab $parent parent component
	 * @param string $id indetifier of component
	 * @param sem $sem SEM instance
	 * @param string $url Ajax server URL
	 * @param array $params Ajax request default params
	 * @param array $messages localization messages
	 * @param array $extra array of parameters for extra items data to be placed into buttons group
	 */
	public function __construct ( &$parent, $id, $sem, $url, $params, $messages, $extra = NULL )
	{
		parent::__construct( $parent );
		$this->sem		= $sem;
		$this->url		= $url;
		$this->params	= $params;
		$this->uicmp	= new _uicmp_sem( $this->parent->getBody( ), $id, $sem );

		$this->parent->getBody( )->add( $this->uicmp );
		
		$buttons = new \io\creat\chassis\uicmp\buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				$buttons->add( $this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Reset', \io\creat\chassis\uicmp\grpitem::IT_A, $messages['sem']['btReset'], $this->uicmp->getJsVar() . '.reset( );', '_uicmp_gi_refresh _uicmp_blue_b' ) );
				
				/**
				 * Insert extra Javascript items into buttons group.
				 */
				if ( is_array( $extra ) )
					foreach ( $extra as $index => $item )
					{
						$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.E' . $index, \io\creat\chassis\uicmp\grpitem::IT_TXT, '|' ) );
						$buttons->add( $this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.' . $item[0], \io\creat\chassis\uicmp\grpitem::IT_A, $item[1], $item[2] ) );
					}
				
				$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.S1', \io\creat\chassis\uicmp\grpitem::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new \io\creat\chassis\uicmp\grpitem( $buttons, $buttons->getId( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $messages['sem']['btSave'], $this->uicmp->getJsVar() . '.save( );' ) );
				
				$this->ind = new \io\creat\chassis\uicmp\indicator( $buttons, $buttons->getId( ) . '.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, $messages['sem'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generate Javascript dependencies and instances.
	 */
	public function  generateReqs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative( ) . '3rd/XMLWriter-1.0.0-min.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative( ) . '3rd/base64.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/js/sem.js' , __CLASS__ ) );
			$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/account/_uicmp.js' , __CLASS__ ) );
			if ( !is_null( $this->sem ) )
			{
				$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . " = new _uicmp_sem( '{$this->url}', " . \io\creat\chassis\uicmp\uicmp::toJsArray( $this->params ) . ", {$this->ind->getJsVar()} );" );
				$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_ONLOAD, $this->uicmp->getJsVar( ) . '.startup();' );

				$coll = $this->sem->getFirst();
				while ( $coll )
				{
					$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $this->uicmp->getJsVar( ) . ".add( new sem_collection( '{$coll->getId()}' ) );" );
					$atom = $coll->getFirst( );
					while ( $atom )
					{
						$particle = $atom->getFirst( );
						while ( $particle )
						{
							$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, "{$this->uicmp->getJsVar()}.colls[{$this->uicmp->getJsVar()}.colls.length-1].add( new sem_atom( {$particle->getType()}, '{$particle->getKey()}', '" . $this->uicmp->getHtmlId( ) . '.' . $particle->getKey( ) . "' ) );" );
							$particle = $atom->getNext( );
						}
						$atom = $coll->getnext( );
					}

					$coll = $this->sem->getNext();
				}
			}

		}
	}
}

?>