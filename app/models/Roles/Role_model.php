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
        return $this->loup;
    }

    /**
     * @param bool $loup
     * @return Role_model
     */
    public function setLoup($loup) {
        $this->loup = $loup;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTanneur() {
        return $this->tanneur;
    }

    /**
     * @param bool $tanneur
     * @return Role_model
     */
    public function setTanneur($tanneur) {
        $this->tanneur = $tanneur;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVillageois() {
        return $this->villageois;
    }

    /**
     * @param bool $villageois
     * @return Role_model
     */
    public function setVillageois($villageois) {
        $this->villageois = $villageois;
        return $this;
    }






}