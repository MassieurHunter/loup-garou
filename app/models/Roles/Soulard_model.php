<?php

/**
 * Class Soulard_model
 *
 * @property Player_model $middleCard
 */
class Soulard_model extends Role_model
{

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
    {
        return $this->switchWithMiddle($arguments['gameUid'], $arguments['currentPlayer'], $arguments['card_1']);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @param int $cardNumber
     * @return array
     */
    public function switchWithMiddle(int $gameUid, Player_model $oPlayer, int $cardNumber): array
    {

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);
        $middleCardRole = $this->midleCard->getCurrentRoleModel($gameUid);
        $playerRole = $oPlayer->getCurrentRoleModel($gameUid);

        $oPlayer->addNewRole($gameUid, $middleCardRole);
        $this->middleCard->addNewRole($gameUid, $playerRole);

        return [
            'type' => 'playerAndCard',
            'number' => 1,
            'player_1' => $oPlayer->getBasicInfos(),
            'card_1' => $this->middleCard->getBasicInfos(),
        ];

    }

}