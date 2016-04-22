<?php
namespace Controllers {

	class PeopleController {

		private $context;

		public function __construct($context = null) {
			$this->context = $context;
		}

		public function __invoke() {
			return [
				'view'           => 'controllerResponse',
				'controllerData' => 'PeopleController default action'
			];
		}

		public function __get($id = null) {
			return new PersonController($id);
		}

	}

}