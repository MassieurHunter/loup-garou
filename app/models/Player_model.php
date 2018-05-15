<?php

class Player_model extends MY_Model
{
    const CAMP_TANNEUR    = 'tanneur';
    const CAMP_VILLAGEOIS = 'villageois';
    const CAMP_LOUP       = 'loup';


    /**
     * @var string
     */
    public $table = 'players';

    /**
     * @var string
     */
    public $primary_key = 'playerUid';


    /**
     * @var int
     */
    protected $playerUid;

    /**
     * @var int
     */
    protected $gameUid;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $role;

    /**
     * @var bool
     */
    protected $dead;


    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param string $action
     * @return Player_model
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getCamps() {
        return $this->camps;
    }

    /**
     * @param string $camps
     * @return Player_model
     */
    public function setCamps($camps) {
        $this->camps = $camps;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDead() {
        return $this->dead;
    }

    /**
     * @param bool $dead
     * @return Player_model
     */
    public function setDead($dead) {
        $this->dead = $dead;
        return $this;
    }

    /**
     * Voter pour un perso
     */
    public function voter(){

    }




}