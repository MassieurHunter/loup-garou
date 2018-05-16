<?php

/**
 * Class Francmac_model
 *
 *
 */
class Francmac_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $otherFrancMac;

    /**
     * @return Player_model|void
     */
    public function action() {
        return $this->getOtherFrancMac();
    }

    /**
     * @return Player_model
     */
    private function getOtherFrancMac()
    {
        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;
        $oGame = $CI->oCurrentGame;

        $otherFrancMac = $this->db
            ->select($oPlayer->table . '.*')
            ->join($oPlayer->player_roles_table, $oPlayer->primary_key)
            ->where('gameUid', $oGame->getGameUid())
            ->where($this->primary_key, $this->getRoleUid())
            ->where('order', 0)
            ->get($oPlayer->table)
            ->row();

        $this->load->model('player_model', 'otherFrancMac');
        $this->otherFrancMac->init(false, $otherFrancMac);

        return $this->otherFrancMac;

    }

}