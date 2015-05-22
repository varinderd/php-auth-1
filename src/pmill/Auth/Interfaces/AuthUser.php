<?php
namespace pmill\Auth\Interfaces;

interface AuthUser
{

    /**
     * @return int
     */
    public function getAuthId();

    /**
     * @return string
     */
    public function getAuthUsername();

    /**
     * @return string
     */
    public function getAuthPassword();

    /**
     * @return string|null
     */
    public function getTwoFactorSecret();

}