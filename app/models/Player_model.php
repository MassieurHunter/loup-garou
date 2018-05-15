<?php


/**
 *
 *
 * @property \Role_model $_roleModel
 * @property \Vote_model $newVote
 *
 */
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
    protected $roleUid;

    /**
     * @var bool
     */
    protected $dead;

    /**
     * @var Role_model
     */
    protected $roleModel;

    /**
     * @return int
     */
    public function getPlayerUid() {
        return $this->playerUid;
    }

    /**
     * @param int $playerUid
     * @return Player_model
     */
    public function setPlayerUid($playerUid) {
        $this->playerUid = $playerUid;
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
     * @return Player_model
     */
    public function setGameUid($gameUid) {
        $this->gameUid = $gameUid;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Player_model
     */
    public function setName($name) {
        $this->name = $name;
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

    public function initRoleModel() {
        $this->load->model('Roles/role_model', '_roleModel');

        $this->roleModel = clone $this->_roleModel;

        $this->roleModel->init($this->getRoleUid());

    }

    /**
     * @return Role_model
     */
    public function getRoleModel() {
        if (empty($this->roleModel)) {
            $this->initRoleModel();
        }

        return $this->roleModel;
    }

    /**
     * @return int
     */
    public function getRoleUid() {
        return $this->roleUid;
    }

    /**
     * @param int $roleUid
     * @return Player_model
     */
    public function setRoleUid($roleUid) {
        $this->roleUid = $roleUid;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoleName() {
        return $this->getRoleModel()->getName();
    }


    /**
     * Voter pour un joueur
     * @param $targetUid
     */
    public function voter($targetUid) {

        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;

        $this->load->model('vote_model', 'newVote');
        $this->newVote
            ->setGameUid($oPlayer->getGameUid())
            ->setPlayerUid($oPlayer->getPlayerUid())
            ->setTargetUid($targetUid)
            ->create()
            ;
    }


}