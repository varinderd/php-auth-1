<?php

class TwoAuthUser implements \pmill\Auth\Interfaces\AuthUser
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $twoAuthSecret;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $auth = new \pmill\Auth\Auth;
        $this->password = $auth->hashPassword($password);
    }

    /**
     * @return string
     */
    public function getTwoAuthSecret()
    {
        return $this->twoAuthSecret;
    }

    /**
     * @param string $twoAuthSecret
     */
    public function setTwoAuthSecret($twoAuthSecret)
    {
        $this->twoAuthSecret = $twoAuthSecret;
    }

    /**
     * @inherit
     */
    public function getAuthId()
    {
        return $this->id;
    }

    /**
     * @inherit
     */
    public function getAuthUsername()
    {
        return $this->username;
    }

    /**
     * @inherit
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * @inherit
     */
    public function getTwoFactorSecret()
    {
        return $this->twoAuthSecret;
    }

}