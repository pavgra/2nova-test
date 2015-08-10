<?php namespace App\Entity;

class User
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $login;
    /** @var string */
    protected $password;
    /** @var string */
    protected $name;
    /** @var \DateTime */
    protected $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getPassword() {
        return $this->password;
    }

    public static function makePassword($password) {
        return hash('sha256', $password);
    }

    public function setPassword($password) {
        $this->password = static::makePassword($password);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}