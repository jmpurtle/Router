<?php
namespace Magnus\Request;

class Context {
	protected $requestURI;
	protected $baseURI;
	protected $requestPath;
	protected $appMode;
	protected $logger;
	protected $controllerPrefix;

	public function __construct(Array $config) {
		$this->documentRoot     = isset($config['documentRoot']) 
								? $this->normalizeURI($config['documentRoot']) 
								: $this->normalizeURI($_SERVER['DOCUMENT_ROOT']);

		$this->requestURI       = isset($config['requestURI']) 
								? $this->normalizeURI($config['requestURI'])
								: '/';

		$this->baseURI          = isset($config['baseURI']) 
								? str_replace($this->documentRoot, '', $this->normalizeURI($config['baseURI']))
								: '/';

		$this->assetRoot        = isset($config['assetRoot'])
								? $this->normalizeURI($config['assetRoot'])
								: rtrim($this->baseURI, '/') . '/src/assets';

		$this->appMode          = isset($config['appMode']) 
								? $config['appMode'] 
								: 'DEVELOPMENT';

		$this->logger           = isset($config['logger']) 
								? $config['logger'] 
								: null;

		$this->controllerPrefix = isset($config['controllerPrefix']) 
								? $config['controllerPrefix'] 
								: 'Magnus\\Controllers\\';
		
		$this->requestPath = explode('/', $this->requestURI);
		
		if (end($this->requestPath) === '') {
		    array_pop($this->requestPath);
		}

		if ($this->requestPath[0] === '') {
		    array_shift($this->requestPath);
		}

	}

	public function normalizeURI($uri) {
		return strtolower(str_replace('\\', '/', $uri));
	}

	public function getRequestURI() {
		return $this->requestURI;
	}

	public function getAssetRoot() {
		return $this->assetRoot;
	}

	public function setRequestURI($uri) {
		$this->requestURI = $this->normalizeURI($uri);
	}

	public function getBaseURI() {
		return $this->baseURI;
	}
	public function setBaseURI($uri) {
		$this->baseURI = $this->normalizeURI($uri);
	}

	public function getRequestPath() {
		return $this->requestPath;
	}

	public function getAppMode() {
		return $this->appMode;
	}

	public function getLogger() {
		return $this->logger;
	}

	public function getControllerPrefix() {
		return $this->controllerPrefix;
	}

}