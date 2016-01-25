<?php
namespace Magnus\Controllers;

class GameController {
	protected $gameID;

	public function __construct($gameID) {
		if (!is_numeric($gameID) || $gameID < 1) {
			throw new \Magnus\Exceptions\HTTPNotFound();
		} else {
			$this->gameID = $gameID;
		}
	}

	public function __invoke($args = array()) {
		$gameModel = new \Magnus\Models\Game($this->gameID);
		return array(
			'view' => 'gameHighlights',
			'gameData' => $gameModel->getRecord()
		);
	}

}