<?php

use lithium\action\Dispatcher;
use lithium\core\Libraries;

defined('LITHIUM_APP_NAME') || define('LITHIUM_APP_NAME', 'Lithium app');

/**
 * Convert `action` to `backend_action` for routes with `backend` prefix.
 * Eg. {{{
 *  /backend/home/index
 *   `array('controller' => 'home', 'action' => 'backend_index')`
 * }}}
 */
Dispatcher::applyFilter('run', function($self, $params, $chain) {
	$self::config(array(
		'rules' => array('backend' => array('action' => 'backend_{:action}'))
	));
	return $chain->next($self, $params, $chain);
});

/**
 * Run `backend_bootstrap` if someone access to backend
 */
Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
	if (isset($params['request']->params['backend']) && $params['request']->params['backend']) {
		foreach(Libraries::get(null, 'path') as $path) {
			if (file_exists($bootstrap = $path . '/config/backend_bootstrap.php')) {
				include_once $bootstrap;
			}
		}
	}
	return $chain->next($self, $params, $chain);
});

?>