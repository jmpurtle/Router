<?php
namespace Controllers {

	class PeopleController {

		private $context;

		public function __construct($context = null) {
			$this->context = $context;
		}

		public function __invoke() {
			return "I'm all people.";
		}

		public function __get($id = null) {
			return new PersonController($id);
		}

	}

}