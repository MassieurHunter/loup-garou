<?php

class Doppelganger_model extends Role_model
{

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $playerUid
     * @return Role_model
     */
    public function action(int $gameUid, Player_model $oPlayer, int $playerUid) : Role_model
    {
        return $this->copyPlayerRole($gameUid, $oPlayer, $playerUid);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $playerUid
     * @return Role_model
     */
    private function copyPlayerRole(int $gameUid, Player_model $oPlayer, int $playerUid) : Role_model
    {

        $this->load->model('player_model', 'otherPlayer');
        $this->otherPlayer->init($playerUid);
        $otherPlayerCardRole = $this->otherPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $otherPlayerCardRole);

        return $otherPlayerCardRole;

    }

}