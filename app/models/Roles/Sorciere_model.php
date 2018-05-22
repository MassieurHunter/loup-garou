<?php

class Sorciere_model extends Role_model
{
    /**
     * @var Player_model
     */
    public $middleCard;

    /**
     * @var Player_model
     */
    public $player;

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
    {

        return $this->getOneMiddleCard($arguments['gameUid'], $arguments['card_1']);

    }

    /**
     *
     * @param int $gameUid
     * @param int $cardNumber
     *
     * @return array
     */
    private function getOneMiddleCard(int $gameUid, int $cardNumber) : array
    {

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);

        return $this->middleCard->getCurrentRoleWithBasicInfos($gameUid);

    }

    /**
     * @param array $arguments
     * @return array
     */
    public function secondAction($arguments) : array
    {

        return $this->switchPlayersRole($arguments['gameUid'], $arguments['card_1'], $arguments['player_1']);

    }

    /**
     * @param int $gameUid
     * @param int $cardNumber
     * @param int $playerUid
     * @return array
     */
    private function switchPlayersRole(int $gameUid, int $cardNumber, int $playerUid) : array
    {
        $this->load->model('player_model', 'middleCard');
        $this->load->model('player_model', 'player');

        $this->middleCard->init($cardNumber);
        $this->player->init($playerUid);

        $player1RoleModel = $this->middleCard->getCurrentRoleModel($gameUid);
        $player2RoleModel = $this->player->getCurrentRoleModel($gameUid);

        $this->middleCard->addNewRole($gameUid, $player2RoleModel);
        $this->player->addNewRole($gameUid, $player1RoleModel);

        return [
            $this->middleCard->getBasicInfos(),
            $this->player->getBasicInfos()
        ];
    }

}