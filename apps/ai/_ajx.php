<?php

/**
 * @file _ajx.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * Script registering Ajax specialized instance of AI application into
 * registry.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

if ( (int)\io\creat\chassis\session::getInstance( )->getUid( ) == 1 )
{
	require_once APP_AI_LIB . '_app.AiAjaxImpl.php';
	AiAjaxImpl::getInstance();
}

?>