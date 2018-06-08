<?php

/**
 * Class Log_model
 */
class Log_model extends MY_Model
{
	/**
	 * @var string
	 */
	public $table = 'games_actions_logs';
	/**
	 * @var string
	 */
	public $primary_key = 'gameLogUid';
	/**
	 * @var int
	 */
	protected $gameLogUid;
	/**
	 * @var int
	 */
	protected $gameUid;
	/**
	 * @var int
	 */
	protected $playerUid;
	/**
	 * @var int
	 */
	protected $roleUid;
	/**
	 * @var string
	 */
	protected $action;
	/**
	 * @var int
	 */
	protected $target1;
	/**
	 * @var int
	 */
	protected $target2;
	/**
	 * @var int
	 */
	protected $target1Role;
	/**
	 * @var int
	 */
	protected $target2Role;
	/**
	 * @var string
	 */
	protected $date;

	/**
	 * Log_model constructor.
	 * @param array $arrParams
	 */
	public function __construct(array $arrParams = []) {
		parent::__construct($arrParams);
	}

	/**
	 * @return int
	 */
	public function getTarget1Role(): int {
		return $this->target1Role;
	}

	/**
	 * @param int $target1Role
	 * @return Log_model
	 */
	public function setTarget1Role(int $target1Role): Log_model {
		$this->target1Role = $target1Role;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTarget2Role(): int {
		return $this->target2Role;
	}

	/**
	 * @param int $target2Role
	 * @return Log_model
	 */
	public function setTarget2Role(int $target2Role): Log_model {
		$this->target2Role = $target2Role;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGameLogUid(): int {
		return $this->gameLogUid;
	}

	/**
	 * @param int $gameLogUid
	 * @return Log_model
	 */
	public function setGameLogUid(int $gameLogUid): Log_model {
		$this->gameLogUid = $gameLogUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGameUid(): int {
		return $this->gameUid;
	}

	/**
	 * @param int $gameUid
	 * @return Log_model
	 */
	public function setGameUid(int $gameUid): Log_model {
		$this->gameUid = $gameUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPlayerUid(): int {
		return $this->playerUid;
	}

	/**
	 * @param int $playerUid
	 * @return Log_model
	 */
	public function setPlayerUid(int $playerUid): Log_model {
		$this->playerUid = $playerUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRoleUid(): int {
		return $this->roleUid;
	}

	/**
	 * @param int $roleUid
	 * @return Log_model
	 */
	public function setRoleUid(int $roleUid): Log_model {
		$this->roleUid = $roleUid;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAction(): string {
		return $this->action;
	}

	/**
	 * @param string $action
	 * @return Log_model
	 */
	public function setAction(string $action): Log_model {
		$this->action = $action;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTarget1(): int {
		return $this->target1;
	}

	/**
	 * @param int $target1
	 * @return Log_model
	 */
	public function setTarget1(int $target1): Log_model {
		$this->target1 = $target1;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTarget2(): int {
		return $this->target2;
	}

	/**
	 * @param int $target2
	 * @return Log_model
	 */
	public function setTarget2(int $target2): Log_model {
		$this->target2 = $target2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDate(): string {
		return $this->date;
	}

	/**
	 * @param string $date
	 * @return Log_model
	 */
	public function setDate(string $date): Log_model {
		$this->date = $date;
		return $this;
	}


}