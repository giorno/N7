<?php

/**
 * @file _uicmp_ue.php
 * @author giorno
 * @package N7
 * @subpackage AI
 * 
 * UICMP component rendering content of User Editor (UE) form.
 */

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * User details editor form component. Provides passive UI.
 */
class _uicmp_ue extends \io\creat\chassis\uicmp\uicmp
{
	/**
	 * Is password field modifiable? If not, its input is not rendered.
	 * @var bool
	 */
	protected $modpasswd = TRUE;
	
	public function __construct ( &$parent, $id = NULL, $modpasswd = TRUE )
	{
		parent::__construct( $parent, $id );
		$this->modpasswd	= $modpasswd;
		$this->type			= __CLASS__;
		$this->renderer		= APP_AI_UI . 'uicmp/ue.html';
		$this->jsPrefix		= '_uicmp_ai_ue_i';
	}
	
	/**
	 * Whether not not to render password field.
	 * @return bool
	 */
	public function renderPasswd ( ) { return $this->modpasswd; }
	
	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

?>