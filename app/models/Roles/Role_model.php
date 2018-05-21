<?php

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
     * @var Role_model
     */
    public $subModel;
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
     * @var array
     */
    public $basics = [
        'name' => 'getName',
        'description' => 'getDescription',
        'model' => 'model',
        'firstAction' => 'hasFirstAction',
        'firstActionName' => 'getFirstActionName',
        'firstActionPassive' => 'isFirstActionPassive',
        'firstActionNbTargets' => 'getFirstActionNbTargets',
        'firstActionTargetType' => 'getFirstActionTargetType',
        'secondAction' => 'hasFirstAction',
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
     * @return int
     */
    public function getRoleUid(): int
    {
        return $this->roleUid;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
        return $this->description;
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
        return $this->nb;
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
    public function isLoup(): bool
    {
        return $this->loup;
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
        return $this->tanneur;
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
     * @return bool
     */
    public function isVillageois(): bool
    {
        return $this->villageois;
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
     * @return int
     */
    public function getCastingOrder(): int
    {
        return $this->castingOrder;
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
        return $this->runningOrder;
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
     * @return Role_model
     */
    public function getSubmodel(): Role_model
    {

        if (empty($this->subModel)) {

            $this->load->model('Roles/' . $this->getModel() . '_model', 'subModel');

        }

        return $this->subModel;

    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
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
     * @return bool
     */
    public function hasFirstAction(): bool
    {
        return $this->firstAction;
    }

    /**
     * @return bool
     */
    public function isFirstActionPassive(): bool
    {
        return $this->firstActionPassive;
    }

    /**
     * @return string
     */
    public function getFirstActionName(): string
    {
        return $this->firstActionName;
    }

    /**
     * @return int
     */
    public function getFirstActionNbTargets(): int
    {
        return $this->firstActionNbTargets;
    }

    /**
     * @return string
     */
    public function getFirstActionTargetType(): string
    {
        return $this->firstActionTargetType;
    }


    /**
     * @return bool
     */
    public function hasSecondAction(): bool
    {
        return $this->secondAction;
    }

    /**
     * @return bool
     */
    public function isSecondActionPassive(): bool
    {
        return $this->secondActionPassive;
    }

    /**
     * @return string
     */
    public function getSecondActionName(): string
    {
        return $this->secondActionName;
    }

    /**
     * @return int
     */
    public function getSecondActionNbTargets(): int
    {
        return $this->secondActionNbTargets;
    }

    /**
     * @return string
     */
    public function getSecondActionTargetType(): string
    {
        return $this->secondActionTargetType;
    }


    /**
     * @param mixed ...$arguments
     */
    public function action(...$arguments)
    {
        $this->getSubmodel()->action(...$arguments);
    }

    /**
     * @param mixed ...$arguments
     */
    public function secondAction(...$arguments)
    {
        $this->getSubmodel()->secondAction(...$arguments);
    }


}