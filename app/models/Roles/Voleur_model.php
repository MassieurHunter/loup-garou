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
     * @param int $cardNumber
     */
    public function action($gameUid, $oPlayer, $cardNumber) {
        $this->switchWithPlayer($gameUid, $oPlayer, $cardNumber);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $playerUid
     */
    public function switchWithPlayer($gameUid, $oPlayer, $playerUid){

        $this->load->model('player_model', 'otherPlayer');
        $this->otherPlayer->init($playerUid);
        $otherPlayerCardRole = $this->otherPlayer->getCurrentRoleModel($gameUid);
        $playerRole = $oPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $otherPlayerCardRole);
        $this->otherPlayer->addNewRole($gameUid, $playerRole);

    }

}