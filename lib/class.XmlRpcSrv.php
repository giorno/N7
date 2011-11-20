<?php

/**
 * @file class.XmlRpcSrv.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Superclass for all application specific implementations of XML RPC server
 * wrapper. Its purpose is to automate registration and execution of procedures.
 * Uses only native PHP XML RPC extension.
 */
class XmlRpcSrv
{
	/**
	 * Server handler.
	 * @var resource
	 */
	protected $sh = NULL;
	
	/**
	 * Table of functions.
	 * @var array
	 */
	protected $ft = NULL;
	
	/**
	 * Constructor. Should be called from the subclass constructor before any
	 * call to register() method.
	 */
	public function __construct ( )
	{
		$this->sh = xmlrpc_server_create( );
		$this->ft = array( );
	}
	
	/**
	 * Registers new member method into table of primitives. All published
	 * primitives should be bound in the subclass constructor.
	 * @param string $name public XML RPC method name
	 * @param string $cb name of the member method to be called
	 * @param array $pnames array of parameters names
	 */
	public function register ( $dtor, $name, $cb, $pnames )
	{
		xmlrpc_server_register_method( $this->sh, $name, array( &$this, 'call' ) );
		$this->ft[$name] = array( 'd' => $dtor, 'f' => $cb, 'p' => $pnames );
	}
	
	/**
	 * Performs call to registered primitive. Public access as it has to be
	 * available to server handler.
	 * @param string $name name of the method
	 * @param array $params parameters from the request
	 * @return mixed
	 */
	public function call ( $name, $params )
	{
		$p = array( );
		foreach ( $this->ft[$name]['p'] as $pname )
			$p[$pname] = $params[0][$pname];
		return call_user_func_array( array( &$this->ft[$name]['d'], $this->ft[$name]['f'] ), $p );
	}
	
	/**
	 * Executes XML RPC primitive and returns result. Caller should send
	 * appropriate MIME header (Content-Type: text/xml) to the client.
	 * @return mixed
	 */
	public function answer ( ) { return xmlrpc_server_call_method( $this->sh, file_get_contents( 'php://input'), NULL ); }
}

?>