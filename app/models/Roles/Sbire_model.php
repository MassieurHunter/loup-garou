<?php

/**
 * Class Sbire_model
 *
 * @property Player_model $_loup
 * @property Player_model $loup1
 * @property Player_model $loup2
 * @property Player_model $loup3
 *
 */
class Sbire_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {

		return $this->getLoups($arguments['gameUid'], $arguments['currentPlayer']);

	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getLoups(int $gameUid, Player_model $oPlayer): array {

		$number = 0;
		$arrReturn = [
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
		];
		
		$this->load->model('player_model', '_loup');

		$arrLoups = $this->db
			->select($this->_loup->table . '.*')
			->join($this->_loup->player_roles_table, $this->_loup->primary_key)
			->where('gameUid', $gameUid)
			->where('roleuid', self::LOUP)
			->where('order', 0)
			->where($this->_loup->primary_key . ' >', 3)//playerUid [1, 2, 3] => middle cards
			->get($this->_loup->table)
			->result();

		foreach ($arrLoups as $key => $loup) {
			$key2 = $key + 1;
			$this->load->model('player_model', 'loup' . $key2);
			$this->{'loup' . $key2}->init(false, $loup);
			$arrReturn['player_' . $key2] = $this->{'loup' . $key2}->getBasicInfos();
			$number++;

		}

		$arrReturn['result'] = $number < 2 ? 1 / 2 : 1;

		return $arrReturn;

	}

}