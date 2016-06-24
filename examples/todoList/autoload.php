<?php
namespace TodoApp {

	spl_autoload_register(function ($className) {

		$className = str_replace("\\", '/', $className);
		$vendors = __DIR__ . '/vendor/';

		if (file_exists($vendors . $className . ".php")) {
			require_once $vendors . $className . ".php";
		} else if (file_exists(__DIR__ . "/" . $className . ".php")){
			require_once __DIR__ . "/" . $className . ".php";
		}
		
	});

}
