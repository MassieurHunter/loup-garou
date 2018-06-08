<?php

/**
 * Class Sorciere_model
 *
 * @property Player_model $middleCard
 * @property Player_model $player
 */
class Sorciere_model extends Role_model
{
	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {

		return $this->getOneMiddleCard($arguments['gameUid'], $arguments['card_1'], $arguments['currentPlayer']);

	}

	/**
	 * @param int $gameUid
	 * @param int $cardNumber
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getOneMiddleCard(int $gameUid, int $cardNumber, Player_model $oPlayer): array {

		$this->load->model('player_model', 'middleCard');
		$this->middleCard->init($cardNumber);

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'card_1'        => $this->middleCard->getBasicInfos(),
			'role_1'        => $this->middleCard->getCurrentRoleWithBasicInfos($gameUid),
		];

	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function secondAction($arguments): array {

		return $this->switchPlayersRole($arguments['gameUid'], $arguments['card_1'], $arguments['player_1'], $arguments['currentPlayer']);

	}

	/**
	 * @param int $gameUid
	 * @param int $cardNumber
	 * @param int $playerUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function switchPlayersRole(int $gameUid, int $cardNumber, int $playerUid, Player_model $oPlayer): array {
		$this->load->model('player_model', 'middleCard');
		$this->load->model('player_model', 'player');

		$this->middleCard->init($cardNumber);
		$this->player->init($playerUid);

		$cardRoleModel = $this->middleCard->getCurrentRoleModel($gameUid);
		$playerRoleModel = $this->player->getCurrentRoleModel($gameUid);

		$this->middleCard->addNewRole($gameUid, $playerRoleModel);
		$this->player->addNewRole($gameUid, $cardRoleModel);

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'player_1'      => $this->player->getBasicInfos(),
			'card_1'        => $this->middleCard->getBasicInfos(),
			'role_1'        => $cardRoleModel->getBasicInfos(),
		];
	}

}