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
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
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
     * @param array $arguments
     * @return array
     */
    public function secondAction($arguments): array
    {
        return $arguments['mode'] == 'onePlayerRole'
            ? $this->getOnePlayerRole($arguments['gameUid'], $arguments['player_1'])
            : $this->getTwoMiddleCard($arguments['gameUid'], $arguments['card_1'], $arguments['card_2']);
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

        $this->player->getCurrentRoleWithBasicInfos($gameUid);
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
            $this->card1->getCurrentRoleWithBasicInfos($gameUid),
            $this->card2->getCurrentRoleWithBasicInfos($gameUid),
        ];


    }

}