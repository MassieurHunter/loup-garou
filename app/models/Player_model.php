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
     * @var string
     */
    protected $name;


    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $arrRoleModel = [];

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
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Player_model
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @param int $gameUid
     * @param Role_model $newRole
     */
    public function addNewRole($gameUid, $newRole) {

        $arrRoleModels = $this->getArrRoleModel($gameUid);

        $newRoleOrder = $this->getCurrentRoleModel($gameUid)->getRoleUid() ? count($arrRoleModels) : 0;

        $this->arrRoleModel = [];
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
    public function getArrRoleModel($gameUid) {
        if (!isset($this->arrRoleModel[$gameUid]) || empty($this->arrRoleModel[$gameUid])) {
            $this->initRoles($gameUid);
        }

        return $this->arrRoleModel[$gameUid];
    }

    /**
     * @param int $gameUid
     */
    public function initRoles($gameUid) {

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
            $this->arrRoleModel[$gameUid] = $this->_roleModel;
        }

    }

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
     * @param int $gameUid
     * @return Role_model
     */
    public function getCurrentRoleModel($gameUid) {
        $arrRoleModel = $this->getArrRoleModel($gameUid);

        return end($arrRoleModel);
    }

    /**
     * @param int $gameUid
     * @return string
     */
    public function getOriginalRoleName($gameUid) {

        return $this->getOriginalRoleModel($gameUid)->getName();

    }

    /**
     * @param int $gameUid
     * @return Role_model
     */
    public function getOriginalRoleModel($gameUid) {
        $arrRoleModel = $this->getArrRoleModel($gameUid);

        return $arrRoleModel[0];
    }

    /**
     * @param int $gameUid
     */
    public function executeOriginalRoleAction($gameUid) {

        $this->getOriginalRoleModel($gameUid)->action();

    }

    /**
     * @param int $gameUid
     * @return string
     */
    public function getCurrentRoleName($gameUid) {

        return $this->getCurrentRoleModel($gameUid)->getName();

    }


    /**
     * Voter pour un joueur
     * @param int $gameUid
     * @param int $targetUid
     */
    public function vote($gameUid, $targetUid) {

        $this->load->model('vote_model', 'newVote');
        $this->newVote
            ->setGameUid($gameUid)
            ->setPlayerUid($this->getPlayerUid())
            ->setTargetUid($targetUid)
            ->create();
    }

    /**
     * @param int $gameUid
     * @param mixed ...$arguments
     */
    public function roleAction($gameUid, ...$arguments) {
        return $this->getOriginalRoleModel($gameUid)->action(...$arguments);
    }

    /**
     *
     * @param int $gameUid
     * @param mixed ...$arguments
     */
    public function roleSecondAction($gameUid, ...$arguments) {
        return $this->getOriginalRoleModel($gameUid)->secondAction(...$arguments);
    }


}