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
require_once APP_AI_LIB . 'class.AiUser.php';

if ( AiUser::isRoot( _session_wrapper::getInstance()->getUid() ) )
{
	require_once APP_AI_LIB . '_app.AiMainImpl.php';
	AiMainImpl::getInstance();
}

?>