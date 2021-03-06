<?php


/**
 * Class Insomniaque_model
 */
class Insomniaque_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {
		return $this->getCurrentRole($arguments['gameUid'], $arguments['currentPlayer']);
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getCurrentRole(int $gameUid, Player_model $oPlayer): array {

		return [
			'result'        => 1,
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
			'role_1'        => $oPlayer->getCurrentRoleWithBasicInfos($gameUid),
		];

	}

}