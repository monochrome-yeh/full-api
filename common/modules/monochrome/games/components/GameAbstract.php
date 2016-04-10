<?php
namespace common\modules\monochrome\games\components;

abstract class GameAbstract {
	abstract public function createGame();
	abstract public function endGame();
	abstract public function loadGame();
	abstract public function saveGame();
}