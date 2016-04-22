<?php
namespace Controllers {

	class RootController {

		private $context;

		public function __construct($context = null) {
			$this->context = $context;
		}

		public function __invoke($path = []) {
			$model = new \Models\ForumModel();

			$response = $model->getCategory(null);

			return [
				'view'       => 'index',
				'categories' => $response
			];

		}

		public function __get($category) {
			return new CategoryController($category, $this->context);
		}

	}

}