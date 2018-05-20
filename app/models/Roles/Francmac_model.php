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
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return string
     */
    public function action(int $gameUid, Player_model $oPlayer): string
    {
        return $this->getOtherFrancMac($gameUid, $oPlayer)->getName();
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return Player_model
     */
    private function getOtherFrancMac(int $gameUid, Player_model $oPlayer): Player_model
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

        return $this->otherFrancMac;

    }

}