<?php

/**
 * Class Noiseuse_model
 *
 *  @property Player_model $player1
 *  @property Player_model $player2
 */
class Noiseuse_model extends Role_model
{

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
    {

        return $this->switchPlayersRole($arguments['gameUid'], $arguments['player_1'], $arguments['player_2']);


    }

    /**
     * @param int $gameUid
     * @param int $firstPlayerUid
     * @param int $secondPlayerUid
     * @return array
     */
    private function switchPlayersRole(int $gameUid, int $firstPlayerUid, int $secondPlayerUid): array
    {
        $this->load->model('player_model', 'player1');
        $this->load->model('player_model', 'player2');

        $this->player1->init($firstPlayerUid);
        $this->player2->init($secondPlayerUid);

        $player1RoleModel = $this->player1->getCurrentRoleModel($gameUid);
        $player2RoleModel = $this->player2->getCurrentRoleModel($gameUid);

        $this->player1->addNewRole($gameUid, $player2RoleModel);
        $this->player2->addNewRole($gameUid, $player1RoleModel);

        return [
            'type' => 'player',
            'number' => 2,
            'player_1' => $this->player1->getBasicInfos(),
            'player_2' => $this->player2->getBasicInfos(),
        ];
    }

}