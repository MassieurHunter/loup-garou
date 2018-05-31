<?php

/**
 * Class Role_model
 *
 * @property Log_model $_log
 * @property Role_model $subModel
 *
 */
class Role_model extends MY_Model
{
    /**
     * @var string
     */
    public $table = 'roles';
    /**
     * @var string
     */
    public $primary_key = 'roleUid';
    /**
     * @var array
     */
    public $basics = [
        'name' => 'getName',
        'description' => 'getDescription',
        'model' => 'getModel',
        'firstAction' => 'hasFirstAction',
        'firstActionName' => 'getFirstActionName',
        'firstActionPassive' => 'isFirstActionPassive',
        'firstActionNbTargets' => 'getFirstActionNbTargets',
        'firstActionTargetType' => 'getFirstActionTargetType',
        'secondAction' => 'hasSecondAction',
        'secondActionNeedFailedFirst' => 'isSecondActionNeedFailedFirst',
        'secondActionName' => 'getSecondActionName',
        'secondActionPassive' => 'isSecondActionPassive',
        'secondActionNbTargets' => 'getSecondActionNbTargets',
        'secondActionTargetType' => 'getSecondActionTargetType',
        'loup' => 'isLoup',
        'villageois' => 'isVillageois',
        'tanneur' => 'isTanneur',
        'bootstrapClass' => 'getBootstrapClass'
    ];
    /**
     * @var int
     */
    protected $roleUid;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $model;
    /**
     * @var int
     */
    protected $nb;
    /**
     * @var bool
     */
    protected $loup;
    /**
     * @var bool
     */
    protected $tanneur;
    /**
     * @var bool
     */
    protected $villageois;
    /**
     * @var int
     */
    protected $castingOrder;
    /**
     * @var int
     */
    protected $runningOrder;
    /**
     * @var bool
     */
    protected $firstAction;
    /**
     * @var bool
     */
    protected $firstActionPassive;
    /**
     * @var string
     */
    protected $firstActionName;
    /**
     * @var int
     */
    protected $firstActionNbTargets;
    /**
     * @var string
     */
    protected $firstActionTargetType;
    /**
     * @var bool
     */
    protected $secondAction;
    /**
     * @var bool
     */
    protected $secondActionPassive;
    /**
     * @var bool
     */
    protected $secondActionNeedFailedFirst;
    /**
     * @var string
     */
    protected $secondActionName;
    /**
     * @var int
     */
    protected $secondActionNbTargets;
    /**
     * @var string
     */
    protected $secondActionTargetType;

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return Role_model
     */
    public function setName(string $name): Role_model
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->description;
    }

    /**
     * @param string $description
     * @return Role_model
     */
    public function setDescription(string $description): Role_model
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getNb(): int
    {
        return (int)$this->nb;
    }

    /**
     * @param int $nb
     * @return Role_model
     */
    public function setNb(int $nb): Role_model
    {
        $this->nb = $nb;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVillageois(): bool
    {
        return $this->villageois == true;
    }

    /**
     * @param bool $villageois
     * @return Role_model
     */
    public function setVillageois(bool $villageois): Role_model
    {
        $this->villageois = $villageois;
        return $this;
    }

    /**
     * @return string
     */
    public function getBootstrapClass(): string
    {
        if ($this->isLoup()) {

            $class = 'dark';

        } elseif ($this->isTanneur()) {

            $class = 'warning';

        } else {

            $class = 'info';

        }

        return $class;
    }

    /**
     * @return bool
     */
    public function isLoup(): bool
    {
        return $this->loup == true;
    }

    /**
     * @param bool $loup
     * @return Role_model
     */
    public function setLoup(bool $loup): Role_model
    {
        $this->loup = $loup;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTanneur(): bool
    {
        return $this->tanneur == true;
    }

    /**
     * @param bool $tanneur
     * @return Role_model
     */
    public function setTanneur(bool $tanneur): Role_model
    {
        $this->tanneur = $tanneur;
        return $this;
    }

    /**
     * @return int
     */
    public function getCastingOrder(): int
    {
        return (int)$this->castingOrder;
    }

    /**
     * @param int $castingOrder
     * @return Role_model
     */
    public function setCastingOrder(int $castingOrder): Role_model
    {
        $this->castingOrder = $castingOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getRunningOrder(): int
    {
        return (int)$this->runningOrder;
    }

    /**
     * @param int $runningOrder
     * @return Role_model
     */
    public function setRunningOrder(int $runningOrder): Role_model
    {
        $this->runningOrder = $runningOrder;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFirstActionPassive(): bool
    {
        return $this->firstActionPassive == true;
    }

    /**
     * @return int
     */
    public function getFirstActionNbTargets(): int
    {
        return (int) $this->firstActionNbTargets;
    }

    /**
     * @return bool
     */
    public function isSecondActionNeedFailedFirst(): bool
    {
        return $this->secondActionNeedFailedFirst == true;
    }

    /**
     * @param bool $secondActionNeedFailedFirst
     * @return Role_model
     */
    public function setSecondActionNeedFailedFirst(bool $secondActionNeedFailedFirst): Role_model
    {
        $this->secondActionNeedFailedFirst = $secondActionNeedFailedFirst;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecondActionPassive(): bool
    {
        return $this->secondActionPassive == true;
    }

    /**
     * @return int
     */
    public function getSecondActionNbTargets(): int
    {
        return (int) $this->secondActionNbTargets;
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function firstAction($arguments): array
    {
        $return = [];

        if ($this->getSubmodel()->hasFirstAction()) {
            $this->logFirstAction($arguments);
            $return = $this->getSubmodel()->firstAction($arguments);
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function hasFirstAction(): bool
    {
        return $this->firstAction == true;
    }

    /**
     * @return Role_model
     */
    public function getSubmodel(): Role_model
    {

        if (empty($this->subModel)) {

            $this->load->model('Roles/' . ucfirst($this->getModel()) . '_model', 'subModel');
            $this->subModel->init($this->getRoleUid());

        }

        return $this->subModel;

    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return (string)$this->model;
    }

    /**
     * @param string $model
     * @return Role_model
     */
    public function setModel(string $model): Role_model
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return int
     */
    public function getRoleUid(): int
    {
        return (int)$this->roleUid;
    }

    /**
     * @param int $roleUid
     * @return Role_model
     */
    public function setRoleUid(int $roleUid): Role_model
    {
        $this->roleUid = $roleUid;
        return $this;
    }

    /**
     * @param array $action
     */
    protected function logFirstAction(array $action)
    {
        $this->logAction(1, $action);
    }

    /**
     * @param int $actionNumber
     * @param array $actionInfos
     */
    protected function logAction(int $actionNumber, array $actionInfos)
    {
        $targetType = $actionNumber === 1 ? $this->getFirstActionTargetType() : $this->getSecondActionTargetType();
        $player1 = isset($actionInfos['player_1']) ? $actionInfos['player_1'] : 0;
        $player2 = isset($actionInfos['player_2']) ? $actionInfos['player_2'] : 0;
        $card1 = isset($actionInfos['card_1']) ? $actionInfos['card_1'] : 0;
        $card2 = isset($actionInfos['card_2']) ? $actionInfos['card_2'] : 0;

        $target1 = $targetType === 'player' ? $player1 : ($targetType === 'card' ? $card1 : 0);
        $target2 = $targetType === 'player' ? $player2 : ($targetType === 'card' ? $card2 : 0);
        $gameUid = $actionInfos['gameUid'];
        /** @var Player_model $currentPlayer */
        $currentPlayer = $actionInfos['currentPlayer'];

        $this->load->model('log_model', '_log');

        $this->_log
            ->setGameUid($gameUid)
            ->setPlayerUid($currentPlayer->getPlayerUid())
            ->setRoleUid($this->getRoleUid())
            ->setAction($actionNumber === 1 ? $this->getFirstActionName() : $this->getSecondActionName())
            ->setTarget1($target1)
            ->setTarget2($target2)
            ->create();

        unset($this->_log);

    }

    /**
     * @return string
     */
    public function getFirstActionTargetType(): string
    {
        return (string)$this->firstActionTargetType;
    }

    /**
     * @return string
     */
    public function getSecondActionTargetType(): string
    {
        return (string)$this->secondActionTargetType;
    }

    /**
     * @return string
     */
    public function getFirstActionName(): string
    {
        return (string)$this->firstActionName;
    }

    /**
     * @return string
     */
    public function getSecondActionName(): string
    {
        return (string)$this->secondActionName;
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function secondAction($arguments): array
    {
        $return = [];

        if ($this->getSubmodel()->hasSecondAction()) {
            $this->logSecondAction($arguments);
            $return = $this->getSubmodel()->secondAction($arguments);
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function hasSecondAction(): bool
    {
        return $this->secondAction == true;
    }

    /**
     * @param array $action
     */
    protected function logSecondAction(array $action)
    {
        $this->logAction(2, $action);
    }


}