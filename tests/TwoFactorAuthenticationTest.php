<?php

class TwoFactorAuthenticationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $rawPassword = 'uly5qHYeMI4csC';

    /**
     * @var string
     */
    protected $correctHashedPassword = '$2y$10$dYXhwn/UUEPR3h5EymE5YeSpkieq07QdZN9D0csqiRG3DwxupANWO';

    public function testVerifyTrue()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $twoFactorHelper = new \pmill\Auth\TwoFactorAuthentication;
        $verificationResult = $twoFactorHelper->verify($user->getTwoFactorSecret(), 'abcdef123456');
        $this->assertTrue($verificationResult);
    }

    public function testVerifyFalse()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $twoFactorHelper = new \pmill\Auth\TwoFactorAuthentication;
        $verificationResult = $twoFactorHelper->verify($user->getTwoFactorSecret(), 'incorrect');
        $this->assertFalse($verificationResult);
    }

    public function testRequiredTrue()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $twoFactorHelper = new \pmill\Auth\TwoFactorAuthentication;
        $this->assertTrue($twoFactorHelper->required($user));
    }

    public function testRequiredFalse()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getTwoFactorSecret')->willReturn(null);

        $twoFactorHelper = new \pmill\Auth\TwoFactorAuthentication;
        $this->assertFalse($twoFactorHelper->required($user));
    }

    /**
     * @expectedException \pmill\Auth\Exceptions\TwoFactorAuthException
     */
    public function testIncorrectSecret()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $auth = new \pmill\Auth\Authenticate;
        $auth->login($user, $this->rawPassword, 'incorrect-secret');
    }

    public function testCorrectSecret()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $auth = new \pmill\Auth\Authenticate;
        $auth->login($user, $this->rawPassword, 'abcdef123456');

        $this->assertEquals(1, $auth->getLoggedInUserId());
    }

}