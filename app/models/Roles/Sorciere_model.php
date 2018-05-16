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
     * @param int $gameUid
     * @param int $cardNumber
     * @return string
     */
    public function action($gameUid, $cardNumber) {

        return $this->getOneMiddleCard($gameUid, $cardNumber);

    }

    /**
     *
     * @param int $gameUid
     * @param int $cardNumber
     *
     * @return string
     */
    private function getOneMiddleCard($gameUid, $cardNumber) {

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);

        return $this->middleCard->getCurrentRoleName($gameUid);

    }

    /**
     * @param int $gameUid
     * @param int $cardNumber
     * @param int $playerUid
     */
    public function secondAction($gameUid, $cardNumber, $playerUid) {

        $this->switchPlayersRole($gameUid, $cardNumber, $playerUid);

    }

    /**
     * @param int $gameUid
     * @param int $cardNumber
     * @param int $playerUid
     */
    private function switchPlayersRole($gameUid, $cardNumber, $playerUid) {
        $this->load->model('player_model', 'middleCard');
        $this->load->model('player_model', 'player');

        $this->middleCard->init($cardNumber);
        $this->player->init($playerUid);

        $player1RoleModel = $this->middleCard->getCurrentRoleModel($gameUid);
        $player2RoleModel = $this->player->getCurrentRoleModel($gameUid);

        $this->middleCard->addNewRole($gameUid, $player2RoleModel);
        $this->player->addNewRole($gameUid, $player1RoleModel);
    }

}