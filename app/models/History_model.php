<?php

class History_model extends MY_Model
{
	/**
	 * @var int
	 */
	protected $gameHistoryUid;
	/**
	 * @var int
	 */
	protected $gameUid;
	/**
	 * @var int
	 */
	protected $playerUid;
	/**
	 * @var boolean
	 */
	protected $winner;
	/**
	 * @var string
	 */
	protected $team;
	/**
	 * @var string
	 */
	protected $allies;

	/**
	 * @var string
	 */
	public $table = 'games_history';

	/**
	 * @var string
	 */
	public $primary_key = 'gameHistoryUid';

	/**
	 * @return int
	 */
	public function getGameHistoryUid(): int
	{
		return (int) $this->gameHistoryUid;
	}

	/**
	 * @param int $gameHistoryUid
	 * @return History_model
	 */
	public function setGameHistoryUid(int $gameHistoryUid): History_model
	{
		$this->gameHistoryUid = $gameHistoryUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGameUid(): int
	{
		return (int) $this->gameUid;
	}

	/**
	 * @param int $gameUid
	 * @return History_model
	 */
	public function setGameUid(int $gameUid): History_model
	{
		$this->gameUid = $gameUid;
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
	 * @return History_model
	 */
	public function setPlayerUid(int $playerUid): History_model
	{
		$this->playerUid = $playerUid;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isWinner(): bool
	{
		return $this->winner == true;
	}

	/**
	 * @param bool $winner
	 * @return History_model
	 */
	public function setWinner(bool $winner): History_model
	{
		$this->winner = $winner;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTeam(): string
	{
		return (string) $this->team;
	}

	/**
	 * @param string $team
	 * @return History_model
	 */
	public function setTeam(string $team): History_model
	{
		$this->team = $team;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAllies(): string
	{
		return (string) $this->allies;
	}

	/**
	 * @param string $allies
	 * @return History_model
	 */
	public function setAllies(string $allies): History_model
	{
		$this->allies = $allies;
		return $this;
	}
	

}