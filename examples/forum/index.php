<?php
namespace Application {

	//Setup
	require_once 'autoload.php';

	putenv('APP_ROLE=PRODUCTION');
	$logger   = new \Loggers\ScreenLogger();
	$router   = new \Routers\ObjectRouter($logger);
	//$dispatch = new \Dispatchers\SimpleDispatcher();
	$context  = null;

	//Request parsing, assuming we won't have to deal with unix paths or non-standard paths like 'people.27.foo'
	$request   = $_SERVER['REQUEST_URI'];
	$webRoot   = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
	$request   = explode('/', str_replace($webRoot, '', $request));
	$request   = array_values(array_filter($request));
	$assetRoot = $webRoot . '/assets';
	$viewRoot  = $_SERVER['DOCUMENT_ROOT'] . '/' . $assetRoot . '/views/';

	//Routing
	foreach ($router($context, '\\Controllers\\RootController', $request) as list($previous, $obj, $isEndpoint)) {
		
		if ($isEndpoint) { break; }

	}

	//Response preparation
	$response = ['view' => 'noResource', 'context' => $context, 'assetRoot' => $assetRoot];

	//Dispatch
	$dispatchResponse = null;
	if (is_object($obj)) {

		if (in_array($previous, get_class_methods($obj))) {
			$dispatchResponse = $obj->$previous();
		} else if (in_array('__invoke', get_class_methods($obj))) {
			$dispatchResponse = $obj->__invoke();
		}

	}

	if (is_array($dispatchResponse)) {
		$response = array_merge($response, $dispatchResponse);
	} else if ($dispatchResponse instanceof \Traversable) {
		/* for sake of simplicity, we assume generators do not emit anything but arrays. In the future, generators may 
		 * yield static elements, functions, objects, generators and more.
		 */
		foreach ($dispatchResponse() as $chunk) {
			$response = array_merge($response, $chunk);
		}

	}

	/*$descentLimit = 10;
	while ($descentLimit) {
		$dispatchResponse = $dispatch($previous, $obj);
		if (is_object($dispatchResponse)) {

		}
		if (is_array($dispatchResponse)) {
			$response = array_merge($response, $dispatchResponse);
		}
		echo var_export($dispatchResponse, true) . "<hr>";
		if (!is_object($dispatchResponse)) { break; }
		$descentLimit--;
	}*/
	

	//Rendering
	require_once 'vendor/Templating/phptenjin-0.0.2/lib/Tenjin.php';

	$layoutFile = 'assets/views/master.phtml';
	
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
		/* If it's an AJAX request, we can skip the layout file and simply render the individual view */
		$layoutFile = null;
	}

	$properties = array('postfix'=>'.phtml', 'layout'=>$layoutFile, 'prefix'=>$viewRoot, 'cache'=>false, 'preprocess'=>true);

	$engine = new \Tenjin_Engine($properties);

	echo $engine->render(":" . $response['view'], $response);

}