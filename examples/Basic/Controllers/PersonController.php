<?php
namespace Controllers {

	class PersonController {

		public $id;

		public function __construct($id) {
			$this->id = $id;
		}

		public function __invoke() {
			return [
				'view'           => 'controllerResponse',
				'controllerData' => 'PersonController with ID of ' . $this->id . " default action"
			];
		}

		public function foo() {
			return [
				'view'           => 'controllerResponse',
				'controllerData' => 'PersonController with ID of ' . $this->id . ", foo action"
			];
		}

	}

}