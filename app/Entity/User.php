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
    protected $salt;
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

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    private function getSalt()
    {
        if (empty($this->salt)) {
            $this->salt = str_random(32);
        }
        return $this->salt;
    }

    public function makePassword($password = null)
    {
        return hash('sha256', ($password ?: $this->password) . $this->getSalt());
    }

    public function setPassword($password)
    {
        $this->password = $this->makePassword($password);
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