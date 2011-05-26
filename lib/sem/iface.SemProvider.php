<?php

/**
 * @file iface.SemProvider.php
 * @author giorno
 * @package N7
 * @subpackage SEM
 *
 * Interface for SEM aware application instances. This interface should be
 * implemented by SEM model instance providers.
 */
interface SemProvider
{
	/**
	 * Provides SEM collection instance for application instance.
	 */
	public function getSemCollection ( );
}

?>