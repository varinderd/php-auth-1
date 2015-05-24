<?php
namespace pmill\Auth;

use pmill\Auth\Exceptions\HashException;

class Password implements Interfaces\PasswordHelper
{

    /**
     * @param string $savedPassword
     * @param string $submittedPassword
     * @return bool
     */
    public function verify($savedPassword, $submittedPassword)
    {
        return password_verify($submittedPassword, $savedPassword);
    }

    /**
     * @param string $submittedPassword
     * @return bool|string
     * @throws HashException
     */
    public function hash($submittedPassword)
    {
        $hash = password_hash($submittedPassword, PASSWORD_BCRYPT);
        if ($hash === false) {
            throw new HashException('Failed to hash submitted password');
        }

        return $hash;
    }

}