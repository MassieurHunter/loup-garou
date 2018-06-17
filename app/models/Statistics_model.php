<?php

/**
 * Class Statistics_model
 *
 * @property Player_model $_player
 * @property Game_model $_game
 * @property History_model $_history
 * @property Role_model $_role
 */
class Statistics_model extends MY_Model
{
	/**
	 * @var Game_model[]
	 */
	protected $arrGames = [];
	/**
	 * @var Player_model[]
	 */
	protected $arrPlayers = [];

	/**
	 * Log_model constructor.
	 * @param array $arrParams
	 */
	public function __construct(array $arrParams = []) {
		$this->db->query('SET SESSION group_concat_max_len = 10000000');
		parent::__construct($arrParams);
	}

	/**
	 * @return array
	 */
	public function getOverallRanking(): array {

		$arrGames = $this->getArrGames();
		$nbGames = count($arrGames);

		$ranking = [];
		$teams = [
			'loup',
			'tanneur',
			'villageois',
		];

		$sortWins = [];
		$sortRatio = [];

		foreach ($arrGames as $gameUid => $oGame) {

			$gameHistories = $oGame->getArrHistories();

			foreach ($oGame->getRealPlayers() as $playerUid => $oPlayer) {

				$oPlayerGameHistory = $gameHistories[$playerUid];

				if (!isset($ranking[$oPlayer->getPlayerUid()])) {
					$ranking[$oPlayer->getPlayerUid()] = [
						'player'                   => $oPlayer->getName(),
						'games_loup'               => 0,
						'games_tanneur'            => 0,
						'games_villageois'         => 0,
						'games_all'                => 0,
						'percent_games_loup'       => '-',
						'percent_games_tanneur'    => '-',
						'percent_games_villageois' => '-',
						'wins_loup'                => 0,
						'wins_tanneur'             => 0,
						'wins_villageois'          => 0,
						'wins_all'                 => 0,
//						'losses'                   => 0,
//						'losses_loup'              => 0,
//						'losses_tanneur'           => 0,
//						'losses_villageois'        => 0,
						'percent_win_loup'         => '-',
						'percent_win_tanneur'      => '-',
						'percent_win_villageois'   => '-',
						'percent_win_all'          => '-',

					];
				}

				$playerStats = &$ranking[$oPlayer->getPlayerUid()];
				$playerStats['games_all']++;

//				$playerRole = $oPlayer->getCurrentRoleModel($gameUid);
				$suffix = '_' . $oPlayerGameHistory->getTeam();

				$playerStats['games' . $suffix]++;

				if ($oPlayerGameHistory->isWinner()) {

					$playerStats['wins_all']++;
					$playerStats['wins' . $suffix]++;

				} else {

//					$playerStats['losses']++;
//					$playerStats['losses' . $suffix]++;

				}

				$percentWin = round($playerStats['wins_all'] / $playerStats['games_all'] * 100, 2);
				$playerStats['percent_win_all'] = $percentWin . '%';

				foreach ($teams as $team) {

					$playerStats['percent_games_' . $team] = round($playerStats['games_' . $team] / $playerStats['games_all'] * 100, 2) . '%';
					$playerStats['percent_win_' . $team] = $playerStats['games_' . $team] ? round($playerStats['wins_' . $team] / $playerStats['games_' . $team] * 100, 2) . '%' : '-';

				}

				$sortWins[$oPlayer->getPlayerUid()] = $playerStats['wins_all'];
				$sortRatio[$oPlayer->getPlayerUid()] = $percentWin;

			}

		}

		array_multisort($sortWins, SORT_DESC, $sortRatio, SORT_DESC, $ranking);

		return [
			'nbGames' => $nbGames,
			'stats'   => $ranking,
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

	/**
	 * @param int $playerUid
	 * @return array
	 */
	public function getPlayerStats(int $playerUid): array {

		$oPlayer = $this->getPlayer($playerUid);

		$playerStats = [
			'all'           => [
				'games'       => 0,
				'wins'        => 0,
				'percent_win' => '-',
			],
			'startingTeams' => [
				'games'                    => 0,
				'games_loup'               => 0,
				'games_tanneur'            => 0,
				'games_villageois'         => 0,
				'wins'                     => 0,
				'wins_loup'                => 0,
				'wins_tanneur'             => 0,
				'wins_villageois'          => 0,
				'percent_games_loup'       => '-',
				'percent_games_tanneur'    => '-',
				'percent_games_villageois' => '-',
				'percent_win'              => '-',
				'percent_win_loup'         => '-',
				'percent_win_tanneur'      => '-',
				'percent_win_villageois'   => '-',
			],
			'endingTeams'   => [
				'games'                    => 0,
				'games_loup'               => 0,
				'games_tanneur'            => 0,
				'games_villageois'         => 0,
				'wins'                     => 0,
				'wins_loup'                => 0,
				'wins_tanneur'             => 0,
				'wins_villageois'          => 0,
				'percent_games_loup'       => '-',
				'percent_games_tanneur'    => '-',
				'percent_games_villageois' => '-',
				'percent_win'              => '-',
				'percent_win_loup'         => '-',
				'percent_win_tanneur'      => '-',
				'percent_win_villageois'   => '-',
			],
			'startingRoles' => [],
			'endingRoles'   => [],
		];
		$playerHistory = [
			'all'           => [],
			'startingTeams' => [],
			'endingTeams'   => [],
			'startingRoles' => [],
			'endingRoles'   => [],
		];

		foreach ($oPlayer->getGames() as $gameUid => $game) {

			$gameHistory = $oPlayer->getGameHistory($gameUid);

			$startingRole = $oPlayer->getOriginalRole($gameUid);
			$endingRole = $oPlayer->getCurrentRole($gameUid);

			$playerStats = $this->buildPlayerStats($playerStats, $game, $startingRole, $endingRole, $gameHistory);
			$playerHistory = $this->buildPlayerHistory($playerHistory, $game, $startingRole, $endingRole, $gameHistory);

		}

		return [
			'history' => $playerHistory,
			'stats'   => $playerStats,
		];

	}

	/**
	 * @param int $playerUid
	 * @return Player_model
	 */
	public function getPlayer(int $playerUid): Player_model {

		$arrPlayers = $this->getArrPlayers();

		return isset($arrPlayers[$playerUid]) ? $arrPlayers[$playerUid] : $this->_player;

	}

	/**
	 * @return Player_model[]
	 */
	public function getArrPlayers(): array {

		if (empty($this->arrPlayers)) {

			$this->initPlayers();

		}

		return $this->arrPlayers;

	}

	public function initPlayers() {
		$this->load->model('game_model', '_game');
		$this->load->model('player_model', '_player');
		$this->load->model('roles/role_model', '_role');
		$this->load->model('history_model', '_history');

		$arrFieldsGame = $this->_game->getArrFields();
		$arrFieldsPlayer = $this->_player->getArrFields();
		$arrFieldsHistory = $this->_history->getArrFields();
		$arrFieldsRole = $this->_role->getArrFields();
		$arrFieldsRole[] = 'gameUid';

		$select = $this->_player->getConcat() . ' playerInfos';
		$select .= ', ' . $this->_game->getGroupConcat() . ' gamesInfos';
		$select .= ', ' . $this->_history->getGroupConcat() . ' historiesInfos';
		$select .= ', ' . $this->_role->getGroupConcat(
				null,
				[$this->_player->player_roles_table . '.gameUid'],
				$this->_player->player_roles_table . '.gameUid, ' . $this->_player->player_roles_table . '.order',
				false,
				true
			) . ' rolesInfos';

		/*
		 * query
		 */
		$arrPlayers = $this->db
			->select($select)
			->from($this->_player->table)
			->join($this->_history->table, $this->_player->primary_key)
			->join($this->_game->table, $this->_game->primary_key)
			->join($this->_player->player_roles_table, $this->_player->primary_key)
			->join($this->_role->table, $this->_role->primary_key)
			->group_by($this->_player->primary_key)
			->order_by($this->_player->primary_key)
			->get()
			->result();

		foreach ($arrPlayers as $player) {

			/*
			 * getting all the histories of the player
			 */
			$arrHistories = [];
			$arrFlatHistories = array_unique(explode(',', $player->historiesInfos)); // "inflate" the histories list

			/*
			 * looping on all "flat" histories
			 */
			foreach ($arrFlatHistories as $key => $flatHistory) {

				$arrHistory = explode(self::CONCAT_SEPARATOR, $flatHistory);// "inflate" the history infos
				$arrHistoryWithKeys = [];

				/*
				 * creating an associative array
				 * if primary_key is null we switch to the next
				 */
				foreach ($arrHistory as $key2 => $value) {

					if ($arrFieldsHistory[$key2] == $this->_history->primary_key && $value == '') {

						continue 2;

					}

					$arrHistoryWithKeys[$arrFieldsHistory[$key2]] = $value;

				}

				/*
				 * cloning and instantiating the History object
				 * putting it in an array
				 */
				$oHistory = clone $this->_history;
				$oHistory->init(false, $arrHistoryWithKeys);
				$arrHistories[$oHistory->getGameUid()] = $oHistory;
			}

			/*
			 * getting all the games of the player
			 */
			$arrGames = [];
			$arrFlatGames = array_unique(explode(',', $player->gamesInfos)); // "inflate" the games list

			/*
			 * looping on all "flat" games
			 */
			foreach ($arrFlatGames as $key => $flatGame) {


				$arrGame = explode(self::CONCAT_SEPARATOR, $flatGame);// "inflate" the game infos
				$arrGameWithKeys = [];

				/*
				 * creating an associative array
				 * if primary_key is null we switch to the next
				 */
				foreach ($arrGame as $key2 => $value) {

					if ($arrFieldsGame[$key2] == $this->_game->primary_key && $value == '') {

						continue 2;

					}

					$arrGameWithKeys[$arrFieldsGame[$key2]] = $value;

				}

				/*
				 * cloning and instantiating the Game object
				 * putting it in an array
				 */
				$oGame = clone $this->_game;
				$oGame->init(false, $arrGameWithKeys);
				$arrGames[$oGame->getGameUid()] = $oGame;
			}

			/*
			 * getting all the roles of the player
			 */
			$arrRoles = [];
			$arrFlatRoles = array_unique(explode(self::GROUP_CONCAT_SEPARATOR, $player->rolesInfos)); // "inflate" the roles list

			/*
			 * looping on all "flat" roles
			 */
			foreach ($arrFlatRoles as $key => $flatRole) {

				$arrRole = explode(self::CONCAT_SEPARATOR, $flatRole);// "inflate" the role infos
				$arrRoleWithKeys = [];

				/*
				 * creating an associative array
				 * if primary_key is null we switch to the next
				 */
				foreach ($arrRole as $key2 => $value) {

					if ($arrFieldsRole[$key2] == $this->_role->primary_key && $value == '') {

						continue 2;

					}

					$arrRoleWithKeys[$arrFieldsRole[$key2]] = $value;

				}

				/*
				 * cloning and instantiating the Role object
				 * putting it in an array
				 */
				$oRole = clone $this->_role;
				$oRole->init(false, $arrRoleWithKeys);
				$arrRoles[$arrRoleWithKeys['gameUid']][] = $oRole;
			}

			/*
			 * Getting the player
			 */
			$arrPlayer = explode(self::CONCAT_SEPARATOR, $player->playerInfos);
			$arrPlayerWithKeys = [];

			/*
			 * creating an associative array
             * if primary_key is null we switch to the next
             */
			foreach ($arrPlayer as $key2 => $value) {

				if ($arrFieldsPlayer[$key2] == $this->_player->primary_key && $value == '') {

					continue 2;

				}

				$arrPlayerWithKeys[$arrFieldsPlayer[$key2]] = $value;

			}

			/*
			 * cloning and instantiating the Player object
			 * settings his games and histories
			 * putting it in the class array
			 */
			$oPlayer = clone $this->_player;
			$oPlayer->init(false, $arrPlayerWithKeys);

			$oPlayer
				->setArrGames($arrGames)
				->setArrGamesHistory($arrHistories)
				->setArrRoles($arrRoles);

			$this->arrPlayers[$oPlayer->getPlayerUid()] = $oPlayer;

		}

	}

	/**
	 * @param array $playerStats
	 * @param Game_model $game
	 * @param Role_model $startingRole
	 * @param Role_model $endingRole
	 * @param History_model $gameHistory
	 * @return array
	 */
	protected function buildPlayerStats(array $playerStats, Game_model $game, Role_model $startingRole, Role_model $endingRole, History_model $gameHistory): array {

		$teams = [
			'loup',
			'tanneur',
			'villageois',
		];

		$startingRoleName = $startingRole->getName();
		$endingRoleName = $endingRole->getName();

		$startingTeam = $startingRole->getTeam();
		$endingTeam = $endingRole->getTeam();

		if (!isset($playerStats['startingRoles'][$startingRoleName])) {
			$playerStats['startingRoles'][$startingRoleName] = [
				'games'       => 0,
				'wins'        => 0,
				'percent_win' => 0,
			];
		}
		if (!isset($playerStats['endingRoles'][$endingRoleName])) {
			$playerStats['endingRoles'][$endingRoleName] = [
				'games'       => 0,
				'wins'        => 0,
				'percent_win' => 0,
			];
		}

		$playerStats['all']['games']++;

		$playerStats['startingRoles'][$startingRoleName]['games']++;
		$playerStats['endingRoles'][$endingRoleName]['games']++;
		$playerStats['startingTeams']['games']++;
		$playerStats['startingTeams']['games_' . $startingTeam]++;
		$playerStats['endingTeams']['games']++;
		$playerStats['endingTeams']['games_' . $endingTeam]++;

		if ($gameHistory->isWinner()) {

			$playerStats['all']['wins']++;
			$playerStats['startingRoles'][$startingRoleName]['wins']++;
			$playerStats['endingRoles'][$endingRoleName]['wins']++;
			$playerStats['startingTeams']['wins']++;
			$playerStats['startingTeams']['wins_' . $startingTeam]++;
			$playerStats['endingTeams']['wins']++;
			$playerStats['endingTeams']['wins_' . $endingTeam]++;

		}

		$playerStats['all']['percent_win'] = $playerStats['all']['games'] ? round($playerStats['all']['wins'] / $playerStats['all']['games'] * 100, 2) . '%' : '-';
		$playerStats['startingRoles'][$startingRoleName]['percent_win'] = $playerStats['startingRoles'][$startingRoleName]['games'] ? round($playerStats['startingRoles'][$startingRoleName]['wins'] / $playerStats['startingRoles'][$startingRoleName]['games'] * 100, 2) . '%' : '-';
		$playerStats['endingRoles'][$endingRoleName]['percent_win'] = $playerStats['endingRoles'][$endingRoleName]['games'] ? round($playerStats['endingRoles'][$endingRoleName]['wins'] / $playerStats['endingRoles'][$endingRoleName]['games'] * 100, 2) . '%' : '-';
		$playerStats['startingTeams']['percent_win'] = $playerStats['startingTeams']['games'] ? round($playerStats['startingTeams']['wins'] / $playerStats['startingTeams']['games'] * 100, 2) . '%' : '-';
		$playerStats['endingTeams']['percent_win'] = $playerStats['endingTeams']['games'] ? round($playerStats['endingTeams']['wins'] / $playerStats['endingTeams']['games'] * 100, 2) . '%' : '-';

		foreach ($teams as $team) {

			$playerStats['startingTeams']['percent_games_' . $team] = $playerStats['startingTeams']['games_' . $team] / $playerStats['startingTeams']['games'] * 100 . '%';
			$playerStats['startingTeams']['percent_win_' . $team] = $playerStats['startingTeams']['games_' . $team] ? round($playerStats['startingTeams']['wins_' . $team] / $playerStats['startingTeams']['games_' . $team] * 100, 2) . '%' : '-';
			$playerStats['endingTeams']['percent_games_' . $team] = $playerStats['endingTeams']['games_' . $team] / $playerStats['endingTeams']['games'] * 100 . '%';
			$playerStats['endingTeams']['percent_win_' . $team] = $playerStats['endingTeams']['games_' . $team] ? round($playerStats['endingTeams']['wins_' . $team] / $playerStats['endingTeams']['games_' . $team] * 100, 2) . '%' : '-';

		}

		return $playerStats;

	}

	/**
	 * @param array $playerHistory
	 * @param Game_model $game
	 * @param Role_model $startingRole
	 * @param Role_model $endingRole
	 * @param History_model $gameHistory
	 * @return array
	 */
	protected function buildPlayerHistory(array $playerHistory, Game_model $game, Role_model $startingRole, Role_model $endingRole, History_model $gameHistory): array {

		$startingRoleName = $startingRole->getName();
		$endingRoleName = $endingRole->getName();

		$startingTeam = $startingRole->getTeam();
		$endingTeam = $endingRole->getTeam();

		if (!isset($playerHistory['startingRoles'][$startingRoleName])) {
			$playerHistory['startingRoles'][$startingRoleName] = [];
		}
		if (!isset($playerHistory['endingRoles'][$endingRoleName])) {
			$playerHistory['endingRoles'][$endingRoleName] = [];
		}
		if (!isset($playerHistory['startingTeams'][$startingTeam])) {
			$playerHistory['startingTeams'][$startingTeam] = [];
		}
		if (!isset($playerHistory['endingTeams'][$endingTeam])) {
			$playerHistory['endingTeams'][$endingTeam] = [];
		}

		$playerHistory['all'][] = [
			'nbPlayers'    => $game->getNbPlayers(),
			'startingRole' => $startingRoleName,
			'endingRoles'  => $endingRoleName,
			'startingTeam' => $startingTeam,
			'endingTeam'   => $endingTeam,
			'players'      => $game->getRealPlayersWithBasicInfos(),
			'winner'       => $gameHistory->isWinner(),
		];
		$playerHistory['startingRoles'][$startingRoleName][] = [
			'nbPlayers' => $game->getNbPlayers(),
			'players'   => $game->getRealPlayersWithBasicInfos(),
			'winner'    => $gameHistory->isWinner(),
		];
		$playerHistory['endingRoles'][$endingRoleName][] = [
			'nbPlayers' => $game->getNbPlayers(),
			'players'   => $game->getRealPlayersWithBasicInfos(),
			'winner'    => $gameHistory->isWinner(),
		];
		$playerHistory['startingTeams'][$startingTeam][] = [
			'nbPlayers' => $game->getNbPlayers(),
			'players'   => $game->getRealPlayersWithBasicInfos(),
			'winner'    => $gameHistory->isWinner(),
		];
		$playerHistory['endingTeams'][$endingTeam][] = [
			'nbPlayers' => $game->getNbPlayers(),
			'players'   => $game->getRealPlayersWithBasicInfos(),
			'winner'    => $gameHistory->isWinner(),
		];

		return $playerHistory;

	}

	/**
	 * @param int $gameUid
	 * @return Game_model
	 */
	public function getGame(int $gameUid): Game_model {

		$arrGames = $this->getArrGames();

		return isset($arrGames[$gameUid]) ? $arrGames[$gameUid] : $this->_game;

	}

}