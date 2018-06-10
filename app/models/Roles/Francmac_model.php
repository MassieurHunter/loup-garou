<?php

/**
 * Class Francmac_model
 *
 * @property Player_model $otherFrancMac
 */
class Francmac_model extends Role_model
{

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array {
		return $this->getOtherFrancMac($arguments['gameUid'], $arguments['currentPlayer']);
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getOtherFrancMac(int $gameUid, Player_model $oPlayer): array {

		$number = 0;
		$arrReturn = [
			'gameUid'       => $gameUid,
			'currentPlayer' => $oPlayer->getBasicInfos(),
		];

		$doppelFrancMacSubSubQuery = $this->db
			->select($oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->where($this->primary_key, self::DOPPELGANGER)
			->where($oPlayer->primary_key . ' > ', 3)
			->where('order', 0)
			->get_compiled_select($oPlayer->player_roles_table);


		$doppelFrancMacSubQuery = $this->db
			->select($oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->where($this->primary_key, $this->getRoleUid())
			->where($oPlayer->primary_key . ' != ', $oPlayer->getPlayerUid())
			->where($oPlayer->primary_key . ' > ', 3)
			->where('order', 1)
			->where_in($oPlayer->primary_key, $doppelFrancMacSubSubQuery, false)
			->get_compiled_select($oPlayer->player_roles_table);
		
		$arrFrancMacs = $this->db
			->select($oPlayer->table . '.*')
			->join($oPlayer->player_roles_table, $oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->group_start()
			->where($oPlayer->primary_key . ' != ', $oPlayer->getPlayerUid())
			->where($oPlayer->primary_key . ' > ', 3)
			->where($this->primary_key, $this->getRoleUid())
			->where('order', 0)
			->group_end()
			->or_group_start()
			->where_in($oPlayer->primary_key, $doppelFrancMacSubQuery, false)
			->group_end()
			->group_by($oPlayer->primary_key)
			->get($oPlayer->table)
			->result();


		foreach ($arrFrancMacs as $key => $francMac) {

			$key2 = $key + 1;
			$this->load->model('player_model', 'otherFrancMac' . $key2);
			$this->{'otherFrancMac' . $key2}->init(false, $francMac);
			$arrReturn['player_' . $key2] = $this->{'otherFrancMac' . $key2}->getBasicInfos();
			$number++;

		}

		$arrReturn['result'] = $number > 0 ? 1 : 0;

		return $arrReturn;

	}

}