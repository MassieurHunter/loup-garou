<?php

/**
 * Class Game_model
 *
 * @property Player_model $_playerModel
 * @property Role_model $_roleModel
 * @property Log_model $_logModel
 * @property History_model $_history
 */
class Game_model extends MY_Model
{
	/**
	 * @var string
	 */
	public $table = 'games';

	/**
	 * @var string
	 */
	public $primary_key = 'gameUid';

	/**
	 * @var string
	 */
	public $player_games_table = 'games_players';
	/**
	 * @var array
	 */
	public $basics = [
		'code'       => 'getCode',
		'maxPlayers' => 'getMaxPlayers',
		'nbPlayers'  => 'getNbPlayers',
		'started'    => 'isStarted',
		'finished'   => 'isFinished',
		'players'    => 'getRealPlayersWithBasicInfos',
	];
	/**
	 * @var array
	 */
	public $advanced = [
		'code'            => 'getCode',
		'maxPlayers'      => 'getMaxPlayers',
		'nbPlayers'       => 'getNbPlayers',
		'started'         => 'isStarted',
		'finished'        => 'isFinished',
		'rolesForCasting' => 'getRolesForCastingWithBasicInfos',
		'rolesForRunning' => 'getRolesForRunningWithBasicInfos',
		'players'         => 'getRealPlayersWithBasicInfos',
	];
	/**
	 * @var int
	 */
	protected $gameUid;
	/**
	 * @var string
	 */
	protected $code;
	/**
	 * @var int
	 */
	protected $maxPlayers;
	/**
	 * @var int
	 */
	protected $nbPlayers;
	/**
	 * @var boolean
	 */
	protected $started;
	/**
	 * @var boolean
	 */
	protected $finished;
	/**
	 * @var Player_model[]
	 */
	protected $arrPlayers = [];
	/**
	 * @var Role_model[]
	 */
	protected $arrRoles = [];
	/**
	 * @var Log_model[]
	 */
	protected $arrLogs = [];
	/**
	 * @var array
	 */
	protected $arrVotes = [];

	/**
	 * @var History_model[]
	 */
	protected $arrHistories;

	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}


	/**
	 * @param string $code
	 * @return Game_model
	 */
	public function setCode(string $code): Game_model {
		$this->code = $code;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function generateCode(): Game_model {
		$newCode = random_string();
		$this->setCode($newCode);
		return $this;
	}

	/**
	 * @param $code
	 */
	public function initByCode(string $code) {
		$infos = $this->db
			->where('code', $code)
			->get($this->table)
			->row();

		$this->init(false, $infos);
	}

	/**
	 * @return bool
	 */
	public function isStarted(): bool {
		return $this->started;
	}

	/**
	 * @param bool $started
	 * @return Game_model
	 */
	public function setStarted(bool $started): Game_model {
		$this->started = $started;
		return $this;
	}

	public function start() {

		$this
			->setStarted(true)
			->saveModifications();

		$this
			->addMiddleCards()
			->giveRoleToPlayers();

	}

	/**
	 *
	 */
	public function giveRoleToPlayers() {
		$arrRoles = $this->getRolesForCasting();
		$arrPlayer = $this->getPlayers();

		shuffle($arrRoles);
		shuffle($arrPlayer);

		foreach ($arrPlayer as $key => $playerModel) {

			$roleModel = $arrRoles[$key];
			$playerModel->addNewRole($this->getGameUid(), $roleModel);

		}


	}

	/**
	 * @return Role_model[]
	 */
	public function getRolesForCasting(): array {
		$arrRoles = $this->getRoles();
		$nbPlayers = $this->getMaxPlayers() + 3;

		$arrSort = [];

		foreach ($arrRoles as $roleModel) {

			$arrSort[] = $roleModel->getCastingOrder();

		}

		array_multisort($arrSort, SORT_ASC, $arrRoles);


		if ($arrRoles[$nbPlayers - 1]->getModel() === 'francmac' && $arrRoles[$nbPlayers - 2]->getModel() !== 'francmac') {
			unset($arrRoles[$nbPlayers - 2]);
		}

		return array_splice($arrRoles, 0, $nbPlayers);

	}

	/**
	 * @return Role_model[]
	 */
	public function getRoles(): array {

		if (empty($this->arrRoles)) {
			$this->initRoles();
		}

		return $this->arrRoles;

	}

	/**
	 *
	 */
	public function initRoles() {
		$this->load->model('Roles/role_model', '_roleModel');

		$arrRoles = $this->db
			->get($this->_roleModel->table)
			->result();

		foreach ($arrRoles as $role) {
			$roleModel = clone $this->_roleModel;
			$roleModel->init(false, $role);

			for ($i = 0; $i < $roleModel->getNb(); $i++) {

				$this->arrRoles[] = $roleModel;

			}
		}

	}

	/**
	 * @return int
	 */
	public function getMaxPlayers(): int {
		return (int)$this->maxPlayers;
	}

	/**
	 * @param int $maxPlayers
	 * @return Game_model
	 */
	public function setMaxPlayers(int $maxPlayers): Game_model {
		$this->maxPlayers = $maxPlayers;
		return $this;
	}

	/**
	 * @return Player_model[]
	 */
	public function getPlayers(): array {

		if (empty($this->arrPlayers)) {
			$this->initPlayers();
		}

		return $this->arrPlayers;

	}

	/**
	 *
	 */
	public function initPlayers() {
		$this->load->model('player_model', '_playerModel');

		$arrPlayers = $this->db
			->select($this->_playerModel->table . '.*')
			->where($this->primary_key, $this->getGameUid())
			->join($this->player_games_table, $this->_playerModel->primary_key)
			->order_by('name')
			->get($this->_playerModel->table)
			->result();

		foreach ($arrPlayers as $player) {
			$playerModel = clone $this->_playerModel;
			$playerModel->init(false, $player);

			$this->arrPlayers[$playerModel->getPlayerUid()] = $playerModel;
		}

	}

	/**
	 * @param Player_model[] $arrPlayers
	 * @return Game_model
	 */
	public function setArrPlayers(array $arrPlayers): Game_model {
		$this->arrPlayers = $arrPlayers;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getGameUid(): int {
		return (int)$this->gameUid;
	}

	/**
	 * @param int $gameUid
	 * @return Game_model
	 */
	public function setGameUid(int $gameUid): Game_model {
		$this->gameUid = $gameUid;
		return $this;
	}

	/**
	 * @return Game_model
	 */
	public function addMiddleCards(): Game_model {
		$this->load->model('player_model', '_playerModel');
		for ($i = 1; $i < 4; $i++) {
			$card = clone $this->_playerModel;
			$card->init($i);
			$this->addPlayer($card);
		}

		return $this;
	}

	/**
	 * @param Player_model $oPlayer
	 */
	public function addPlayer(Player_model $oPlayer) {
		if (empty($this->arrPlayers)) {
			$this->initPlayers();
		}

		$insertQuery = $this->db
			->set('gameUid', $this->getGameUid())
			->set('playerUid', $oPlayer->getPlayerUid())
			->get_compiled_insert($this->player_games_table);

		$insertIgnoreQuery = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insertQuery);

		$this->db->query($insertIgnoreQuery);

		$this->arrPlayers[$oPlayer->getPlayerUid()] = $oPlayer;

		$nbRealPlayers = 0;

		foreach ($this->arrPlayers as $player) {

			if ($player->getPlayerUid() > 3) {

				$nbRealPlayers++;

			}

		}


		$this
			->setNbPlayers($nbRealPlayers)
			->saveModifications();
	}

	/**
	 * @return string
	 */
	public function getRolesNameForCasting(): string {

		$rolesName = [];

		foreach ($this->getRolesForCasting() as $role) {
			$rolesName[] = $role->getName();
		}

		return implode(', ', $rolesName);

	}

	/**
	 * @return array
	 */
	public function getRolesForCastingWithBasicInfos(): array {

		$arrRolesforCasting = [];

		foreach ($this->getRolesForCasting() as $role) {

			$arrRolesforCasting[] = $role->getBasicInfos();

		}

		return $arrRolesforCasting;
	}

	/**
	 * @return array
	 */
	public function getRealPlayersWithBasicInfos(): array {
		$arrPlayers = [];

		foreach ($this->getRealPlayers() as $playerUid => $player) {
			$arrPlayers[$playerUid] = $player->getBasicInfos();
		}

		return $arrPlayers;
	}

	/**
	 * @return Player_model[]
	 */
	public function getRealPlayers() {

		$arrPlayers = [];

		foreach ($this->getPlayers() as $playerUid => $player) {
			if ($playerUid > 3) {
				$arrPlayers[$playerUid] = $player;
			}
		}

		return $arrPlayers;

	}

	/**
	 * @return string
	 */
	public function getRealPlayersName(): string {
		$arrPlayersName = [];

		foreach ($this->getRealPlayers() as $playerUid => $player) {
			$arrPlayersName[] = $player->getName();
		}

		sort($arrPlayersName);

		return $this->lang->line('players_list') . implode(', ', $arrPlayersName);
	}

	/**
	 * @return array
	 */
	public function getFirstRole(): array {
		$arrRolesForRunning = $this->getRolesForRunning();
		return $arrRolesForRunning[0]->getBasicInfos();
	}

	/**
	 * @return Role_model[]
	 */
	public function getRolesForRunning(): array {
		$arrRoles = $this->getRolesForCasting();

		$arrSort = [];
		$previousRole = null;

		foreach ($arrRoles as $key => $roleModel) {

			if ($previousRole) {

				if ($previousRole->getModel() === $roleModel->getModel() || !$roleModel->hasFirstAction()) {

					unset($arrRoles[$key]);
					continue;

				}

			}

			$arrSort[] = $roleModel->getRunningOrder();

			$previousRole = clone $roleModel;
		}


		array_multisort($arrSort, SORT_ASC, $arrRoles);

		return $arrRoles;
	}

	/**
	 * @return array
	 */
	public function getRolesForRunningWithBasicInfos(): array {

		$arrRolesforRunning = [];

		foreach ($this->getRolesForRunning() as $role) {

			$arrRolesforRunning[] = $role->getBasicInfos();

		}

		return $arrRolesforRunning;
	}

	/**
	 * @param string $role
	 * @return array
	 */
	public function getNextRole(string $role): array {
		$arrRolesForRunning = $this->getRolesForRunning();
		$nextRole = [];

		foreach ($arrRolesForRunning as $key => $roleModel) {

			if ($roleModel->getModel() === $role && $key < ($this->getNbPlayers() - 1)) {

				$key2 = $key + 1;
				$nextRoleModel = $arrRolesForRunning[$key2];
				if ($nextRoleModel->getModel() !== $role) {
					$nextRole = $nextRoleModel->getBasicInfos();
					break;
				} elseif ($key2 < ($this->getNbPlayers() - 1)) {
					$key3 = $key2 + 1;
					$nextRoleModel = $arrRolesForRunning[$key3];
					$nextRole = $nextRoleModel->getBasicInfos();
					break;
				}

			}

		}

		return $nextRole;

	}

	/**
	 * @return int
	 */
	public function getNbPlayers(): int {
		return (int)$this->nbPlayers;
	}

	/**
	 * @param int $nbPlayers
	 * @return Game_model
	 */
	public function setNbPlayers(int $nbPlayers): Game_model {
		$this->nbPlayers = $nbPlayers;
		return $this;
	}

	/**
	 * @param int $playerUid
	 * @return array
	 */
	public function finish(int $playerUid): array {

		if (!$this->isFinished()) {

			$this
				->setFinished(true)
				->saveModifications();

		}

		$arrVotes = $this->getVotes();
		$arrPlayers = $this->getRealPlayers();

		$arrMessages = [
			'votes'         => '',
			'killed'        => [],
			'winnerTeam'    => [],
			'playerMessage' => '',
			'playerWon'     => '',
		];

		$maxNbVotes = 0;
		$votesList = '';
		$loupKilled = false;
		$tanneurKilled = false;

		foreach ($arrVotes as $key => $vote) {

			/** @var Player_model $player */
			$player = $vote['player'];
			$votesList .= '<li>' . $player->getName() . ': ' . $vote['nbVotes'] . '</li>';

			if ($key === 0) {

				$maxNbVotes = $vote['nbVotes'];

			}

			if ($vote['nbVotes'] === $maxNbVotes) {

				$arrMessages['killed'][] =
					str_replace('*player_name*', $player->getName(), $this->lang->line('player_killed'))
					. ', '
					. str_replace(['*player_name*', '*player_role*'], [$player->getName(), $player->getCurrentRole($this->getGameUid())->getName()], $this->lang->line('player_was_role'));

				$loupKilled = $loupKilled || $player->getCurrentRole($this->getGameUid())->isLoup();
				$tanneurKilled = $tanneurKilled || $player->getCurrentRole($this->getGameUid())->isTanneur();

			}


		}

		$arrMessages['votes'] = str_replace('*votes_list*', $votesList, $this->lang->line('vote_results'));

		if ($loupKilled) {

			$arrMessages['winnerTeam'][] = $this->lang->line('villageois_won');

		} else if (!$loupKilled && !$tanneurKilled) {

			$arrMessages['winnerTeam'][] = $this->lang->line('loups_won');

		}

		if ($tanneurKilled) {

			$arrMessages['winnerTeam'][] = $this->lang->line('tanneur_won');

		}

		$playerTeam = [];

		foreach ($arrPlayers as $player) {

			if ($playerUid === $player->getPlayerUid()) {

				$playerTeam[$player->getPlayerUid()] = $player->getCurrentRole($this->getGameUid())->getTeam();
					
				if ($player->getCurrentRole($this->getGameUid())->isLoup()) {

					$arrMessages['playerMessage'] = $loupKilled ? $this->lang->line('you_lost') : $this->lang->line('you_won');
					$arrMessages['playerWon'] = !$loupKilled;

				} else if ($player->getCurrentRole($this->getGameUid())->isTanneur()) {

					$arrMessages['playerMessage'] = $tanneurKilled ? $this->lang->line('you_won') : $this->lang->line('you_lost');
					$arrMessages['playerWon'] = $tanneurKilled;


				} else {

					$arrMessages['playerMessage'] = $loupKilled ? $this->lang->line('you_won') : $this->lang->line('you_lost');
					$arrMessages['playerWon'] = $loupKilled;

				}

			} else {

				if ($player->getCurrentRole($this->getGameUid())->isLoup()) {

					$playerTeam[$player->getPlayerUid()] = 'loup';

				} else if ($player->getCurrentRole($this->getGameUid())->isTanneur()) {

					$playerTeam[$player->getPlayerUid()] = 'tanneur';


				} else {

					$playerTeam[$player->getPlayerUid()] = 'villageois';

				}

			}

		}

		$currentPlayerTeam = $playerTeam[$playerUid];
		$playerAllies = [];

		foreach ($playerTeam as $loopPlayerUid => $team) {

			if ($currentPlayerTeam === $team && $loopPlayerUid !== $playerUid) {

				$playerAllies[] = $loopPlayerUid;

			}

		}

		$this->load->model('history_model', '_history');
		$this->_history
			->setPlayerUid($playerUid)
			->setGameUid($this->getGameUid())
			->setWinner($arrMessages['playerWon'])
			->setTeam($currentPlayerTeam)
			->setAllies(implode(',', $playerAllies))
			->create();


		return $arrMessages;

	}

	/**
	 * @return bool
	 */
	public function isFinished(): bool {
		return $this->finished;
	}

	/**
	 * @param bool $finished
	 * @return Game_model
	 */
	public function setFinished(bool $finished): Game_model {
		$this->finished = $finished;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getVotes(): array {
		if (empty($this->arrVotes)) {
			$this->initVotes();
		}

		return $this->arrVotes;
	}

	/**
	 *
	 */
	public function initVotes() {
		$this->arrVotes = [];

		$arrPlayer = $this->getPlayers();

		$votes = $this->db
			->select('targetUid, count(voteUid) as nbVotes')
			->where($this->primary_key, $this->getGameUid())
			->group_by('targetUid')
			->order_by('nbVotes', 'DESC')
			->get('votes')
			->result();

		foreach ($votes as $vote) {

			$player = $arrPlayer[$vote->targetUid];
			$this->arrVotes[] = [
				'player'  => $player,
				'nbVotes' => $vote->nbVotes,
			];

		}

	}

	/**
	 * @return array
	 */
	public function getSummary(): array {
		$arrLogs = $this->getLogs();
		$arrPlayers = $this->getPlayers();
		$arrRolesForCasting = $this->getRolesForCasting();

		/*
		 * starting roles
		 */
		$startingRoles = '';
		foreach ($arrPlayers as $player) {
			if ($player->getPlayerUid() > 3) {
				$startingRoles .= '<li>' . str_replace(['*player_name*', '*player_role*'], [$player->getName(), $player->getOriginalRoleName($this->getGameUid())], $this->lang->line('player_is_role')) . '</li>';
			}
		}

		/*
		 * blank line
		 */
		$startingRoles .= '<li></li>';

		/*
		 * middle cards
		 */
		foreach ($arrPlayers as $player) {
			if ($player->getPlayerUid() <= 3) {
				$startingRoles .= '<li>' . str_replace(['*player_name*', '*player_role*'], [$player->getName(), $player->getOriginalRoleName($this->getGameUid())], $this->lang->line('player_is_role')) . '</li>';
			}
		}


		/*
		 * actions
		 */
		$actions = [];
		$loupPassed = false;
		$francMacPassed = false;
		foreach ($arrLogs as $log) {

			$player = $arrPlayers[$log->getPlayerUid()];
			$playerRole = clone $this->_roleModel;

			foreach ($arrRolesForCasting as $role) {

				if ($role->getRoleUid() === $log->getRoleUid()) {

					$playerRole = $role;
					break;

				}

			}

			if ($playerRole->getModel() !== 'loup' || ($playerRole->getModel() === 'loup' && !$loupPassed)) {

				$loupPassed = true;

			} else {

				continue;

			}

			if ($playerRole->getModel() !== 'francmac' || ($playerRole->getModel() === 'francmac' && !$francMacPassed)) {

				$francMacPassed = true;

			} else {

				continue;

			}


			$target1 = isset($arrPlayers[$log->getTarget1()]) ? $arrPlayers[$log->getTarget1()] : $this->_playerModel;
			$target2 = isset($arrPlayers[$log->getTarget2()]) ? $arrPlayers[$log->getTarget2()] : $this->_playerModel;
			$target3 = isset($arrPlayers[$log->getTarget3()]) ? $arrPlayers[$log->getTarget3()] : $this->_playerModel;
			$target1Role = clone $this->_roleModel;
			$target2Role = clone $this->_roleModel;
			$target3Role = clone $this->_roleModel;

			foreach ($arrRolesForCasting as $role) {

				if ($role->getRoleUid() === $log->getTarget1Role()) {

					$target1Role = $role;

				}

				if ($role->getRoleUid() === $log->getTarget2Role()) {

					$target2Role = $role;

				}

				if ($role->getRoleUid() === $log->getTarget3Role()) {

					$target3Role = $role;

				}

			}

			$actions[] = $playerRole->buildActionSummary($log->getAction(), $player, $playerRole, $target1, $target2, $target3, $target1Role, $target2Role, $target3Role);

		}


		/*
		 * ending roles
		 */
		$endingRoles = '';
		foreach ($arrPlayers as $player) {
			if ($player->getPlayerUid() > 3) {
				$endingRoles .= '<li>' . str_replace(['*player_name*', '*player_role*'], [$player->getName(), $player->getCurrentRoleName($this->getGameUid())], $this->lang->line('player_is_role')) . '</li>';
			}
		}

		/*
		 * blank line
		 */
		$endingRoles .= '<li></li>';

		/*
		 * middle cards
		 */
		foreach ($arrPlayers as $player) {
			if ($player->getPlayerUid() <= 3) {
				$endingRoles .= '<li>' . str_replace(['*player_name*', '*player_role*'], [$player->getName(), $player->getCurrentRoleName($this->getGameUid())], $this->lang->line('player_is_role')) . '</li>';
			}
		}

		return [
			'startingRoles' => str_replace('*roles_list*', $startingRoles, $this->lang->line('starting_roles')),
			'actions'       => $actions,
			'endingRoles'   => str_replace('*roles_list*', $endingRoles, $this->lang->line('ending_roles')),
		];

	}

	/**
	 * @return Log_model[]
	 */
	public function getLogs(): array {

		if (empty($this->arrLogs)) {
			$this->initLogs();
		}

		return $this->arrLogs;

	}

	/**
	 *
	 */
	public function initLogs() {
		$this->load->model('log_model', '_logModel');

		$arrLogs = $this->db
			->select($this->_logModel->table . '.*')
			->where($this->primary_key, $this->getGameUid())
			->order_by('date')
			->get($this->_logModel->table)
			->result();

		foreach ($arrLogs as $log) {
			$logModel = clone $this->_logModel;
			$logModel->init(false, $log);

			$this->arrLogs[$logModel->getGameLogUid()] = $logModel;
		}

	}

	/**
	 * @param Player_model $oPlayer
	 * @return array
	 */
	public function rebuildActions(Player_model $oPlayer): array {

		$actions = [];
		$arrLogs = $this->getLogs();
		$arrPlayers = $this->getPlayers();
		$arrRolesForCasting = $this->getRolesForCasting();

		if (!isset($this->_roleModel)) {
			$this->load->model('roles/role_model', '_roleModel');
		}

		$playerRole = clone $this->_roleModel;
		$doppel = false;

		foreach ($arrLogs as $log) {

			if ($log->getPlayerUid() === $oPlayer->getPlayerUid()) {

				$target1 = isset($arrPlayers[$log->getTarget1()]) ? $arrPlayers[$log->getTarget1()] : $this->_playerModel;
				$target2 = isset($arrPlayers[$log->getTarget2()]) ? $arrPlayers[$log->getTarget2()] : $this->_playerModel;
				$target3 = isset($arrPlayers[$log->getTarget3()]) ? $arrPlayers[$log->getTarget3()] : $this->_playerModel;
				$target1Role = clone $this->_roleModel;
				$target2Role = clone $this->_roleModel;
				$target3Role = clone $this->_roleModel;

				foreach ($arrRolesForCasting as $role) {

					if ($role->getRoleUid() === $log->getRoleUid()) {

						$playerRole = $role;

					}

					if ($role->getRoleUid() === $log->getTarget1Role()) {

						$target1Role = $role;

					}

					if ($role->getRoleUid() === $log->getTarget2Role()) {

						$target2Role = $role;

					}

					if ($role->getRoleUid() === $log->getTarget3Role()) {

						$target3Role = $role;

					}

				}


				$doppel = $doppel || $playerRole->getModel() === 'doppelganger';
				$actions[] = $playerRole->rebuildActionMessage($log->getAction(), $target1, $target2, $target3, $target1Role, $target2Role, $target3Role);


			}

		}

		$playerFinishedFirstAction = false;
		$playerFinishedTurn = false;
		$playerNbActions = 0;

		if ($playerRole->getRoleUid()) {

			$nbActions = [];

			foreach ($arrLogs as $log) {

				$loopRole = clone $this->_roleModel;
				$loopRole->init($log->getRoleUid());

				if (!isset($nbActions[$log->getRoleUid()])) {

					$nbActions[$log->getRoleUid()] = 0;

				}

				$nbActions[$log->getRoleUid()]++;


				if ($playerRole->getRunningOrder() < $loopRole->getRunningOrder()) {

					$playerFinishedTurn = true;

				} elseif ($playerRole->getRunningOrder() === $loopRole->getRunningOrder()) {

					$loopNbActions = $nbActions[$playerRole->getRoleUid()];
					$playerNbActions = $playerNbActions ? $playerNbActions + 1 : 1;

					if (!$playerNbActions) {

						if ($playerRole->getFirstActionTargetType() !== 'ajax') {

							$playerNbActions++;

						}

						if ($playerRole->isSecondActionNeedFailedFirst() && $log->getTarget1()) {

							$playerNbActions++;

						}

					}

					if ($loopNbActions > 0) {

						$playerFinishedFirstAction = true;

					}

					if ($loopNbActions == $playerNbActions) {

						$playerFinishedTurn = true;

					}

				}

			}

		}

		return [
			'actions'                   => $actions,
			'playerFinishedFirstAction' => $playerFinishedFirstAction && !$playerFinishedTurn,
			'finishedTurn'              => $playerFinishedTurn,
			'doppel'                    => $doppel,
		];

	}

	/**
	 * @return History_model[]
	 */
	public function getArrHistories(): array {

		if (empty($this->arrHistories)) {

			$this->initHistories();

		}

		return $this->arrHistories;
	}

	/**
	 *
	 */
	public function initHistories() {

		$this->arrHistories = [];
		$this->load->model('history_model', '_history');

		$arrHistories = $this->db
			->select('*')
			->where('gameUid', $this->getGameUid())
			->get($this->_history->table)
			->result();

		foreach ($arrHistories as $history) {
			$oHistory = clone $this->_history;
			$this->arrHistories[$oHistory->getPlayerUid()] = $oHistory->init(false, $history);
		}

	}


	/**
	 * @param array $arrHistories
	 * @return Game_model
	 */
	public function setArrHistories(array $arrHistories): Game_model {
		$this->arrHistories = $arrHistories;
		return $this;
	}


}