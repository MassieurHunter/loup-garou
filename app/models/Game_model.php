<?php

/**
 * Class Game_model
 *
 * @property Player_model $_playerModel
 * @property Role_model $_roleModel
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
	 * @var array
	 */
	protected $arrVotes = [];

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
	 * @return int
	 */
	public function getMaxPlayers(): int {
		return $this->maxPlayers;
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
		$arrPlayers = $this->getPlayers();
		$nbPlayers = count($arrPlayers);

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
			->get($this->_playerModel->table)
			->result();

		foreach ($arrPlayers as $player) {
			$playerModel = clone $this->_playerModel;
			$playerModel->init(false, $player);

			$this->arrPlayers[$playerModel->getPlayerUid()] = $playerModel;
		}

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
			$arrPlayers[] = $player->getBasicInfos();
		}

		return $arrPlayers;
	}

	public function getRealPlayers() {

		$arrPlayers = [];

		foreach ($this->getPlayers() as $playerUid => $player) {
			if ($playerUid > 3) {
				$arrPlayers[] = $player;
			}
		}

		return $arrPlayers;

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
		return $this->nbPlayers;
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
	 * @return array
	 */
	public function getResults(): array {

		$arrVotes = $this->getVotes();
		$arrPlayers = $this->getRealPlayers();

		$arrVoteResults = [
			'votes'      => $arrVotes,
			'killed'     => [],
			'winnerTeam' => [],
			'winners'    => [],
			'loosers'    => [],
		];

		$maxNbVotes = 0;

		foreach ($arrVotes as $key => $vote) {

			/** @var Player_model $player */
			$player = $vote['player'];

			if ($key === 0) {

				$maxNbVotes = $vote['nbVotes'];
				$arrVoteResults['killed'][$key] = $player->getBasicInfos();
				$arrVoteResults['killed'][$key]['role'] = $player->getcurrentroleWithBasicInfos($this->getGameUid());

			} else {

				if ($vote['nbVotes'] === $maxNbVotes) {

					$arrVoteResults['killed'][$key] = $player->getBasicInfos();
					$arrVoteResults['killed'][$key]['role'] = $player->getcurrentroleWithBasicInfos($this->getGameUid());

				} else {

					break;

				}

			}


		}


		$loupKilled = false;
		$tanneurKilled = false;


		foreach ($arrVoteResults['killed'] as $killed) {
			/** @var $killed Player_model */

			if ($killed['role']['loup']) {

				$loupKilled = true;

			}

			if ($killed['role']['tanneur']) {

				$tanneurKilled = true;

			}

		}

		if ($loupKilled) {

			$arrVoteResults['winnerTeam'][] = 'villageois';

		} else {

			$arrVoteResults['winnerTeam'][] = ['loups'];

		}

		if ($tanneurKilled) {

			$arrVoteResults['winnerTeam'][] = 'tanneur';

		}

		foreach ($arrPlayers as $player) {

			if ($player->getCurrentRoleModel($this->getGameUid())->isLoup()) {

				if ($loupKilled) {

					$arrVoteResults['loosers'][] = $player->getBasicInfos();

				} else {

					$arrVoteResults['winners'][] = $player->getBasicInfos();

				}

			} else if ($player->getCurrentRoleModel($this->getGameUid())->isTanneur()) {

				if ($tanneurKilled) {

					$arrVoteResults['winners'][] = $player->getBasicInfos();

				} else {

					$arrVoteResults['loosers'][] = $player->getBasicInfos();

				}

			} else {

				if ($loupKilled) {

					$arrVoteResults['winners'][] = $player->getBasicInfos();

				} else {

					$arrVoteResults['loosers'][] = $player->getBasicInfos();

				}

			}

		}

		return $arrVoteResults;

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

	public function initVotes() {
		$this->arrVotes = [];

		$arrPlayer = $this->getPlayers();

		$votes = $this->db
			->select('targetUid, count(voteUid) as nbVotes')
			->where($this->primary_key, $this->getGameUid())
			->group_by('targetUid')
			->order_by('nbVotes', 'DESC')
			->get('votes');

		foreach ($votes as $vote) {

			$player = $arrPlayer[$vote->targetUid];
			$this->arrVotes[] = [
				'player'  => $player,
				'nbVotes' => $vote->nbVotes,
			];

		}


	}


}