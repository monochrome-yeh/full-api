<?php
namespace common\modules\monochrome\games;

use namespace common\modules\monochrome\games\models\GameModel;

class PokeGame extends GameAbstract {
	private $_game;

	private static $_instance;

	public static function api () {
		if (self::$_instance === null) {
			self::$_instance = new __CLASS__;

			return self::$_instance;
		}
	}

	public function createGame() {
		new PokeModel();
	}
	public function loadGame(GameModel $id) {
		$this->_game = PokeModel::findOne(['_id' => $id]);
	}
	public function startGame() {
		
	}
	public function endGame() {
		
	}

	/* 2 = host, 1 = invitee, 0 = speceptor */
	public function roleCheck() {
		$uid = \Yii::$app->user->getId();
		if ($uid == $this->_game->status['host']) {
			return 2;
		}

		if (in_array($uid, $this->_game->status['invitee'])) {
			return 1;
		}

		return 0;	
	}

	public function is_agree_terms() {
    	if (in_array('pokeGame', is_array(\Yii::$app->user->getIdentity()->agree_terms)?\Yii::$app->user->getIdentity()->agree_terms:[])) {
    		return true;
    	}
    	return false;
	};				
}