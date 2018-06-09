<?php

/**
 * Class Loup_model
 *
 * @property Player_model $otherLoup1
 * @property Player_model $otherLoup2
 * @property Player_model $middleCard
 */
class Loup_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {

		return $this->getOtherLoup($arguments['gameUid'], $arguments['currentPlayer']);

	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getOtherLoup(int $gameUid, Player_model $oPlayer): array {
		$number = 0;
		$arrReturn = [
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
		];
		
		
		$doppelLoupSubSubQuery = $this->db
			->select($oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->where($this->primary_key, self::DOPPELGANGER)
			->where($oPlayer->primary_key . ' > ', 3)
			->where('order', 0)
			->get_compiled_select($oPlayer->player_roles_table);


		$doppelLoupSubQuery = $this->db
			->select($oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->where($this->primary_key, $this->getRoleUid())
			->where($oPlayer->primary_key . ' != ', $oPlayer->getPlayerUid())
			->where($oPlayer->primary_key . ' > ', 3)
			->where('order', 1)
			->where_in($oPlayer->primary_key, $doppelLoupSubSubQuery, false)
			->get_compiled_select($oPlayer->player_roles_table);

		$arrLoups = $this->db
			->select($oPlayer->table . '.*')
			->join($oPlayer->player_roles_table, $oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->group_start()
			->where($this->primary_key, $this->getRoleUid())
			->where($oPlayer->primary_key . ' != ', $oPlayer->getPlayerUid())
			->where($oPlayer->primary_key . ' > ', 3)
			->where('order', 0)
			->group_end()
			->or_group_start()
			->where_in($oPlayer->primary_key, $doppelLoupSubQuery, false)
			->group_end()
			->group_by($oPlayer->primary_key)
			->get($oPlayer->table)
			->result();


		foreach ($arrLoups as $key => $loup) {

			$key2 = $key + 1;
			$this->load->model('player_model', 'otherLoup' . $key2);
			$this->{'otherLoup' . $key2}->init(false, $loup);
			$arrReturn['player_' . $key2] = $this->{'otherLoup' . $key2}->getBasicInfos();
			$number++;

		}

		$arrReturn['result'] = $number > 0 ? 1 : 0;

		return $arrReturn;

	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function secondAction($arguments): array {

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


}