<?php

/**
 * Class Francmac_model
 */
class Francmac_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $otherFrancMac;

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
            ->where($this->primary_key, $this->getRoleUid())
            ->where('order', 0)
            ->get($oPlayer->table)
            ->row();

        $this->load->model('player_model', 'otherFrancMac');
        $this->otherFrancMac->init(false, $otherFrancMac);

        return $this->otherFrancMac->getBasicInfos();

    }

}