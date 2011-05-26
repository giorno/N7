<?PHP

/**
 * @file __ajx.php
 * @author giorno
 *
 * Setting up Ajax server implementations for unsigned user.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

require_once UNSIGNEDTAB_LIB . '_app.LoginAjaxImpl.php';
LoginAjaxImpl::getInstance();

?>