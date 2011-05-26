<?PHP

/**
 * @file __idx.php
 * @author giorno
 *
 * Setting up main application instances for unsigned user.
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

require_once UNSIGNEDTAB_LIB . '_app.LoginMainImpl.php';
LoginMainImpl::getInstance();

?>