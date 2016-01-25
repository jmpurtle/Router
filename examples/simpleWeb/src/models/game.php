<?php
namespace Magnus\Models;

class Game {
	protected $gameListData;
	protected $gameID;

	public function __construct($gameID = 0) {
		/* Hard coded data for the sake of time and avoid requiring a database set up and seeded for this demo. */
		$this->gameListData = file_exists('src/models/gameDataRepository.php') ? include 'src/models/gameDataRepository.php' : [];
		$this->gameID = $gameID;
	}

	public function getAllRecords() {
		return $this->gameListData;
	}

	public function getRecord() {
		/* For the interest of time, we will simply loop over the data for our desired result. This can be further optimized later on */
		foreach ($this->gameListData as $gameRecord) {
			if ($gameRecord['gameID'] == $this->gameID) {
				return $gameRecord;
			}
		}
		return false;

	}
}