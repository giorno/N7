<?php

/**
 * @file _vcmp_sem.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 *
 * Virtual UICMP component rendering settings editor (part of SEM) and
 * corresponding parts of tab element (buttons group, etc.).
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';

require_once ACCTAB_LIB . '_uicmp_sem.php';

class _vcmp_sem extends _vcmp_comp
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

	public function __construct ( &$parent, $id, $sem, $url, $params, $messages )
	{
		parent::__construct( $parent );
		$this->sem		= $sem;
		$this->url		= $url;
		$this->params	= $params;
		$this->uicmp	= new _uicmp_sem( $this->parent->getBody( ), $id, $sem );

		$this->parent->getBody( )->add( $this->uicmp );
		
		$buttons = new _uicmp_buttons( $this->parent->getHead( ), $this->parent->getHead( )->getId( ) . '.Buttons' );
				$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Reset', _uicmp_gi::IT_A, $messages['sem']['btReset'], $this->uicmp->getJsVar() . '.reset( );', '_uicmp_gi_now _uicmp_blue_b' ) );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S1', _uicmp_gi::IT_TXT, '|' ) );
				$buttons->add( $this->bt = new _uicmp_gi( $buttons, $buttons->getId( ) . '.Save', _uicmp_gi::IT_BT, $messages['sem']['btSave'], $this->uicmp->getJsVar() . '.save( );' ) );
				
				$this->ind = new _uicmp_gi_ind( $buttons, $buttons->getId( ) . '.Ind', _uicmp_gi::IT_IND, $messages['sem'] );
					$buttons->add( $this->ind );
				$this->parent->getHead( )->add( $buttons );
	}
	
	/**
	 * Generate Javascript dependencies and instances.
	 */
	public function  generateJs ( )
	{
		$requirer = $this->uicmp->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_JS, array( $requirer->getRelative( ) . '3rd/XMLWriter-1.0.0-min.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JS, array( $requirer->getRelative( ) . '3rd/base64.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JS, array( 'inc/js/sem.js' , __CLASS__ ) );
			$requirer->call( _uicmp_layout::RES_JS, array( 'inc/account/_uicmp_account.js' , __CLASS__ ) );
			if ( !is_null( $this->sem ) )
			{
				$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->uicmp->getJsVar( ) . " = new _uicmp_sem( '{$this->url}', " . $this->generateJsArray( $this->params ) . ", {$this->ind->getJsVar()} );" );
				$requirer->call( _uicmp_layout::RES_ONLOAD, $this->uicmp->getJsVar( ) . '.startup();' );

				$coll = $this->sem->getFirst();
				while ( $coll )
				{
					$requirer->call( _uicmp_layout::RES_JSPLAIN, $this->uicmp->getJsVar( ) . ".add( new sem_collection( '{$coll->getId()}' ) );" );
					$atom = $coll->getFirst( );
					while ( $atom )
					{
						$particle = $atom->getFirst( );
						while ( $particle )
						{
							$requirer->call( _uicmp_layout::RES_JSPLAIN, "{$this->uicmp->getJsVar()}.colls[{$this->uicmp->getJsVar()}.colls.length-1].add( new sem_atom( {$particle->getType()}, '{$particle->getKey()}', '" . $this->uicmp->getHtmlId( ) . '.' . $particle->getKey( ) . "' ) );" );
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