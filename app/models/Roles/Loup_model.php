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
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return string
     */
    public function action(int $gameUid, Player_model $oPlayer): string
    {

        return $this->getOtherLoup($gameUid, $oPlayer);

    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return String
     */
    private function getOtherLoup(int $gameUid, Player_model $oPlayer): string
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

        return $this->otherLoup->getName();

    }

    /**
     * @param int $gameUid
     * @param int $cardNumber
     * @return string
     */
    public function secondAction(int $gameUid, int $cardNumber): string
    {

        return $this->getOneMiddleCard($gameUid, $cardNumber);

    }

    /**
     *
     * @param int $gameUid
     * @param int $cardNumber
     *
     * @return string
     */
    private function getOneMiddleCard(int $gameUid, int $cardNumber): string
    {

        $this->load->model('player_model', 'middleCard');
        $this->middleCard->init($cardNumber);

        return $this->middleCard->getCurrentRoleName($gameUid);

    }


}