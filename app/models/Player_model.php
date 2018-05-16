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
    public $player_roles_table = 'players_game_role';

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
     * @var Role_model[]
     */
    protected $arrRoleModel;

    /**
     * @return int
     */
    public function getPlayerUid()
    {
        return $this->playerUid;
    }

    /**
     * @param int $playerUid
     * @return Player_model
     */
    public function setPlayerUid($playerUid)
    {
        $this->playerUid = $playerUid;
        return $this;
    }

    /**
     * @return int
     */
    public function getGameUid()
    {
        return $this->gameUid;
    }

    /**
     * @param int $gameUid
     * @return Player_model
     */
    public function setGameUid($gameUid)
    {
        $this->gameUid = $gameUid;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Player_model
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    public function initRoles()
    {
        $CI = get_instance();

        /** @var Game_model $oGame */
        $oGame = $CI->oCurrentGame;

        $this->arrRoleModel = array();
        $this->load->model('Roles/role_model', '_roleModel');

        $arrRoles = $this->db
            ->select($this->_roleModel->table . '.*')
            ->where('playerUid', $this->getPlayerUid())
            ->where('gameUid', $oGame->getGameUid())
            ->join($this->player_roles_table, $this->_roleModel->primary_key)
            ->order_by('order')
            ->get($this->_roleModel->table)
            ->result();

        foreach ($arrRoles as $role) {
            $roleModel = clone $this->_roleModel;
            $this->arrRoleModel[] = $roleModel->init(false, $role);
        }

        if (empty($this->arrRoleModel)) {
            $this->arrRoleModel[] = $this->_roleModel;
        }

    }

    /**
     * @return Role_model[]
     */
    public function getArrRoleModel()
    {
        if (empty($this->arrRoleModel)) {
            $this->initRoles();
        }

        return $this->arrRoleModel;
    }

    /**
     * @return Role_model
     */
    public function getOriginalRoleModel()
    {
        $arrRoleModel = $this->getArrRoleModel();

        return $arrRoleModel[0];
    }

    /**
     * @return Role_model
     */
    public function getCurrentRoleModel()
    {
        $arrRoleModel = $this->getArrRoleModel();

        return end($arrRoleModel);
    }

    /**
     * @return string
     */
    public function getOriginalRoleName()
    {

        return $this->getOriginalRoleModel()->getName();

    }

    /**
     *
     */
    public function executeOriginalRoleAction()
    {

        $this->getOriginalRoleModel()->getSubmodel()->action();

    }

    /**
     * @return string
     */
    public function getCurrentRoleName()
    {

        return $this->getCurrentRoleName()->getName();

    }



    /**
     * Voter pour un joueur
     * @param $targetUid
     */
    public function voter($targetUid)
    {
        $CI = get_instance();

        /** @var Game_model $oGame */
        $oGame = $CI->oCurrentGame;

        $this->load->model('vote_model', 'newVote');
        $this->newVote
            ->setGameUid($oGame->getGameUid())
            ->setPlayerUid($this->getPlayerUid())
            ->setTargetUid($targetUid)
            ->create();
    }

    public function actionRole()
    {
        $this->getOriginalRoleModel();
    }


}