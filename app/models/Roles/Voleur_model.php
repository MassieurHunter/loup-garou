<?php

class Voleur_model extends Role_model
{
    /**
     * @var Player_model
     */
    public $otherPlayer;

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $playerUid
     */
    public function action(int $gameUid, Player_model $oPlayer, int $playerUid)
    {
        $this->switchWithPlayer($gameUid, $oPlayer, $playerUid);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $playerUid
     */
    private function switchWithPlayer(int $gameUid, Player_model $oPlayer, int $playerUid)
    {

        $this->load->model('player_model', 'otherPlayer');
        $this->otherPlayer->init($playerUid);
        $otherPlayerCardRole = $this->otherPlayer->getCurrentRoleModel($gameUid);
        $playerRole = $oPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $otherPlayerCardRole);
        $this->otherPlayer->addNewRole($gameUid, $playerRole);

    }

}