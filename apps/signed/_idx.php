<?php

/**
 * @file _idx.php
 *
 * Script to instantiate logout application handler and register it into
 * registry.
 *
 * @author giorno
 */

require_once dirname( __FILE__ ) . '/_cfg.php';

require_once SIGNEDTAB_LIB . '_app.SignedMainImpl.php';

SignedMainImpl::getInstance( );

?>