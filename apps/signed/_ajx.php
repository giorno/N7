<?PHP

/**
 * @file _ajx.php
 *
 * Script to initialize and register general purpose Ajax server interfaces for
 * signed user session.
 *
 * @author giorno
 */

require_once dirname( __FILE__ ) . '/_cfg.php';
require_once SIGNEDTAB_LIB . '_app.SignedAjaxImpl.php';

SignedAjaxImpl::getInstance();

?>