<?php

class AuthTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $rawPassword = 'uly5qHYeMI4csC';

    /**
     * @var string
     */
    protected $correctHashedPassword = '$2y$10$dYXhwn/UUEPR3h5EymE5YeSpkieq07QdZN9D0csqiRG3DwxupANWO';

    public function testHashPassword()
    {
        $auth = new \pmill\Auth\Auth;
        $hashResult = $auth->hashPassword($this->rawPassword);

        $verificationResult = password_verify($this->rawPassword, $hashResult);
        $this->assertTrue($verificationResult);
    }

    public function testVerifyPasswordCorrect()
    {
        $passwordToTest = 'hunter2';

        $auth = new \pmill\Auth\Auth;
        $verifyPasswordResult = $auth->verifyPassword(password_hash($passwordToTest, PASSWORD_BCRYPT), $passwordToTest);

        $this->assertTrue($verifyPasswordResult);
    }

    public function testVerifyPasswordIncorrect()
    {
        $auth = new \pmill\Auth\Auth;
        $verifyPasswordResult = $auth->verifyPassword($auth->hashPassword('hunter2'), 'incorrect-password');

        $this->assertFalse($verifyPasswordResult);
    }

    /**
     * @expectedException \pmill\Auth\Exceptions\PasswordException
     */
    public function testIncorrectPassword()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn('incorrect-password');

        $auth = new \pmill\Auth\Auth;
        $auth->login($user, 'incorrect-password');
    }

    public function testCorrectPassword()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Auth;
        $auth->login($user, $this->rawPassword);

        $this->assertEquals(1, $auth->getLoggedInUserId());
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

        $auth = new \pmill\Auth\Auth;
        $auth->login($user, $this->rawPassword, 'incorrect-secret');
    }

    public function testCorrectSecret()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $auth = new \pmill\Auth\Auth;
        $auth->login($user, $this->rawPassword, 'abcdef123456');

        $this->assertEquals(1, $auth->getLoggedInUserId());
    }

    public function testLoginUser()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);

        $auth = new \pmill\Auth\Auth;
        $auth->loginUser($user);

        $this->assertEquals(1, $auth->getLoggedInUserId());
    }

    public function testVerifyTwoFactorAuth()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);
        $user->method('getTwoFactorSecret')->willReturn('abcdef123456');

        $auth = new \pmill\Auth\Auth;
        $this->assertTrue($auth->verifyTwoFactorAuth($user->getTwoFactorSecret(), 'abcdef123456'));
    }

    public function testLogout()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Auth;
        $auth->login($user, $this->rawPassword);

        $this->assertEquals(1, $auth->getLoggedInUserId());
        $auth->logout();

        $this->assertEquals(null, $auth->getLoggedInUserId());
    }

    public function testIsLoggedIn()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Auth;
        $this->assertFalse($auth->isLoggedIn());

        $auth->login($user, $this->rawPassword);
        $this->assertTrue($auth->isLoggedIn());
    }

    public function testResetLoginAttempts()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Auth;

        for ($i=1; $i<=10; $i++) {
            try {
                $auth->login($user, 'incorrect-password');
            }
            catch(\pmill\Auth\Exceptions\PasswordException $e) {

            }
        }

        $this->assertNotEquals(0, $auth->getLoginAttempts());
        $auth->resetLoginAttempts();
        $this->assertEquals(0, $auth->getLoginAttempts());
    }

    public function testIsLoginLimitReached()
    {
        $maxLoginAttempts = 3;

        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Auth;
        $auth->setMaxAttempts($maxLoginAttempts);
        $auth->resetLoginAttempts();

        for ($i=1; $i<=$maxLoginAttempts+1; $i++) {
            try {
                $auth->login($user, 'incorrect-password');
            }
            catch(\pmill\Auth\Exceptions\PasswordException $e) {
                if ($i <= $maxLoginAttempts) {
                    $this->assertEquals($i, $auth->getLoginAttempts());
                    $this->assertFalse($auth->isLoginLimitReached());
                }
                else {
                    $this->assertEquals($maxLoginAttempts+1, $auth->getLoginAttempts());
                    $this->assertTrue($auth->isLoginLimitReached());
                }
            }
        }
    }

}