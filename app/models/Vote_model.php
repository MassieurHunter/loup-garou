<?php

class Vote_model extends MY_Model
{
	/**
	 * @var string
	 */
	public $table = 'votes';

	/**
	 * @var string
	 */
	public $primary_key = 'voteUid';

	/**
	 * @var int
	 */
	protected $voteUid;
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
	protected $targetUid;

	/**
	 * @param int $gameUid
	 * @param int $playerUid
	 * @return Vote_model
	 */
	public function initWithGameAndPlayer(int $gameUid, int $playerUid): Vote_model
	{

		$infos = $this->db
			->where('gameUid', $gameUid)
			->where('playerUid', $playerUid)
			->get($this->table)
			->row();
		
		$this->init(false, $infos);

		return $this;

	}

	/**
	 * @return int
	 */
	public function getVoteUid()
	{
		return (int) $this->voteUid;
	}

	/**
	 * @param int $voteUid
	 * @return Vote_model
	 */
	public function setVoteUid($voteUid)
	{
		$this->voteUid = $voteUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getGameUid()
	{
		return (int) $this->gameUid;
	}

	/**
	 * @param int $gameUid
	 * @return Vote_model
	 */
	public function setGameUid($gameUid)
	{
		$this->gameUid = $gameUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPlayerUid()
	{
		return (int) $this->playerUid;
	}

	/**
	 * @param int $playerUid
	 * @return Vote_model
	 */
	public function setPlayerUid($playerUid)
	{
		$this->playerUid = $playerUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTargetUid()
	{
		return (int) $this->targetUid;
	}

	/**
	 * @param int $targetUid
	 * @return Vote_model
	 */
	public function setTargetUid($targetUid)
	{
		$this->targetUid = $targetUid;
		return $this;
	}


}