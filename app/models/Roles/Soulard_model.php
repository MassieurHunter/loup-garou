<?php

/**
 * Class Soulard_model
 *
 * @property Player_model $middleCard
 */
class Soulard_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {
		return $this->switchWithMiddle($arguments['gameUid'], $arguments['currentPlayer'], $arguments['card_1']);
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @param int $cardNumber
	 * @return array
	 */
	public function switchWithMiddle(int $gameUid, Player_model $oPlayer, int $cardNumber): array {

		$this->load->model('player_model', 'middleCard');
		$this->middleCard->init($cardNumber);
		$middleCardRole = $this->middleCard->getCurrentRole($gameUid);
		$playerRole = $oPlayer->getCurrentRole($gameUid);

		$oPlayer->addNewRole($gameUid, $middleCardRole);
		$this->middleCard->addNewRole($gameUid, $playerRole);

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'card_1'        => $this->middleCard->getBasicInfos(),
			'role_1'        => $middleCardRole->getBasicInfos(),
		];

	}

}