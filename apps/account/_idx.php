<?PHP

/**
 * @file _idx.php
 * @author giorno
 * @subpackage Account
 *
 * Initialization of Account app.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';
require_once ACCTAB_LIB . '_app.AccountMainImpl.php';
AccountMainImpl::getInstance();

?>