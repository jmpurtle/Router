<?php
namespace Magnus;
spl_autoload_register(function($class) {		
		if (stripos($class, __NAMESPACE__) === 0)
		{
			$fileLocation = __DIR__ . DIRECTORY_SEPARATOR . 'src' . str_replace('\\', DIRECTORY_SEPARATOR, strtolower(substr($class, strlen(__NAMESPACE__)))) . '.php';
			if (file_exists($fileLocation)) {
				include_once($fileLocation);
			}
		}
	}
);