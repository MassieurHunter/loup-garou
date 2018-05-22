<?php

class Loup_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $otherLoup;

    /**
     * @var Player_model
     */
    public $middleCard;

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

        return $this->otherLoup->getBasicInfos();

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

        return $this->middleCard->getCurrentRoleWithBasicInfos($gameUid);

    }


}