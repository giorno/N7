<?php

/**
 * @file _wwg.Promotion.php
 * @author giorno
 *
 * Widget displaying links to other services or resources.
 */

require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

class Promotion extends Wwg
{
	const ID = '_wwg.Promotion';

	public function __construct ( )
	{
		$this->id = static::ID;
		$this->template = dirname(__FILE__) . '/../templ/_wwg.Promotion.html';

		_wwg_registry::getInstance( )->register( _wwg_registry::POOL_FOOTER, $this->id, $this );
	}
}

?>