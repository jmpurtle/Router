<?php
namespace Controllers {

	class RootController {

		private $context;
		public $people = '\\Controllers\\PeopleController';

		public function __construct($context = null) {
			$this->context = $context;
		}

	}

}