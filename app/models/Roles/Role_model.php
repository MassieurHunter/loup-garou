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
    protected $class;
    /**
     * @var bool
     */
    protected $isLoup;
    /**
     * @var bool
     */
    protected $isTanneur;
    /**
     * @var bool
     */
    protected $isVillageois;
    /**
     * @var bool
     */
    protected $hasAction;

    /**
     * @return int
     */
    public function getRoleUid() {
        return $this->roleUid;
    }

    /**
     * @param int $roleUid
     * @return Role_model
     */
    public function setRoleUid($roleUid) {
        $this->roleUid = $roleUid;
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
     * @return Role_model
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Role_model
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param string $class
     * @return Role_model
     */
    public function setClass($class) {
        $this->class = $class;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoup() {
        return $this->isLoup;
    }

    /**
     * @param bool $isLoup
     * @return Role_model
     */
    public function setIsLoup($isLoup) {
        $this->isLoup = $isLoup;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTanneur() {
        return $this->isTanneur;
    }

    /**
     * @param bool $isTanneur
     * @return Role_model
     */
    public function setIsTanneur($isTanneur) {
        $this->isTanneur = $isTanneur;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVillageois() {
        return $this->isVillageois;
    }

    /**
     * @param bool $isVillageois
     * @return Role_model
     */
    public function setIsVillageois($isVillageois) {
        $this->isVillageois = $isVillageois;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasAction() {
        return $this->hasAction;
    }

    /**
     * @param bool $hasAction
     * @return Role_model
     */
    public function setHasAction($hasAction) {
        $this->hasAction = $hasAction;
        return $this;
    }



}