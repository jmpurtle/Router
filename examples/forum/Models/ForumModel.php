<?php
namespace Models {
	
	class ForumModel {

		//A full mock database, obviously, you're not loading the entire thing but making queries with an actual DB
		private $forumDocument;

		public function __construct() {
			$this->forumDocument = include('Database/forum.php');
		}

		public function getCategory($categoryID = null) {
			//imitate a database call
			$categories = $this->forumDocument['categories'];

			if (isset($categories[$categoryID])) {
				return [
					$categoryID => $categories[$categoryID]
				];
			}

			return $categories;

		}

		public function getThread($categoryID, $threadID = null) {
			//imitate a database call
			$threads = $this->forumDocument['categories'][$categoryID]['threads'];

			if (isset($threads[$threadID])) {
				return [
					$threadID => $threads[$threadID]
				];
			}

			return $threads;

		}

		public function getPost($categoryID, $threadID, $postID = null) {
			//imitate a database call
			$posts = $this->forumDocument['categories'][$categoryID]['threads'][$threadID]['posts'];
			
			if (isset($posts[$postID])) {
				return [
					$postID => $posts[$postID]
				];
			}

			return $posts;

		}

	}
	
}