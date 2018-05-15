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
     * @return int
     */
    public function getVoteUid() {
        return $this->voteUid;
    }

    /**
     * @param int $voteUid
     * @return Vote_model
     */
    public function setVoteUid($voteUid) {
        $this->voteUid = $voteUid;
        return $this;
    }

    /**
     * @return int
     */
    public function getGameUid() {
        return $this->gameUid;
    }

    /**
     * @param int $gameUid
     * @return Vote_model
     */
    public function setGameUid($gameUid) {
        $this->gameUid = $gameUid;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerUid() {
        return $this->playerUid;
    }

    /**
     * @param int $playerUid
     * @return Vote_model
     */
    public function setPlayerUid($playerUid) {
        $this->playerUid = $playerUid;
        return $this;
    }

    /**
     * @return int
     */
    public function getTargetUid() {
        return $this->targetUid;
    }

    /**
     * @param int $targetUid
     * @return Vote_model
     */
    public function setTargetUid($targetUid) {
        $this->targetUid = $targetUid;
        return $this;
    }


}