<?PHP

/**
 * @file _ajx.php
 * @author giorno
 * @package N7
 * @subpackage Login
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Setting up Ajax server implementations for unsigned user.
 */

require_once N7_SOLUTION_APPS . 'login/_cfg.php';

require_once APP_LOGIN_LIB . '_app.LoginAjaxImpl.php';
LoginAjaxImpl::getInstance();

?>