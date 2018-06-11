<?php


/**
 *
 * @property \Player_model $_oTestPlayer
 * @property \Role_model $_roleModel
 * @property \Vote_model $newVote
 * @property \Vote_model $_vote
 *
 */
class Player_model extends MY_Model
{
	/**
	 * @var string
	 */
	public $table = 'players';

	/**
	 * @var string
	 */
	public $primary_key = 'playerUid';

	/**
	 * @var string
	 */
	public $player_games_table = 'games_players';

	/**
	 * @var string
	 */
	public $player_roles_table = 'players_game_roles';
	/**
	 * @var array
	 */
	public $basics = [
		'playerUid' => 'getPlayerUid',
		'name' => 'getName',
	];
	/**
	 * @var int
	 */
	protected $playerUid;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $password;
	/**
	 * @var string
	 */
	protected $theme;
	/**
	 * @var array
	 */
	protected $arrRoleModel = [];

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Player_model
	 */
	public function setName(string $name): Player_model
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Init the user with email and test the password
	 *
	 * @param string $name
	 * @param string $password
	 *
	 *
	 * array['result']  boolean login successfull
	 * array['message'] string translation key
	 * @return array
	 */
	public function login(string $name, string $password): array
	{
		$success = false;

		/*
         * check if the inputs aren't empty
         */
		if ($name && $password) {

			$this->initFromName($name);
			/*
             * Check if the player exists
             */
			if ($this->getPlayerUid()) {
				/*
                 * Check if the password is correct
                 */
				if ($this->verifyPassword($password)) {

					$success = true;
					$message = 'login_success';
					$this->createCookieAndSession();

				} else {

					$message = 'error_wrong_name_password';

				}// end password
			} else {

				$message = 'error_wrong_name_password';

			}// end player exists

		} else {

			$message = 'error_no_data';

		}// end inputs

		return [
			'success' => $success,
			'message' => $message,
		];
	}

	/**
	 * @param $name
	 * @return Player_model
	 */
	public function initFromName(string $name): Player_model
	{
		$player = $this->db
			->select('*')
			->from($this->table)
			->where('name', $name)
			->get()
			->row();
		if (!empty($player)) {
			$this->init(false, $player);
		}
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPlayerUid(): int
	{
		return (int) $this->playerUid;
	}

	/**
	 * @param int $playerUid
	 * @return Player_model
	 */
	public function setPlayerUid(int $playerUid): Player_model
	{
		$this->playerUid = $playerUid;
		return $this;
	}

	/**
	 * Test the password for the user
	 *
	 * @param string $password
	 * @param boolean $hashed
	 * @return boolean
	 */
	public function verifyPassword(string $password, bool $hashed = false): bool
	{
		return $hashed
			? $password == $this->getPassword()
			: password_verify($password, $this->getPassword());
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return (string) $this->password;
	}

	/**
	 * @param string $password
	 * @return Player_model
	 */
	public function setPassword(string $password): Player_model
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTheme(): string
	{
		return (string) $this->theme;
	}

	/**
	 * @param string $theme
	 * @return Player_model
	 */
	public function setTheme(string $theme): Player_model
	{
		$this->theme = $theme;
		return $this;
	}

	

	/**
	 *
	 */
	public function createCookieAndSession()
	{

		$autoLogCookie = [
			'name' => 'autoLog',
			'value' => $this->getPlayerUid() . ':' . $this->getPassword(),
			'expire' => strtotime('+1 year'),
			'path' => '/',
		];

		$this->session->set_userdata('autoLog', $this->getPlayerUid() . ':' . $this->getPassword());
		$this->input->set_cookie($autoLogCookie);

	}

	/**
	 * @param string $password
	 * @return Player_model
	 */
	public function hashAndSetPassword(string $password): Player_model
	{
		$this->setPassword($this->hashPassword($password));
		return $this;
	}

	/**
	 * Return the hashed version of the inputed password
	 *
	 * @param string $password
	 * @return string
	 */
	public function hashPassword(string $password): string
	{

		$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);

		return $hashedPassword;
	}

	/**
	 * Login with ws_auth cookie or session
	 *
	 * @return boolean
	 */
	public function autoLogin(): bool
	{
		$this->load->model('player/player_model', '_oTestPlayer');
		$autoLogString = $this->session->userdata('autoLog');
		$splitedAutoLog = explode(':', $autoLogString);
		$ok = false;

		/*
         * We test if the auto-login string is valid
         */
		if (count($splitedAutoLog) == 2) {

			$playerUid = $splitedAutoLog[0];
			$hashedPassword = $splitedAutoLog[1];
			/*
             * we test if the two inputs aren't empty
             */
			if ($playerUid && $hashedPassword) {
				/*
                 * We init the player's infos from his playerUid
                 */
				$this->_oTestPlayer->init($playerUid);

				if ($this->_oTestPlayer->getPlayerUid()) {
					/*
                     * We test the hashed password
                     */
					if ($this->_oTestPlayer->verifyPassword($hashedPassword, true)) {
						$ok = true;
						$this->init($playerUid);
						$this->createCookieAndSession();
					}
				}
			}
		}

		return $ok;
	}

	/**
	 * @param Game_model $game
	 * @return array
	 */
	public function joinGame(Game_model $game): array
	{

		$success = false;

		if ($this->getPlayerUid()) {

			if ($game->getGameUid()) {

				if (!$game->isFinished()) {

					if ($game->getNbPlayers() < $game->getMaxPlayers()) {

						$game->addPlayer($this);
						$success = true;
						$message = 'joining_game';

					} else {

						$message = 'error_game_full';
					}

				} else {

					$message = 'error_game_finished';

				}

			} else {
				$message = 'error_game_not_exists';
			}

		} else {
			$message = 'error_not_logged_in';
		}

		return [
			'success' => $success,
			'message' => $message,
		];

	}

	/**
	 * @param int $gameUid
	 * @param Role_model $newRole
	 */
	public function addNewRole(int $gameUid, Role_model $newRole)
	{

		$arrRoleModels = $this->getArrRoleModel($gameUid);

		$newRoleOrder = $this->getCurrentRoleModel($gameUid)->getRoleUid() ? count($arrRoleModels) : 0;

		$this->load->model('Roles/role_model', '_roleModel');

		$this->db
			->set('playerUid', $this->getPlayerUid())
			->set('gameUid', $gameUid)
			->set('roleUid', $newRole->getRoleUid())
			->set('order', $newRoleOrder)
			->insert($this->player_roles_table);

		$this->arrRoleModel[$gameUid][] = $newRole;

	}

	/**
	 * @param int $gameUid
	 * @return Role_model[]
	 */
	public function getArrRoleModel(int $gameUid): array
	{
		if (!isset($this->arrRoleModel[$gameUid]) || empty($this->arrRoleModel[$gameUid])) {
			$this->initRoles($gameUid);
		}

		return $this->arrRoleModel[$gameUid];
	}

	/**
	 * @param int $gameUid
	 */
	public function initRoles(int $gameUid)
	{

		$this->arrRoleModel[$gameUid] = [];
		$this->load->model('Roles/role_model', '_roleModel');

		$arrRoles = $this->db
			->select($this->_roleModel->table . '.*')
			->where('playerUid', $this->getPlayerUid())
			->where('gameUid', $gameUid)
			->join($this->player_roles_table, $this->_roleModel->primary_key)
			->order_by('order')
			->get($this->_roleModel->table)
			->result();

		foreach ($arrRoles as $role) {
			$roleModel = clone $this->_roleModel;
			$this->arrRoleModel[$gameUid][] = $roleModel->init(false, $role);
		}

		if (empty($this->arrRoleModel[$gameUid])) {
			$this->arrRoleModel[$gameUid][] = $this->_roleModel;
		}

	}

	/**
	 * @param int $gameUid
	 * @return Role_model
	 */
	public function getCurrentRoleModel(int $gameUid): Role_model
	{
		$arrRoleModel = $this->getArrRoleModel($gameUid);

		return end($arrRoleModel);
	}

	/**
	 * @param int $gameUid
	 * @return array
	 */
	public function getCurrentRoleWithBasicInfos(int $gameUid): array
	{
		return $this->getCurrentRoleModel($gameUid)->getBasicInfos();
	}

	/**
	 * @param int $gameUid
	 * @return string
	 */
	public function getOriginalRoleName(int $gameUid): string
	{

		return $this->getOriginalRoleModel($gameUid)->getName();

	}

	/**
	 * @param int $gameUid
	 * @return Role_model
	 */
	public function getOriginalRoleModel(int $gameUid): Role_model
	{
		$arrRoleModel = $this->getArrRoleModel($gameUid);

		return $arrRoleModel[0];
	}

	/**
	 * @param int $gameUid
	 * @param int $order
	 * @return Role_model
	 */
	public function getSpecificRoleModel(int $gameUid, $order): Role_model
	{
		$arrRoleModel = $this->getArrRoleModel($gameUid);

		return isset($arrRoleModel[$order]) ? $arrRoleModel[$order] : $this->_roleModel;
	}

	/**
	 * @param int $gameUid
	 * @return array
	 */
	public function getOriginalRoleWithBasicInfos(int $gameUid): array
	{
		return $this->getOriginalRoleModel($gameUid)->getBasicInfos();
	}

	/**
	 * @param int $gameUid
	 * @return string
	 */
	public function getCurrentRoleName(int $gameUid): string
	{

		return $this->getCurrentRoleModel($gameUid)->getName();

	}


	/**
	 * @param int $gameUid
	 * @param int $targetUid
	 */
	public function vote(int $gameUid, int $targetUid)
	{

		$this->load->model('vote_model', 'newVote');
		$this->newVote
			->setGameUid($gameUid)
			->setPlayerUid($this->getPlayerUid())
			->setTargetUid($targetUid)
			->create();
	}

	/**
	 * @param int $gameUid
	 * @return bool
	 */
	public function hasVoted(int $gameUid) : bool {

		$this->load->model('vote_model', '_vote');
		$this->_vote->initWithGameAndPlayer($gameUid, $this->getPlayerUid());
		
		return $this->_vote->getVoteUid() > 0;
		
	}
	
	
	/**
	 * @param int $gameUid
	 */
	public function cancelVote(int $gameUid)
	{

		$this->load->model('vote_model', '_vote');
		$this->_vote
			->initWithGameAndPlayer($gameUid, $this->getPlayerUid())
			->delete();
		
	}
	
	

	/**
	 * @param int $gameUid
	 * @return bool
	 */
	public function hasPlayed(int $gameUid): bool
	{
		$queryResult = $this->db
			->select('played')
			->where($this->primary_key, $this->getPlayerUid())
			->where('gameUid', $gameUid)
			->get($this->player_games_table)
			->row();

		return $queryResult->played === '1';
	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function originalRoleFirstAction($arguments): array
	{
		return $this->getOriginalRoleModel($arguments['gameUid'])->firstAction($arguments);
	}

	/**
	 *
	 * @param array $arguments
	 * @return array
	 */
	public function originalRoleSecondAction($arguments): array
	{
		return $this->getOriginalRoleModel($arguments['gameUid'])->secondAction($arguments);
	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function currentRoleFirstAction($arguments): array
	{
		return $this->getCurrentRoleModel($arguments['gameUid'])->firstAction($arguments);
	}

	/**
	 *
	 * @param array $arguments
	 * @return array
	 */
	public function currentRoleSecondAction($arguments): array
	{
		return $this->getCurrentRoleModel($arguments['gameUid'])->secondAction($arguments);
	}


}