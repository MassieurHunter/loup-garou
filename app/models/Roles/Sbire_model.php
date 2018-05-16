<?php

class Sbire_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $loup1;

    /**
     * @var Player_model
     */
    public $loup2;

    /**
     * @param int $gameUid
     * @return array
     */
    public function action($gameUid) {

        return $this->getLoups($gameUid);

    }

    /**
     * @param int $gameUid
     * @return array
     */
    private function getLoups($gameUid) {

        $this->load->model('player_model', 'loup1');
        $this->load->model('player_model', 'loup2');

        $arrLoups = $this->db
            ->select($this->loup1->table . '.*')
            ->join($this->loup1->player_roles_table, $this->loup1->primary_key)
            ->where('gameUid', $gameUid)
            ->where('model', 'loup')
            ->where('order', 0)
            ->where($this->loup1->primary_key . ' >', 3)//playerUid [1, 2, 3] => middle cards
            ->get($this->loup1->table)
            ->result();

        foreach ($arrLoups as $key => $loup) {

            $this->{'loup' . $key}->init(false, $loup);

        }

        return [
            $this->loup1->getName(),
            $this->loup2->getName(),
        ];

    }

}