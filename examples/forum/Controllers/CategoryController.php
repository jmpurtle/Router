<?php
namespace Controllers {

	class CategoryController {

		private $context;
		private $categoryID;

		public function __construct($categoryID = null, $context = null) {
			$this->categoryID = $categoryID;
			$this->context    = $context;
		}

		public function __invoke($path = []) {

			$model = new \Models\ForumModel();

			$response = $model->getCategory($this->categoryID);

			if (isset($response[$this->categoryID])) {
				return [
					'view'        => 'category',
					'categoryID'  => $this->categoryID,
					'category'    => $response
				];
			}

			return [
				'view'       => 'index',
				'categories' => $response
			];

		}

		public function __get($threadID) {
			return new ThreadController($this->categoryID, $threadID, $this->context);
		}

	}

}