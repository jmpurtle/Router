<?php
namespace Magnus;

require_once 'autoload.php';
require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('Magnus');
$log->pushHandler(new StreamHandler('logs/application.log', Logger::WARNING));

$context = new Request\Context([
	'requestURI' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
	'baseURI'    => __DIR__,
	'logger'     => $log
]);

$router = new Router\ObjectRouter();

$response = array('context' => $context);

try {
	foreach ($router($context, 'RootController') as $signal) {
		list($object, $chunk, $path, $isEndpoint) = $signal;
		if ($isEndpoint) {
		    if (!empty($chunk)) {
		        $response = array_merge($response, $object->$chunk($path));
		    } else {
		        $response = array_merge($response, $object($path));
		    }
		}
	}
} catch (\Exception $e) {
	$response['view'] = 'noResource';

} finally {
	require_once 'src/templateEngine/phptenjin-0.0.2/lib/Tenjin.php';

	$layoutFile = 'src/assets/views/layout.phtml';
	
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
		/* If it's an AJAX request, we can skip the layout file and simply render the individual view */
		$layoutFile = null;
	}

	$properties = array('postfix'=>'.phtml', 'layout'=>$layoutFile, 'prefix'=>'src/assets/views/', 'cache'=>false, 'preprocess'=>true);

	$engine = new \Tenjin_Engine($properties);

	echo $engine->render(":" . $response['view'], $response);
}
