<?php

/**
 * Class Voyante_model
 *
 */
class Voyante_model extends Role_model
{

    /**
     * @var Player_model
     */
    public $player;

    /**
     * @var Player_model
     */
    public $card1;

    /**
     * @var Player_model
     */
    public $card2;

    /**
     * @param array|mixed[] $mode
     * @param $gameUid
     * @param $firstCard
     * @param null $secondCard
     * @return array|string
     */
    public function action($mode, $gameUid, $firstCard, $secondCard = null) {
        return $mode == 'onePlayer'
            ? $this->getOnePlayerRole($gameUid, $firstCard)
            : $this->getTwoMiddleCard($gameUid, $firstCard, $secondCard );
    }

    /**
     * @param $gameUid
     * @param $playerUid
     * @return string
     */
    private function getOnePlayerRole($gameUid, $playerUid){

        $this->load->model('player_model', 'player');
        $this->player->init($playerUid);

        return $this->player->getCurrentRoleName($gameUid);
    }

    /**
     *
     * @param int $gameUid
     * @param int $firstCard
     * @param int $secondCard
     *
     * @return array
     */
    private function getTwoMiddleCard($gameUid, $firstCard, $secondCard ) {

        $this->load->model('player_model', 'card1');
        $this->load->model('player_model', 'card2');

        $this->card1->init($firstCard);
        $this->card2->init($secondCard);

        return [
          $this->card1->getCurrentRoleModel($gameUid),
          $this->card2->getCurrentRoleModel($gameUid),
        ];


    }

}