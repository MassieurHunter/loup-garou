<?php

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
     * @var int
     */
    protected $gameUid;

    /**
     * @var string
     */
    protected $code;

    /**
     * @return int
     */
    public function getGameUid() {
        return $this->gameUid;
    }

    /**
     * @param int $gameUid
     * @return Game_model
     */
    public function setGameUid($gameUid) {
        $this->gameUid = $gameUid;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Game_model
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }



}