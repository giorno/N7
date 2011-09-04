<?php

/**
 * @file _app.AccountMainImpl.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/layout.php';
require_once CHASSIS_LIB . 'uicmp/headline.php';

require_once N7_SOLUTION_LIB . 'n7_requirer.php';

require_once N7_SOLUTION_LIB . 'sem/iface.SemProvider.php';
require_once N7_SOLUTION_LIB . 'sem/sem.php';
require_once N7_SOLUTION_LIB . 'sem/sem_atom.php';
require_once N7_SOLUTION_LIB . 'sem/sem_collection.php';

require_once ACCTAB_LIB . '_app.Account.php';
require_once ACCTAB_LIB . 'uicmp/_vcmp_sem.php';
require_once ACCTAB_LIB . 'uicmp/chpass.php';

/**
 * Main execution instance of Account application.
 */
class AccountMainImpl extends Account implements SemProvider
{
	/**
	 * Instance of SEM model.
	 *
	 * @var sem
	 */
	protected $sem = NULL;

	/**
	 * Constructor. Implementation specific calls.
	 */
	protected function __construct ( )
	{
		parent::__construct( );
		
		_app_registry::getInstance( )->setDefault( $this->id );
		
		$this->indexTemplatePath = ACCTAB_UI . 'index.html';
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'APP_ACCOUNT_TEMPLATES', ACCTAB_UI );
	}

	/**
	 * Main execution body of the application. Creates client side UI and logic.
	 */
	public function exec ( )
	{
		$layout = n7_ui::getInstance( )->getLayout( );
		$dlgs = n7_ui::getInstance( )->getDlgs( );
		$layout->createSep( );
		
			$chp = new \io\creat\n7\apps\account\uicmp\chpass(	$dlgs,
																$this->id . '.ChPass',
																n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php',
																Array( 'app' => $this->id, 'action' => 'chpass' ),
																$this->getMessages( ) );
			
			$tab = $layout->createTab( $this->id . '.Settings', FALSE );
				$tab->createFold( $this->messages['foldSettings'] );
				$tab->getHead( )->add( new \io\creat\chassis\uicmp\headline( $tab, $tab->getId( ) . '.Title', $this->messages['capSettings']) );

				$sem = new _vcmp_sem(	$tab,
										$tab->id . '.Sem',
										$this->getSem( ),
										n7_globals::getInstance()->get( 'url' )->myUrl( ) . 'ajax.php',
										Array( 'app' => $this->id, 'action' => 'sem' ),
										$this->getMessages( ),
										array( array( 'ChPass', $this->messages['capChPass'], $chp->getJsVar() . '.show( );' ) ) );

				$tab->addVcmp( $sem );

		$layout->createSep( );

		$smarty = _smarty_wrapper::getInstance( )->getEngine( );
		$smarty->assignByRef( 'APP_ACCOUNT_LAYOUT', $layout );
		$smarty->assignByRef( 'APP_ACCOUNT_MSG', $this->messages );
	}

	/**
	 * Calls collect on all registered applications. On-demand initialization
	 * of SEM model instance.
	 *
	 * @return sem
	 */
	private function getSem ( )
	{
		if ( is_null( $this->sem ) )
		{
			$this->sem = new sem( );
			
			$this->sem->addCollection( $this->getSemCollection( ) );	// Karhide first
			
			$app = _app_registry::getInstance( )->getFirst( );

			while ( $app )
			{
				if ( ( $app instanceof SemProvider ) && ( !( $app instanceof AccountMainImpl ) ) )	// we already provided SEM for this application
					$this->sem->addCollection( $app->getSemCollection( ) );
				$app = _app_registry::getInstance( )->getNext( );
			}
		}

		return $this->sem;
	}

	/**
	 * Implements interface. Provides SEM collection instance for this
	 * application.
	 * 
	 * @return sem_collection
	 */
	public function getSemCollection ( )
	{
		$coll = new sem_collection( $this->id/* $this->messages['sem']['collGlobal'] */);

			/* Language */
			$atom = new sem_atom( sem_atom::AT_SELECT, 'usr.lang', n7_globals::lang( ), $this->messages['sem']['aLanguage'] );
				$langs = n7_globals::languages( );
				foreach( $langs as $value => $name )
					$atom->addOption( $value, $name );
				$coll->add( $atom );

			/* Timezone */
			$atom = new sem_atom( sem_atom::AT_SELECT, 'usr.tz', n7_globals::settings( )->get( 'usr.tz'), $this->messages['sem']['aTimezone'] );
				$zones = n7_timezone::allZones( );
				foreach( $zones as $record )
					$atom->addOption( $record['id'], $record['display'] );
				$coll->add( $atom );

			/* List size */
			$atom = new sem_atom( sem_atom::AT_SELECT, 'usr.lst.len', n7_globals::settings( )->get( 'usr.lst.len'), $this->messages['sem']['aListLen'] );
				$atom->addOption( 10, 10 );
				$atom->addOption( 20, 20 );
				$atom->addOption( 30, 30 );
				$atom->addOption( 50, 50 );
				$coll->add( $atom );
				
			/* E-mail address */
			$atom = new sem_atom( sem_atom::AT_TEXT, 'usr.email', _session_wrapper::getInstance( )->getEmail( ), $this->messages['sem']['aAddress'] );
				$coll->add( $atom );
			
		return $coll;
	}

	public function event ( $event ) { }

}

?>