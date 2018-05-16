<?php


/**
 *
 * @property \Player_model $_oTestPlayer
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
    public function setPassword(string $password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Generate and return a key to autolog on the website
     *
     * @return string
     */
    public function getAutoLoginHash() {
        return sha1(
            $this->getPlayerUid()
            . ' toi pas changer assiette pour fromage'
            . $this->getName()
            . 'la fleur en bouquet fanne... et jamais de renait !'
        );
    }


    /**
     * Login with ws_auth cookie or session
     *
     * @return boolean
     */
    public function wsAuthLogin()
    {
        $this->load->model('player/player_model', '_oTestPlayer');
        $wsAuth = $this->session->playerdata('ws_auth');
        $splitedAuth = explode(':', $wsAuth);
        $ok = false;
        /*
         * We test if the cookie is valid
         */
        if (count($splitedAuth) == 2) {
            $playerUid = $splitedAuth[0];
            $hashedPassword = $splitedAuth[1];
            /*
             * we test if the two inputs aren't empty
             */
            if ($playerUid && $hashedPassword) {
                /*
                 * We init the player's infos from his playerId
                 */
                $this->_oTestPlayer->init($playerUid);

                /*
                 * We test the email to see if the init succeded
                 */
                if ($this->_oTestPlayer->getName()) {
                    /*
                     * We test the hashed password
                     */
                    if ($this->_oTestPlayer->testPassword($hashedPassword, true)) {
                        $ok = true;
                        $this->init($playerUid);
                    }
                }//end test email for init
            }//end inputs
        } //end valid cookie

        return $ok;
    }

    /**
     * Test the password for the user
     *
     * @param string $password
     * @param boolean $hashed set to true if you want to test a hash
     * @return boolean
     */
    public function testPassword($password, $hashed = false) {
        $hash = $hashed ? $password : $this->hashPassword($password);
        return $hash == $this->getPassword();
    }

    /**
     * Return the hashed version of the inputed password
     *
     * @param string $password
     * @return string
     */
    public function hashPassword($password) {
        $pass	 = sha1(stripslashes($password));

        $hashedPassword = password_hash($pass, PASSWORD_BCRYPT, array(
            "salt"	 => "le mot de passe c'est trois",
            "cost"	 => 12));  // Cost : If we use a higher cost, the algorithm will use more time. (Against Brute Force)

        return $hashedPassword;
    }

    /**
     * @param int $gameUid
     * @param Role_model $newRole
     */
    public function addNewRole(int $gameUid, Role_model $newRole) {

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
    public function getArrRoleModel(int $gameUid) {
        if (!isset($this->arrRoleModel[$gameUid]) || empty($this->arrRoleModel[$gameUid])) {
            $this->initRoles($gameUid);
        }

        return $this->arrRoleModel[$gameUid];
    }

    /**
     * @param int $gameUid
     */
    public function initRoles(int $gameUid) {

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
    public function setPlayerUid(int $playerUid) {
        $this->playerUid = $playerUid;
        return $this;
    }

    /**
     * @param int $gameUid
     * @return Role_model
     */
    public function getCurrentRoleModel(int $gameUid) {
        $arrRoleModel = $this->getArrRoleModel($gameUid);

        return end($arrRoleModel);
    }

    /**
     * @param int $gameUid
     * @return string
     */
    public function getOriginalRoleName(int $gameUid) {

        return $this->getOriginalRoleModel($gameUid)->getName();

    }

    /**
     * @param int $gameUid
     * @return Role_model
     */
    public function getOriginalRoleModel(int $gameUid) {
        $arrRoleModel = $this->getArrRoleModel($gameUid);

        return $arrRoleModel[0];
    }

    /**
     * @param int $gameUid
     */
    public function executeOriginalRoleAction(int $gameUid) {

        $this->getOriginalRoleModel($gameUid)->action();

    }

    /**
     * @param int $gameUid
     * @return string
     */
    public function getCurrentRoleName(int $gameUid) {

        return $this->getCurrentRoleModel($gameUid)->getName();

    }


    /**
     * Voter pour un joueur
     * @param int $gameUid
     * @param int $targetUid
     */
    public function vote(int $gameUid, int $targetUid) {

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
    public function roleAction(int $gameUid, ...$arguments) {
        return $this->getOriginalRoleModel($gameUid)->action(...$arguments);
    }

    /**
     *
     * @param int $gameUid
     * @param mixed ...$arguments
     */
    public function roleSecondAction(int $gameUid, ...$arguments) {
        return $this->getOriginalRoleModel($gameUid)->secondAction(...$arguments);
    }


}