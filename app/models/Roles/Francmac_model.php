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
	public function firstAction($arguments): array
	{
		return $this->getOtherFrancMac($arguments['gameUid'], $arguments['currentPlayer'])->getName();
	}

	/**
	 * @param int $gameUid
	 * @param Player_model $oPlayer
	 * @return array
	 */
	private function getOtherFrancMac(int $gameUid, Player_model $oPlayer): array
	{
		$otherFrancMac = $this->db
			->select($oPlayer->table . '.*')
			->join($oPlayer->player_roles_table, $oPlayer->primary_key)
			->where('gameUid', $gameUid)
			->where($oPlayer->primary_key . ' != ', $oPlayer->getPlayerUid())
			->where($oPlayer->primary_key . ' > ',  3)
			->where($this->primary_key, $this->getRoleUid())
			->where('order', 0)
			->get($oPlayer->table)
			->row();

		$this->load->model('player_model', 'otherFrancMac');
		$this->otherFrancMac->init(false, $otherFrancMac);

		return [
			'result' => $this->otherFrancMac->getPlayerUid() > 0 ? 1 : 0,
			'player_1' => $this->otherFrancMac->getBasicInfos(),
		];

	}

}