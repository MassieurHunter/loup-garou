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
		parent::__construct($arrParams);
	}

	/**
	 * @return array
	 */
	public function getOverallRanking(): array {

		$arrGames = $this->getArrGames();
		$nbGames = count($arrGames);

		$ranking = [];

		foreach ($arrGames as $gameUid => $oGame) {

			$gameHistories = $oGame->getArrHistories();

			foreach ($oGame->getRealPlayers() as $playerUid => $oPlayer) {

				$oPlayerGameHistory = $gameHistories[$playerUid];

				if (!isset($ranking[$oPlayer->getPlayerUid()])) {
					$ranking[$oPlayer->getPlayerUid()] = [
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

				$playerStats = &$ranking[$oPlayer->getPlayerUid()];
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

		$playerStats = [];
		$allGames = [];
		$gamesWithStartingRoles = [];
		$gamesWithEndingRoles = [];

		foreach ($oPlayer->getGames() as $gameUid => $game) {

			$firstRole = $oPlayer->getOriginalRole($gameUid);
			$lastRole = $oPlayer->getCurrentRole($gameUid);

		}

	}

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
		$this->load->model('role_model', '_role');
		$this->load->model('history_model', '_history');

		$arrFieldsGame = $this->_game->getArrFields();
		$arrFieldsPlayer = $this->_player->getArrFields();
		$arrFieldsHistory = $this->_history->getArrFields();
		$arrFieldsRole = $this->_role->getArrFields();

		$select = $this->_player->getConcat() . ' playersInfos';
		$select .= ', ' . $this->_game->getGroupConcat() . ' gameInfos';
		$select .= ', ' . $this->_history->getGroupConcat() . ' historiesInfos';
		$select .= ', ' . $this->_role->getGroupConcat(
				null,
				$this->_player->player_roles_table . '.gameUid, ' . $this->_player->player_roles_table . '.order'
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
				$arrHistories[$oHistory->getPlayerUid()] = $oHistory;
			}


			/*
			 * getting all the games of the player
			 */
			$arrGames = [];
			$arrFlatGames = array_unique(explode(',', $player->gameInfos)); // "inflate" the games list

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
			$arrFlatRoles = array_unique(explode(',', $player->rolesInfos)); // "inflate" the roles list

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
				$oRole = clone $this->_game;
				$oRole->init(false, $arrRoleWithKeys);
				$arrRoles[$oRole->getGameUid()][] = $oRole;
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

	public function getGame(int $gameUid): Game_model {

		$arrGames = $this->getArrGames();

		return isset($arrGames[$gameUid]) ? $arrGames[$gameUid] : $this->_game;

	}

}