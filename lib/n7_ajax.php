<?php

/**
 * @file ajax.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\n7;

/**
 * Object handling Ajax response composition. There is supposed to be leading
 * command tag, transported as JSON in HTML comment element.
 */
class n7_ajax
{
	// Cluster of core messages.
	const CORE = 'n7';
	
	// Indicates that session has expired -> triggers reload.
	const TAG_EXPIRED = 'ssexp';
	
	/**
	 * Array of command tags, indexed by application.
	 * @var array
	 */
	protected $vector = NULL;
	
	/**
	 * Payload, will be prepended with command tag.
	 * @var string
	 */
	protected $pl = NULL;
	
	/**
	 * Writes payload part of the reponse.
	 * @param string $pl output to be appended after the command tag
	 */
	public function setPayload ( $pl ) { $this->pl = $pl; }
	
	/**
	 * Indicates client logic that session has expired and UI should be
	 * refreshed to enforce login.
	 */
	public function setExpired ( ) { $this->vector[self::CORE][] = self::TAG_EXPIRED; }
	
	/**
	 * Provides JSON serialized command tag from the structure of command
	 * vector.
	 * @return string
	 */
	private function encode ( ) { return "<!--" . json_encode( $this->vector, JSON_HEX_TAG ) . "-->"; }
	
	/**
	 * Composes response buffer.
	 * @return string
	 */
	public function getResponse ( ) { return $this->encode( ) . $this->pl; }
}

?>