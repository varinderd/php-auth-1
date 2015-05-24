<?php
namespace pmill\Auth\Interfaces;

interface PasswordHelper
{

    /**
     * @return bool
     */
    public function verify($savedPassword, $submittedPassword);

    /**
     * @return string
     */
    public function hash($submittedPassword);

}