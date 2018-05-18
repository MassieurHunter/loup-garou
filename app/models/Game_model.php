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
     * @var int
     */
    protected $maxPlayers;

    /**
     * @var int
     */
    protected $nbPlayers;

    /**
     * @var boolean
     */
    protected $finished;

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
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Game_model
     */
    public function setCode(string $code): Game_model {
        $this->code = $code;
        return $this;
    }

    /**
     * @return $this
     */
    public function generateCode(): Game_model {
        $newCode = random_string();
        $this->setCode($newCode);
        return $this;
    }

    /**
     * @param $code
     */
    public function initByCode(string $code) {
        $infos = $this->db
            ->where('code', $code)
            ->get($this->table)
            ->row();

        $this->init(false, $infos);
    }

    /**
     * @param Player_model $oPlayer
     */
    public function addPlayer(Player_model $oPlayer) {
        $this->db
            ->set('gameUid', $this->getGameUid())
            ->set('playerUid', $oPlayer->getPlayerUid())
            ->insert($this->player_games_table);

        $this->arrPlayers[$oPlayer->getPlayerUid()] = $oPlayer;
    }

    /**
     * @return int
     */
    public function getGameUid(): int {
        return $this->gameUid;
    }

    /**
     * @param int $gameUid
     * @return Game_model
     */
    public function setGameUid(int $gameUid): Game_model {
        $this->gameUid = $gameUid;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    /**
     * @param int $maxPlayers
     * @return Game_model
     */
    public function setMaxPlayers(int $maxPlayers): Game_model {
        $this->maxPlayers = $maxPlayers;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbPlayers(): int {
        return $this->nbPlayers;
    }

    /**
     * @param int $nbPlayers
     * @return Game_model
     */
    public function setNbPlayers(int $nbPlayers): Game_model {
        $this->nbPlayers = $nbPlayers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool {
        return $this->finished;
    }

    /**
     * @param bool $finished
     * @return Game_model
     */
    public function setFinished(bool $finished): Game_model {
        $this->finished = $finished;
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
    public function getRolesForCasting(): array {
        $arrRoles = $this->getRoles();
        $arrPlayers = $this->getPlayers();
        $nbPlayers = count($arrPlayers);

        $arrSort = [];

        foreach ($arrRoles as $roleModel) {

            $arrSort[] = $roleModel->getCastingOrder();

        }

        array_multisort($arrSort, SORT_ASC, $arrRoles);


        if ($arrRoles[$nbPlayers - 1]->getModel() === 'francmac' && $arrRoles[$nbPlayers - 2]->getModel() !== 'francmac') {
            unset($arrRoles[$nbPlayers - 2]);
        }

        return array_splice($arrRoles, 0, $nbPlayers);

    }

    /**
     * @return Role_model[]
     */
    public function getRoles(): array {

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
    public function getPlayers(): array {

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
    public function getRolesForRunning(): array {
        $arrRoles = $this->getRolesForCasting();

        $arrSort = [];

        foreach ($arrRoles as $roleModel) {

            $arrSort[] = $roleModel->getRunningOrder();

        }

        array_multisort($arrSort, SORT_ASC, $arrRoles);


        return $arrRoles;
    }

}