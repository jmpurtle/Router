<?php
namespace Magnus\Controllers;

class SuperBowlController {

	public function __invoke($args = array()) {
		$bowlList = new \Magnus\Models\Game();
		return array(
			'view' => 'bowlList',
			'games' => $bowlList->getAllRecords() 
		);
	}

	public function lookup($args = []) {
		$gameID = $args[0];
		return [new GameController($gameID), [$args[0]]];
	}

}