<?php
namespace Magnus\Controllers;

class RootController {
	public $superbowl = 'SuperBowlController';

	public function __invoke($args = array()) {
		return [
			'view'  => 'index',
		];
	}

	public function lookup($path, $context) {
		return ['None', array_intersect($path, explode('/', $context->getBaseURI()))];
	}

}