<?php
namespace Application {

	//Setup
	require_once 'autoload.php';

	putenv('APP_ROLE=PRODUCTION');
	$logger = new \Loggers\ScreenLogger();
	$router = new \Routers\ObjectRouter($logger);
	$dispatch = new \Dispatchers\SimpleDispatcher();

	//Request parsing
	$request = $_SERVER['REQUEST_URI'];
	$webRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
	$request = explode('/', str_replace($webRoot, '', $request));
	$request = array_values(array_filter($request));

	//Routing
	foreach ($router(null, '\\Controllers\\RootController', $request) as list($previous, $obj, $isEndpoint)) {
		
		if ($isEndpoint) { break; }

	}

	//Dispatch
	$response = $dispatch($previous, $obj);

	//Rendering
	echo "Response: <br>" . var_export($response, true) . "<hr><br><hr>";

}