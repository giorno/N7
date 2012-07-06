<?php

/**
 * @file _app.AccountAjaxImpl.php
 * @author giorno
 * @package N7
 * @subpackage Account
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_3RD . 'EmailAddressValidator.php';

require_once N7_SOLUTION_LIB . 'sem/iface.SemApplicator.php';
require_once N7_SOLUTION_LIB . 'sem/sem_decoder.php';

require_once ACCTAB_LIB . '_app.Account.php';

/**
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
				if ( ( $result = $this->setSem( $sem ) ) === true )
					echo "OK";
				else
				{
					if ( is_array( $result ) )
						echo $result['desc'];
					else
						echo "KO";
				}
			break;
			
			/**
			 * Processing change password request.
			 */
			case 'chpass':
				
				// In case that authorization plugin is used, perform change
				// of the password only if it is supported.
				$authbe = n7_globals::getInstance()->authbe( );
				if ( ( !is_null( $authbe ) ) && ( ( \io\creat\chassis\session::getInstance( )->getUid( ) > 1 ) && ( $authbe->hasFlag( \io\creat\chassis\authbe::ABE_MODPASSWD ) ) ) )
				{
					echo "e_unsupported";
					break;
				}
						
				
				$old = base64_decode( $_POST['o'] );
				$new = base64_decode( $_POST['n'] );
				$retype = base64_decode( $_POST['r'] );

				/**
				 * Check current password.
				 */
				if ( !\io\creat\chassis\session::getInstance()->checkPassword( $old ) )
				{
					echo "e_old";
					break;
				}
					
				require_once APP_AI_LIB . 'pers/users.php';
				
				/**
				 * Check new password to fulfillment of security.
				 */
				if ( !\io\creat\n7\ai\users::passOk( $new ) )
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
				\io\creat\chassis\session::getInstance()->setPassword( $new );
				
				if ( !\io\creat\chassis\session::getInstance()->checkPassword( $new ) )
					echo "e_unknown";
				else
					echo "OK";
				
			break;
			
		}
	}

	/**
	 * Perform set operation on all collections delivered in SEM model.
	 * 
	 * @param sem $sem sem model with collections
	 * @return bool
	 */
	public function setSem( &$sem )
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
				{
					if ( is_array( $ex = $coll->getException( ) ) )
						return $ex;
					else
						return false;
				}

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

	/**
	 * Check if data delivered from client side are valid.
	 * 
	 * @param sem_collection $coll client collected data
	 * @return bool
	 */
	public function chkSemCollection ( &$coll )
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
			
		/**
		 * Check e-mail address.
		 */
		$validator = new EmailAddressValidator;
		if ( $atom = $coll->getById( 'usr.email') )
			if ( !$validator->check_email_address( $atom->getValue( ) ) )
			{
				$coll->setException( array( 'code' => 'e_email', 'desc' => $this->messages['sem']['e_email'] ) );
				return false;
			}
			
		return true;
	}

	/**
	 * Set client collected and delivered data.
	 * 
	 * @param sem_colelction $coll collection data
	 */
	public function setSemCollection ( &$coll )
	{
		if ( $atom = $coll->getById( 'usr.lang' ) )
			n7_globals::settings( )->saveOne( 'usr.lang', $atom->getValue( ) );

		if ( $atom = $coll->getById( 'usr.tz' ) )
			n7_globals::settings( )->saveOne( 'usr.tz', $atom->getValue( ) );

		if ( $atom = $coll->getById( 'usr.lst.len' ) )
			n7_globals::settings( )->saveOne( 'usr.lst.len', $atom->getValue( ) );
		
		/**
		 * E-mail address in not ordinary setting, it is updated in users table.
		 */
		if ( $atom = $coll->getById( 'usr.email' ) )
			n7_globals::getInstance()->get( n7_globals::PDO )->prepare(
				"UPDATE `" . Config::T_USERS . "`
					SET `" . Config::F_EMAIL . "` = ?
					WHERE `" . Config::F_UID . "` = ?"
				)->execute( array( $atom->getValue( ), \io\creat\chassis\session::getInstance( )->getUid( ) ) );
	}

	/**
	 * Implements abstract method to conform parent requirement.
	 * 
	 * @param int $event 
	 */
	public function event ( $event ) { }

}

?>