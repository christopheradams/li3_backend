<?php

use lithium\net\http\Router;
use lithium\action\Response;
use lithium\net\http\Media;

/**
 * Enable access to assets from installed libraries
 * This route expect all `lithium` libraries prefixed with `li3_`
 *
 * For example route `'/assets/backend/css/bootstrap.css'` is equivalent to:
 * `'/assets/li3_backend/css/bootstrap.css'` and will look for assets in library `webroot` dir.
 *
 * To speed up your application you can copy any asset to your public webroot. Just create same
 * directory structure as url and your asset will be loaded properly.
 */
Router::connect('/assets/{:library}/{:args}', array(), function($request) {
	$file  = Media::webroot('li3_' . $request->params['library']);
	$file .= '/' . join('/', $request->params['args']);
	if (file_exists($file)) {
		$info = pathinfo($file);
		$media = Media::type($info['extension']);
		$content = (array) $media['content'];
		$response = new Response(array(
			'headers' => array('Content-type' => reset($content)),
			'body'    => file_get_contents($file)
		));
		$response->cache('+1 hour');

		return $response;
	}

	throw new \lithium\action\DispatchException();
});

/**
 * Backend routing
 * `backend` prefix is prepended to `action` name.
 */
Router::connect('/backend/{:args}', array('backend' => true), array('continue' => true));

?>