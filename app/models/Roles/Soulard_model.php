<?php

class Soulard_model extends Role_model
{
    /**
     * @var Player_model
     */
    public $midleCard;

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments) : array {
        return $this->switchWithMiddle($arguments['gameUid'], $arguments['currentPlayer'], $arguments['card_1']);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $cardNumber
     * @return array
     */
    public function switchWithMiddle(int $gameUid, Player_model $oPlayer, int $cardNumber) : array{

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);
        $middleCardRole = $this->midleCard->getCurrentRoleModel($gameUid);
        $playerRole = $oPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $middleCardRole);
        $this->middleCard->addNewRole($gameUid, $playerRole);

        return $oPlayer->getBasicInfos();

    }

}