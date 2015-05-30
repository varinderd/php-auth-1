<?php
namespace pmill\Auth;

use pmill\Auth\Exceptions\HashException;

class TwoFactorAuthentication implements Interfaces\TwoFactorAuthenticationHelper
{


    /**
     * @param string $savedSecret
     * @param string $submittedSecret
     * @return bool
     */
    public function verify($savedSecret, $submittedSecret)
    {
        return $submittedSecret == $savedSecret;
    }

    /**
     * @param Interfaces\AuthUser $userToAuthenticate
     * @return bool
     */
    public function required(Interfaces\AuthUser $userToAuthenticate)
    {
        return !is_null($userToAuthenticate->getTwoFactorSecret());
    }

}