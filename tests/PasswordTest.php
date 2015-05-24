<?php

class PasswordTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $rawPassword = 'uly5qHYeMI4csC';

    /**
     * @var string
     */
    protected $correctHashedPassword = '$2y$10$dYXhwn/UUEPR3h5EymE5YeSpkieq07QdZN9D0csqiRG3DwxupANWO';

    public function testHash()
    {
        $passwordHelper = new \pmill\Auth\Password;
        $hashResult = $passwordHelper->hash($this->rawPassword);

        $verificationResult = password_verify($this->rawPassword, $hashResult);
        $this->assertTrue($verificationResult);
    }

    public function testVerifyCorrect()
    {
        $passwordToTest = 'hunter2';

        $passwordHelper = new \pmill\Auth\Password;
        $verifyPasswordResult = $passwordHelper->verify(password_hash($passwordToTest, PASSWORD_BCRYPT), $passwordToTest);

        $this->assertTrue($verifyPasswordResult);
    }

    public function testVerifyIncorrect()
    {
        $passwordHelper = new \pmill\Auth\Password;
        $verifyPasswordResult = $passwordHelper->verify($passwordHelper->hash('hunter2'), 'incorrect-password');

        $this->assertFalse($verifyPasswordResult);
    }

}