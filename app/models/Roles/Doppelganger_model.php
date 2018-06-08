<?php

/**
 * Class Doppelganger_model
 *
 * @property Player_model $otherPlayer
 */
class Doppelganger_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {
		return $this->copyPlayerRole($arguments['gameUid'], $arguments['currentPlayer'], $arguments['player_1']);
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @param int $playerUid
	 * @return array
	 */
	private function copyPlayerRole(int $gameUid, Player_model $oPlayer, int $playerUid): array {

		$this->load->model('player_model', 'otherPlayer');
		$this->otherPlayer->init($playerUid);
		/** @var $otherPlayerCardRole Role_model */
		$otherPlayerCardRole = $this->otherPlayer->getCurrentRoleModel($gameUid);

		$oPlayer->addNewRole($gameUid, $otherPlayerCardRole);

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'player_1'      => $this->otherPlayer->getBasicInfos(),
			'role_1'        => $otherPlayerCardRole->getBasicInfos(),
		];

	}

}