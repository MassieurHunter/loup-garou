<?php

class Noiseuse_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $player1;

    /**
     * @var Player_model
     */
    public $player2;

    /**
     * @param int $gameUid
     * @param int $firstPlayerUid
     * @param int $secondPlayerUid
     */
    public function action($gameUid, $firstPlayerUid, $secondPlayerUid) {

        $this->switchPlayersRole($gameUid, $firstPlayerUid, $secondPlayerUid);

    }

    /**
     * @param int $gameUid
     * @param int $firstPlayerUid
     * @param int $secondPlayerUid
     */
    private function switchPlayersRole($gameUid, $firstPlayerUid, $secondPlayerUid){
        $this->load->model('player_model', 'player1');
        $this->load->model('player_model', 'player2');

        $this->player1->init($firstPlayerUid);
        $this->player2->init($secondPlayerUid);

        $player1RoleModel = $this->player1->getCurrentRoleModel($gameUid);
        $player2RoleModel = $this->player2->getCurrentRoleModel($gameUid);

        $this->player1->addNewRole($gameUid, $player2RoleModel);
        $this->player2->addNewRole($gameUid, $player1RoleModel);
    }

}