<?php

/**
 * Class Statistics_model
 *
 * @property Player_model $_player
 * @property Game_model $_game
 * @property History_model $_history
 */
class Statistics_model extends MY_Model
{
	/**
	 * @var Game_model[]
	 */
	protected $arrGames = [];

	/**
	 * Log_model constructor.
	 * @param array $arrParams
	 */
	public function __construct(array $arrParams = []) {
		parent::__construct($arrParams);
	}

	/**
	 * @return array
	 */
	public function getOverallRanking(): array {

		$arrGames = $this->getArrGames();
		$nbGames = count($arrGames);

		$arrStats = [];

		foreach ($arrGames as $gameUid => $oGame) {

			$gameHistories = $oGame->getArrHistories();

			foreach ($oGame->getRealPlayers() as $playerUid => $oPlayer) {

				$oPlayerGameHistory = $gameHistories[$playerUid];

				if (!isset($arrStats[$oPlayer->getPlayerUid()])) {
					$arrStats[$oPlayer->getPlayerUid()] = [
						'player'             => $oPlayer->getBasicInfos(),
						'games'              => 0,
						'games_loup'         => 0,
						'games_tanneur'      => 0,
						'games_villageois'   => 0,
						'wins'               => 0,
						'wins_loup'          => 0,
						'wins_tanneur'       => 0,
						'wins_villageois'    => 0,
						'losses'             => 0,
						'losses_loup'        => 0,
						'losses_tanneur'     => 0,
						'losses_villageois'  => 0,
						'percent'            => '-',
						'percent_loup'       => '-',
						'percent_tanneur'    => '-',
						'percent_villageois' => '-',

					];
				}

				$playerStats = &$arrStats[$oPlayer->getPlayerUid()];
				$playerStats['games']++;

//				$playerRole = $oPlayer->getCurrentRoleModel($gameUid);
				$suffix = '_' . $oPlayerGameHistory->getTeam();

				$playerStats['games' . $suffix]++;

				if ($oPlayerGameHistory->isWinner()) {

					$playerStats['wins']++;
					$playerStats['wins' . $suffix]++;

				} else {

					$playerStats['losses']++;
					$playerStats['losses' . $suffix]++;

				}

				$playerStats['percent'] = round($playerStats['wins'] / $playerStats['games'] * 100, 2) . '%';
				$playerStats['percent' . $suffix] = round($playerStats['wins' . $suffix] / $playerStats['games' . $suffix] * 100, 2) . '%';

			}

		}

		return [
			'nbGames' => $nbGames,
			'stats'   => $arrStats,
		];

	}

	/**
	 * @return Game_model[]
	 */
	public function getArrGames(): array {

		if (empty($this->arrGames)) {

			$this->initGames();

		}

		return $this->arrGames;

	}

	public function initGames() {

		$this->load->model('game_model', '_game');
		$this->load->model('player_model', '_player');
		$this->load->model('history_model', '_history');

		$arrFieldsGame = $this->_game->getArrFields();
		$arrFieldsPlayer = $this->_player->getArrFields();
		$arrFieldsHistory = $this->_history->getArrFields();

		$select = $this->_game->getConcat() . ' gameInfos';
		$select .= ', ' . $this->_player->getGroupConcat() . ' playersInfos';
		$select .= ', ' . $this->_history->getGroupConcat() . ' historiesInfos';

		$arrGames = $this->db
			->select($select)
			->from($this->_game->table)
			->join($this->_history->table, $this->_game->primary_key)
			->join($this->_player->table, $this->_player->primary_key)
			->group_by($this->_game->primary_key)
			->order_by($this->_game->primary_key)
			->get()
			->result();

		foreach ($arrGames as $game) {

			$arrPlayers = [];
			$arrFlatPlayers = array_unique(explode(',', $game->playersInfos));
			$arrHistories = [];
			$arrFlatHistories = array_unique(explode(',', $game->historiesInfos));

			foreach ($arrFlatHistories as $key => $flatHistory) {

				$arrHistory = explode(self::CONCAT_SEPARATOR, $flatHistory);
				$arrHistoryWithKeys = [];

				foreach ($arrHistory as $key2 => $value) {

					if ($arrFieldsHistory[$key2] == $this->_history->primary_key && $value == '') {

						continue 2;

					}

					$arrHistoryWithKeys[$arrFieldsHistory[$key2]] = $value;

				}

				$oHistory = clone $this->_history;
				$oHistory->init(false, $arrHistoryWithKeys);
				$arrHistories[$oHistory->getPlayerUid()] = $oHistory;
			}

			foreach ($arrFlatPlayers as $key => $flatPlayer) {

				$arrPlayer = explode(self::CONCAT_SEPARATOR, $flatPlayer);
				$arrPlayerWithKeys = [];

				foreach ($arrPlayer as $key2 => $value) {

					if ($arrFieldsPlayer[$key2] == $this->_player->primary_key && $value == '') {

						continue 2;

					}

					$arrPlayerWithKeys[$arrFieldsPlayer[$key2]] = $value;

				}

				$oPlayer = clone $this->_player;
				$oPlayer->init(false, $arrPlayerWithKeys);
				$arrPlayers[$oPlayer->getPlayerUid()] = $oPlayer;
			}

			$arrGame = explode(self::CONCAT_SEPARATOR, $game->gameInfos);
			$arrGameWithKeys = [];

			foreach ($arrGame as $key2 => $value) {

				if ($arrFieldsGame[$key2] == $this->_game->primary_key && $value == '') {

					continue 2;

				}

				$arrGameWithKeys[$arrFieldsGame[$key2]] = $value;

			}

			$oGame = clone $this->_game;
			$oGame->init(false, $arrGameWithKeys);
			$oGame
				->setArrPlayers($arrPlayers)
				->setArrHistories($arrHistories);

			$this->arrGames[$oGame->getGameUid()] = $oGame;

		}

	}

}