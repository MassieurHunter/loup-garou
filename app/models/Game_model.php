<?php

/**
 * Class Game_model
 *
 * @property Player_model $_playerModel
 * @property Role_model $_roleModel
 */
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
     * @var string
     */
    public $player_games_table = 'games_players';

    /**
     * @var int
     */
    protected $gameUid;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var Player_model[]
     */
    protected $arrPlayers = [];

    /**
     * @var Role_model[]
     */
    protected $arrRoles = [];

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

    /**
     * @param $code
     */
    public function initByCode($code) {
        $infos = $this
            ->where('code', $code)
            ->get($this->table)
            ->row();

        $this->init(false, $infos);
    }

    /**
     * @param Player_model $oPlayer
     */
    public function addPlayer($oPlayer) {
        $this->db
            ->set('gameUid', $this->getGameUid())
            ->set('playerUid', $oPlayer->getPlayerUid())
            ->insert($this->player_games_table);

        $this->arrPlayers[$oPlayer->getPlayerUid()] = $oPlayer;
    }

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
     *
     */
    public function giveRoleToPlayers() {

        $arrRoles = $this->getRolesForCasting();
        $arrPlayer = $this->getPlayers();

        shuffle($arrRoles);
        shuffle($arrPlayer);

        foreach ($arrPlayer as $key => $playerModel) {
            $roleModel = $arrRoles[$key];
            $playerModel->addNewRole($this->getGameUid(), $roleModel);
        }


    }

    /**
     * @return Role_model[]
     */
    public function getRolesForCasting() {
        $arrRoles = $this->getRoles();
        $arrPlayers = $this->getPlayers();
        $nbPlayers = count($arrPlayers);

        $arrSort = [];

        foreach ($arrRoles as $roleModel) {

            $arrSort[] = $roleModel->getCastingOrder();

        }

        array_multisort($arrSort, SORT_ASC, $arrRoles);


        if ($arrRoles[$nbPlayers - 1]->getModel() === 'francmac' && $arrRoles[$nbPlayers - 2]->getModel() !== 'francmac') {
            unset($arrRoles[$nbPlayers - 1]);
        }

        return array_splice($arrRoles, 0, $nbPlayers);

    }

    /**
     * @return Role_model[]
     */
    public function getRoles() {

        if (empty($this->arrRoles)) {
            $this->initRoles();
        }

        return $this->arrRoles;

    }

    /**
     *
     */
    public function initRoles() {
        $this->load->model('Roles/role_model', '_roleModel');

        $arrRoles = $this->db
            ->get($this->_role->table)
            ->result();

        foreach ($arrRoles as $role) {
            $roleModel = clone $this->_roleModel;
            $roleModel->init(false, $role);

            for ($i = 0; $i < $roleModel->getNb(); $i++) {

                $this->arrRoles[] = $roleModel;

            }
        }

    }

    /**
     * @return Player_model[]
     */
    public function getPlayers() {

        if (empty($this->arrPlayers)) {
            $this->initPlayers();
        }

        return $this->arrPlayers;

    }

    /**
     *
     */
    public function initPlayers() {
        $this->load->model('player_model', '_playerModel');

        $arrPlayers = $this->db
            ->select($this->_playerModel->table . '.*')
            ->where($this->primary_key, $this->getGameUid())
            ->join($this->player_games_table, $this->_playerModel->primary_key)
            ->get($this->_playerModel->table)
            ->result();

        foreach ($arrPlayers as $player) {
            $playerModel = clone $this->_playerModel;
            $playerModel->init(false, $player);

            $this->arrPlayers[] = $playerModel;
        }

    }

    /**
     * @return Role_model[]
     */
    public function getRolesForRunning() {
        $arrRoles = $this->getRolesForCasting();

        $arrSort = [];

        foreach ($arrRoles as $roleModel) {

            $arrSort[] = $roleModel->getRunningOrder();

        }

        array_multisort($arrSort, SORT_ASC, $arrRoles);


        return $arrRoles;
    }

}