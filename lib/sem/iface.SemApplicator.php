<?php

/**
 * @file iface.SemApplicator.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Interface for SEM aware application instances. This interface should be
 * implemented by SEM model instance providers.
 */
interface SemApplicator
{
	/**
	 * Saves SEM collection model instance values into database.
	 */
	public function setSemCollection ( &$coll );

	/**
	 * Checks validity of SEM collection model instance values.
	 */
	public function chkSemCollection ( &$coll );
}

?>