<?php

require_once N7_SOLUTION_LIB . 'sem/iface.SemApplicator.php';
require_once N7_SOLUTION_LIB . 'sem/sem_decoder.php';

require_once ACCTAB_LIB . '_app.Account.php';

/**
 * @file _app.AccountAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Main execution instance of Account application.
 */
class AccountAjaxImpl extends Account implements SemApplicator
{
	public function exec ( )
	{
		switch ( $_POST['action'] )
		{
			/**
			 * Request from SEM UI. Applies new settings.
			 */
			case 'sem':
				$sem = sem_decoder::decode( $_POST['data'] );
				if ( $this->setSem( $sem ) )
					echo "OK";
				else
					echo "KO";
			break;
			
			/**
			 * Processing change password request.
			 */
			case 'chpass':
				
				$old = base64_decode( $_POST['o'] );
				$new = base64_decode( $_POST['n'] );
				$retype = base64_decode( $_POST['r'] );

				/**
				 * Check current password.
				 */
				if ( !_session_wrapper::getInstance()->checkPassword( $old ) )
				{
					echo "e_old";
					break;
				}
					
				require_once APP_AI_LIB . 'class.AiUser.php';
				
				/**
				 * Check new password to fulfillment of security.
				 */
				if ( !AiUser::passOk( $new ) )
				{
					echo "e_new";
					break;
				}
				
				if ( $new != $retype )
				{
					echo "e_retype";
					break;
				}
				
				/**
				 * Set and check the result.
				 */
				_session_wrapper::getInstance()->setPassword( $new );
				
				if ( !_session_wrapper::getInstance()->checkPassword( $new ) )
					echo "e_unknown";
				else
					echo "OK";
				
			break;
			
		}
	}

	public function setSem( $sem )
	{

		/**
		 * Check values.
		 */
		$coll = $sem->getFirst( );
		while ( $coll )
		{
			$app = _app_registry::getInstance()->getById( $coll->getId( ) );
			
			if ( $app instanceof SemApplicator )
				if ( !$app->chkSemCollection( $coll ) )
					return false;

			$coll = $sem->getNext( );
		}
		
		/**
		 * Write
		 */
		$coll = $sem->getFirst( );
		while ( $coll )
		{
			$app = _app_registry::getInstance()->getById( $coll->getId( ) );
			
			if ( $app instanceof SemApplicator )
				$app->setSemCollection( $coll );

			$coll = $sem->getNext( );
		}

		return true;
	}

	public function chkSemCollection ( $coll )
	{
		/**
		 * Check language.
		 */
		if ( $atom = $coll->getById( 'usr.lang' ) )
			if ( !array_key_exists( $atom->getValue( ), n7_globals::languages( ) ) )
				return false;

		/**
		 * Check timezone for length over 2 characters.
		 */
		if ( $atom = $coll->getById( 'usr.tz' ) )
			if ( strlen ( trim( $atom->getValue( ) ) ) < 3 )
				return false;

		/**
		 * Check list length values.
		 */
		if ( $atom = $coll->getById( 'usr.lst.len' ) )
			if ( !in_array ( $atom->getValue( ), Array( 10, 20, 30, 50 ) ) )
				return false;
			
		return true;
	}

	public function setSemCollection ( $coll )
	{
		if ( $atom = $coll->getById( 'usr.lang' ) )
			n7_globals::settings( )->saveOne( 'usr.lang', $atom->getValue( ) );

		if ( $atom = $coll->getById( 'usr.tz' ) )
			n7_globals::settings( )->saveOne( 'usr.tz', $atom->getValue( ) );

		if ( $atom = $coll->getById( 'usr.lst.len' ) )
			n7_globals::settings( )->saveOne( 'usr.lst.len', $atom->getValue( ) );
	}

	public function event ( $event ) { }

}

?>