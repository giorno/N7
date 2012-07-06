<?php

/**
 * @file _idx.php
 * @author giorno
 * @package N7
 * @subpackage Ai
 * 
 * Script registering Users application into registry.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

if ( (int)\io\creat\chassis\session::getInstance( )->getUid( ) == 1 )
{
	require_once APP_AI_LIB . '_app.AiMainImpl.php';
	AiMainImpl::getInstance();
}

?>