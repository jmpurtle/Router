<?php
use phpunit\framework\TestCase;

class TestSuite extends TestCase {

	private function route($router, $context, $obj, $path) {
		foreach ($router($context, $obj, $path) as list($previous, $obj, $isEndpoint)) {
			if ($isEndpoint) { break; }
		}

		return [$context, $previous, $obj, $isEndpoint];
	}

	public function setUp() {
		putenv('APP_ROLE=TEST');
		$this->router   = new \Routers\ObjectRouter();
		$this->context  = null;
	}

	public function tearDown() {
		unset($this->router);
		unset($this->context);
	}

	public function testRootRoute() {
		$baseObj = '\\Controllers\\RootController';
		$response = $this->route($this->router, $this->context, $baseObj, []);
		$challenge = [null, null, new \Controllers\RootController($this->context), true];

		$this->assertEquals($response, $challenge);
	}

}