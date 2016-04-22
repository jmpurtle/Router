<?php
namespace Controllers {

	class ThreadController {

		private $categoryID;
		private $threadID;
		private $context;

		public function __construct($categoryID, $threadID, $context = null) {
			$this->categoryID = $categoryID;
			$this->threadID   = $threadID;
			$this->context    = $context;
		}

		public function __invoke($path = []) {

			$model = new \Models\ForumModel();

			$response = $model->getThread($this->categoryID, $this->threadID);

			if (isset($response[$this->threadID])) {

				return [
					'view'      => 'thread',
					'threadID'  => $this->threadID,
					'thread'    => $response
				];

			}

			return [
				'view'       => 'category',
				'categoryID' => $this->categoryID,
				'threads'    => $response
			];

		}

		public function __get($postID) {
			return new PostController($this->categoryID, $this->threadID, $postID, $this->context);
		}

	}

}