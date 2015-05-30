<?php
namespace pmill\Auth\Interfaces;

interface TwoFactorAuthenticationHelper
{

    /**
     * @return bool
     */
    public function verify($savedSecret, $submittedSecret);

    /**
     * @param AuthUser $userToAuthenticate
     * @return mixed
     */
    public function required(AuthUser $userToAuthenticate);
}