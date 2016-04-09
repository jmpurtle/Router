<?php
namespace Controllers {

	class PersonController {

		public $id;

		public function __construct($id) {
			$this->id = $id;
		}

		public function __invoke() {
			return "Hello, I'm " . $this->id;
		}

		public function foo() {
			return "User " . $this->id . ", I pity da foo!";
		}

	}

}