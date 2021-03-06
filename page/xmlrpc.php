<?php

/**
 * @file xmlrpc.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Script providing interface for XML RPC server.
 */

ini_set( 'default_charset', 'UTF-8' );

$xmlrpc_defencoding = "UTF8";
$xmlrpc_internalencoding = "UTF8";
$output_options = array(
                       "output_type" => "php",
                       "verbosity" => "pretty",
                       "escaping" => array("markup", "non-ascii", "non-print"),
                       "version" => "xmlrpc",
                       "encoding" => "utf-8"
                      );

require_once '../_init.php';

require_once CHASSIS_LIB . 'session/session.php';

require_once N7_SOLUTION_LIB . 'n7_globals.php';
require_once N7_SOLUTION_LIB . 'n7_at.php';
require_once N7_SOLUTION_LIB . 'class.XmlRpcSrv.php';

/**
 * 
 * This variable is used only once in each _xrs.php script, therefore it does
 * not need to be included in n7_globals registry. Also obligatory
 * initialization of the N7.
 */
$srv = new XmlRpcSrv( );
n7_globals::getInstance( )->set( 'xrs', $srv );

/**
 * Each XML RPC enabled application must have this file, in which it should
 * bind object and its methods as XML RPC primitives.
 */
\n7_at::run( n7_at::FL_XMLRPC, '_xrs.php' );

echo $srv->answer( );

?>