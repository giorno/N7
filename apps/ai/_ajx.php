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
require_once APP_AI_LIB . 'class.AiUser.php';

if ( AiUser::isRoot( _session_wrapper::getInstance()->getUid() ) )
{
	require_once APP_AI_LIB . '_app.AiAjaxImpl.php';
AiAjaxImpl::getInstance();
}

?>