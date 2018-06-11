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
			->select($this->_loup->table . '.*')
			->join($this->_loup->player_roles_table, $this->_loup->primary_key)
			->where('gameUid', $gameUid)
			->group_start()
			->where('roleuid', self::LOUP)
			->where($this->_loup->primary_key . ' >', 3)//playerUid [1, 2, 3] => middle cards
			->where('order', 0)
			->group_end()
			->or_group_start()
			->where_in($oPlayer->primary_key, $doppelLoupSubQuery, false)
			->group_end()
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