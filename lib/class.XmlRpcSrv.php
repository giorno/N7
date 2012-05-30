<?php

/**
 * @file class.XmlRpcSrv.php
 * @author giorno
 * @package N7
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'libpdo.php';
require_once CHASSIS_LIB . 'libfw.php';

/**
 * XML RPC server (XRS) decorator. Binds new primitives to server instance. Uses
 * Singleton and Decorator patterns. Serves as an example for application
 * specific decorators.
 * 
 * This implementation binds core authentication methods of the N7 container.
 */
class CoreXrsDec
{
	/**
	 * Reference to instance implicitly created by the bind() method.
	 * @var \CoreXrsDec
	 */
	private static $instance = NULL;
	
	/**
	 * Reference to XML RPC server instance.
	 * @var XmlRpcSrv
	 */
	protected $server = NULL;
	
	/**
	 * Creates instance and registers new primitives into XML RPC server
	 * (decorates it).
	 * @param \XmlRpcServer $srv reference to XML RPC server wrapper
	 */
	public static function bind ( &$srv )
	{
		if ( is_null( self::$instance ) )
			self::$instance = new CoreXrsDec( $srv );
		
		$srv->register(	self::$instance,	'auth.validate',	'validate',	array( 'token' ) );
		$srv->register(	self::$instance,	'auth.login',		'login',	array( 'username', 'password' ) );
		$srv->register(	self::$instance,	'auth.logout',		'logout',	array( 'token' ) );
	}
	
	/**
	 * Constructor. Makes link to server instance.
	 * @param \XmlRpcServer $srv server instance
	 */
	private function __construct ( $srv ) { $this->server = $srv; }
	
	/**
	 * Hiding copy constructor to follow Singleton pattern.
	 */
	private function __clone ( ) { }
	
	/**
	 * [XML RPC] Validates given token against table of tokens.
	 * @param string $token security token created by login primitive
	 * @return bool
	 */
	public function validate ( $token ) { return $this->server->token2uid( $token ) > 0; }
	
	/**
	 * [XML RPC] Authenticates user given credentials.
	 * @param string $username user login name
	 * @param string $password user password
	 * @return mixed token on success, FALSE on failure
	 */
	public function login ( $username, $password ) { return $this->server->login( $username, $password ); }

	/**
	 * [XML RPC] Performs logout operation.
	 * @param string $token security token
	 * @return array
	 */
	public function logout ( $token ) { return $this->server->logout( $token ); }
}

/**
 * Superclass for all application specific implementations of XML RPC server
 * wrapper. Its purpose is to automate registration and execution of procedures.
 * Uses only native PHP XML RPC extension.
 */
class XmlRpcSrv
{
	/* Table of security tokens for XML RPC sessions. */
	const T_RPCSESS = 'n7_rpcsess';
	
	/* Field name for user ID. */
	const F_UID = Config::F_UID;
	
	/* Field name for security token. */
	const F_TOKEN = Config::F_TOKEN;
	
	/* Field name for expiration timestamp. */
	const F_EXPIRES = 'expires';
	
	/* Default interval for session validity (in minutes). */
	const INTERVAL = 720;
	
	/* Authentication error No. */
	const E_AUTH = 100;
	
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
	 * Core tables PDO. The one maintained by global repository.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Constructor. Should be called from the subclass constructor before any
	 * call to register() method.
	 */
	public function __construct ( )
	{
		$this->sh = xmlrpc_server_create( );
		$this->ft = array( );
		$this->pdo = n7_globals::getInstance( )->get( n7_globals::PDO );
		CoreXrsDec::bind( $this );
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
		$i = 0;
		foreach ( $this->ft[$name]['p'] as $pname )
			$p[$pname] = $params[$i++];
		return call_user_func_array( array( &$this->ft[$name]['d'], $this->ft[$name]['f'] ), $p );
	}
	
	/**
	 * Executes XML RPC primitive and returns result. Caller should send
	 * appropriate MIME header (Content-Type: text/xml) to the client.
	 * @return mixed
	 */
	public function answer ( ) { return xmlrpc_server_call_method( $this->sh, file_get_contents( 'php://input'), NULL ); }
	
	/**
	 * Produces simple array wrapping error indication
	 * @param int $id identifier of an error, see member constants for explation
	 * @param string $msg text explanation
	 * @return array
	 */
	public function error ( $id, $msg = '' ) { return array( 'error' => $id, 'msg' => $msg ); }
	
	/**
	 * Converts security token to user ID. Returns zero on failure (not logged
	 * or session expired). Updates existing record with new new date.
	 * @param string $token security token from the client
	 * @return int
	 */
	public function token2uid ( $token )
	{
		$this->pdo->query( "DELETE FROM `" . self::T_RPCSESS . "`
					WHERE `" . self::F_EXPIRES . "` <= NOW()" );
		
		$uid = (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT `" . self::F_UID . "`
								FROM `" . self::T_RPCSESS . "`
								WHERE `" . self::F_TOKEN . "` = ?" ), array( $token ) );
		
		if ( $uid > 0 )
			$this->pdo->prepare( "UPDATE `" . self::T_RPCSESS . "`
						SET `" . self::F_EXPIRES . "` = (NOW() + INTERVAL " . self::INTERVAL . " MINUTE)
						WHERE `" . self::F_TOKEN . "` = ?
						AND `" . self::F_UID . "` = ?"
								)->execute( array( $token, $uid ) );
		
		return $uid;
	}
	
	/**
	 * Authenticates user given credentials.
	 * @param string $username user login name
	 * @param string $password user password
	 * @return mixed token on success, FALSE on failure
	 */
	public function login ( $username, $password )
	{
		$uid = 0;
		$hash = _fw_hash_passwd( $password );
		
		// Do we have authentication plugin configured?
		$authbe = n7_globals::getInstance( )->authbe( );
		if ( !is_null( $authbe ) )
			if ( (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT `" . Config::F_UID . "`
								FROM `" . Config::T_USERS . "`
								WHERE `" . Config::F_LOGIN . "` = ?" ),
							array( $username ) ) != 1 )
				$uid = (int)$authbe->validate( $username, $password );

		// Table authentication.
		if ( $uid < 1 )
			$uid = (int)\io\creat\chassis\pdo1f( $this->pdo->prepare( "SELECT `" . Config::F_UID . "`
										FROM `" . Config::T_USERS . "`
										WHERE `" . Config::F_LOGIN . "` = ?
										AND `" . Config::F_PASSWD . "` = ?
										AND `" . Config::F_ENABLED . "` = '1'" ),
								array( $username, $hash ) );
		
		// Create session.
		if ( $uid > 0 )
		{
			_fw_rand_init( );
			$token = _fw_rand_hash( );
			$this->pdo->prepare( "INSERT INTO `" . self::T_RPCSESS . "`
						SET `" . self::F_EXPIRES . "` = (NOW() + INTERVAL " . self::INTERVAL . " MINUTE),
							`" . self::F_TOKEN . "` = ?
							`" . self::F_UID . "` = ?"
								)->execute( array( $token, $uid ) );
			return $token;
		}
		
		return FALSE;
	}
	
	/**
	 * Deletes session associated with the token.
	 * @param string $token security token
	 * @return array
	 */
	public function logout ( $token )
	{
		if ( $this->token2uid( $token ) > 0 )
		{
			$this->pdo->prepare( "DELETE FROM `" . self::T_RPCSESS . "`
				WHERE `" . self::F_TOKEN . "` = ?"
								)->execute( array( $token ) );
			
			return array( 'status' => ( (int)$this->token2uid( $token ) == 0 ) );
		}
		else
			return array( 'status' => TRUE );
	}
}

?>
