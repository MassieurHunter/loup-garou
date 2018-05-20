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
     * @return array
     */
    public function action()
    {
        return $this->getActionChoices();
    }


    /**
     * @return array
     */
    private function getActionChoices(): array
    {
        return [
            [
                'type' => 'onePlayerRole',
                'name' => $this->lang->line('get_one_player_role'),
                'nbTarget' => 1,
                'targetType' => 'player',
            ],
            [
                'type' => 'oneTwoCardsRole',
                'name' => $this->lang->line('get_two_cards_role'),
                'nbTarget' => 2,
                'targetType' => 'card',
            ],
        ];
    }

    /**
     * @param string $mode
     * @param int $gameUid
     * @param int $firstCard
     * @param int $secondCard
     * @return array
     */
    public function secondAction(string $mode, int $gameUid, int $firstCard, int $secondCard = 0): array
    {
        return $mode == 'onePlayerRole'
            ? $this->getOnePlayerRole($gameUid, $firstCard)
            : $this->getTwoMiddleCard($gameUid, $firstCard, $secondCard);
    }

    /**
     * @param int $gameUid
     * @param int $playerUid
     * @return array
     */
    private function getOnePlayerRole(int $gameUid, int $playerUid): array
    {

        $this->load->model('player_model', 'player');
        $this->player->init($playerUid);

        return [
            $this->player->getCurrentRoleName($gameUid)
        ];
    }

    /**
     *
     * @param int $gameUid
     * @param int $firstCard
     * @param int $secondCard
     *
     * @return array
     */
    private function getTwoMiddleCard(int $gameUid, int $firstCard, int $secondCard): array
    {

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