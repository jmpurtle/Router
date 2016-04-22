<?php
namespace Controllers {

	class PostController {

		private $categoryID;
		private $threadID;
		private $postID;
		private $context;

		public function __construct($categoryID, $threadID, $postID, $context = null) {
			$this->categoryID = $categoryID;
			$this->threadID   = $threadID;
			$this->postID     = $postID;
			$this->context    = $context;
		}

		public function __invoke($path = []) {

			$model = new \Models\ForumModel();

			$response = $model->getPost($this->categoryID, $this->threadID, $this->postID);

			if (isset($response[$this->postID])) {

				return [
					'view'   => 'post',
					'postID' => $this->postID,
					'post'   => $response
				];

			}

			return [
				'view'     => 'thread',
				'threadID' => $this->threadID,
				'thread'   => $response
			];

		}


	}


}