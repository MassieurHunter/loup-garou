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
     * @return string
     */
    public function action() {

        return $this->getOtherLoup();

    }

    /**
     * @return string
     */
    public function secondAction()
    {

        return $this->getOneMiddleCard();

    }


    /**
     * @return String
     */
    private function getOtherLoup()
    {
        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;
        $oGame = $CI->oCurrentGame;

        $otherLoup = $this->db
            ->select($oPlayer->table . '.*')
            ->join($oPlayer->player_roles_table, $oPlayer->primary_key)
            ->where('gameUid', $oGame->getGameUid())
            ->where($this->primary_key, $this->getRoleUid())
            ->where('order', 0)
            ->get($oPlayer->table)
            ->row();

        $this->load->model('player_model', 'otherLoup');
        $this->otherLoup->init(false, $otherLoup);

        return $this->otherLoup->getName();

    }

    /**
     *
     * @param int $cardNumber
     *
     * @return string
     */
    private function getOneMiddleCard($cardNumber)
    {
        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;
        $oGame = $CI->oCurrentGame;

        $middleCard = $this->db
            ->select($oPlayer->table . '.*')
            ->join($oPlayer->player_roles_table, $oPlayer->primary_key)
            ->where('gameUid', $oGame->getGameUid())
            ->where($this->primary_key, $this->getRoleUid())
            ->where('playerUid', $cardNumber)
            ->where('order', 0)
            ->get($oPlayer->table)
            ->row();

        $this->load->model('player_model', 'otherLoup');
        $this->middleCard->init(false, $middleCard);

        return $this->middleCard->getCurrentRoleName();

    }


}