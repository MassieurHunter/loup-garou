<?php

/**
 * Class Loup_model
 *
 * @property Player_model $otherLoup
 * @property Player_model $middleCard
 */
class Loup_model extends Role_model
{

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
    {

        return $this->getOtherLoup($arguments['gameUid'], $arguments['currentPlayer']);

    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return array
     */
    private function getOtherLoup(int $gameUid, Player_model $oPlayer): array
    {

        $otherLoup = $this->db
            ->select($oPlayer->table . '.*')
            ->join($oPlayer->player_roles_table, $oPlayer->primary_key)
            ->where('gameUid', $gameUid)
            ->where($this->primary_key, $this->getRoleUid())
            ->where($oPlayer->primary_key, $oPlayer->getPlayerUid())
            ->where('order', 0)
            ->get($oPlayer->table)
            ->row();

        $this->load->model('player_model', 'otherLoup');
        $this->otherLoup->init(false, $otherLoup);

        return [
            'type' => 'player',
            'success' => $this->otherLoup->getPlayerUid() > 0,
            'number' => 1,
            'player_1' => $this->otherLoup->getBasicInfos(),
        ];

    }

    /**
     * @param array $arguments
     * @return array
     */
    public function secondAction($arguments): array
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
    private function getOneMiddleCard(int $gameUid, int $cardNumber): array
    {

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);

        return [
            'type' => 'cardRole',
            'number' => 1,
            'card_1' => $this->middleCard->getBasicInfos(),
            'role_1' => $this->middleCard->getCurrentRoleWithBasicInfos($gameUid),
        ];
    }


}