<?php

class Soulard_model extends Role_model
{
    /**
     * @var Player_model
     */
    public $midleCard;

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $cardNumber
     */
    public function action(int $gameUid, Player_model $oPlayer, int $cardNumber) {
        $this->switchWithMiddle($gameUid, $oPlayer, $cardNumber);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $cardNumber
     */
    public function switchWithMiddle(int $gameUid, Player_model $oPlayer, int $cardNumber){

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);
        $middleCardRole = $this->midleCard->getCurrentRoleModel($gameUid);
        $playerRole = $oPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $middleCardRole);
        $this->middleCard->addNewRole($gameUid, $playerRole);

    }

}