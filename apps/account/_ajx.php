<?PHP

/**
 * @file __ajx.php
 * @author giorno
 * @package N7
 * @subpackage Account
 *
 * Setting up Ajax server implementations for Account app.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

require_once ACCTAB_LIB . '_app.AccountAjaxImpl.php';
AccountAjaxImpl::getInstance();

?>