<?php

/**
 * Class Voyante_model
 *
 * @property Player_model $player
 * @property Player_model $card1
 * @property Player_model $card2
 *
 */
class Voyante_model extends Role_model
{

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
				'name' => 'get_one_player_role',
				'nbTargets' => 1,
				'targetType' => 'player',
			],
			[
				'type' => 'oneTwoCardsRole',
				'name' => 'get_two_cards_role',
				'nbTargets' => 2,
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
		return $arguments['type'] == 'onePlayerRole'
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
		$this->setSecondActionName('get_one_player_role');
			
		$this->load->model('player_model', 'player');
		$this->player->init($playerUid);

		return [
			'result' => 1,
			'player_1' => $this->player->getBasicInfos(),
			'role_1' => $this->player->getCurrentRoleWithBasicInfos($gameUid),
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
		$this->setSecondActionName('get_two_cards_role');

		$this->load->model('player_model', 'card1');
		$this->load->model('player_model', 'card2');

		$this->card1->init($firstCard);
		$this->card2->init($secondCard);

		return [
			'result' => 1,
			'card_1' => $this->card1->getBasicInfos(),
			'card_2' => $this->card2->getBasicInfos(),
			'role_1' => $this->card1->getCurrentRoleWithBasicInfos($gameUid),
			'role_2' => $this->card2->getCurrentRoleWithBasicInfos($gameUid),
		];


	}

}