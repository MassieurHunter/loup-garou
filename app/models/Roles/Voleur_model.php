<?php

/**
 * Class Voleur_model
 *
 * @property Player_model $otherPlayer
 *
 */
class Voleur_model extends Role_model
{
	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {
		return $this->switchWithPlayer($arguments['gameUid'], $arguments['currentPlayer'], $arguments['player_1']);
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @param int $playerUid
	 * @return array
	 */
	private function switchWithPlayer(int $gameUid, Player_model $oPlayer, int $playerUid): array {

		$this->load->model('player_model', 'otherPlayer');
		$this->otherPlayer->init($playerUid);
		$otherPlayerRole = $this->otherPlayer->getCurrentRole($gameUid);
		$playerRole = $oPlayer->getCurrentRole($gameUid);

		$oPlayer->addNewRole($gameUid, $otherPlayerRole);
		$this->otherPlayer->addNewRole($gameUid, $playerRole);

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'player_1'      => $this->otherPlayer->getBasicInfos(),
			'role_1'        => $otherPlayerRole->getBasicInfos(),
		];

	}

}