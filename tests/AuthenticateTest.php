<?php

class AuthenticateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $rawPassword = 'uly5qHYeMI4csC';

    /**
     * @var string
     */
    protected $correctHashedPassword = '$2y$10$dYXhwn/UUEPR3h5EymE5YeSpkieq07QdZN9D0csqiRG3DwxupANWO';

    /**
     * @expectedException \pmill\Auth\Exceptions\PasswordException
     */
    public function testIncorrectPassword()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn('incorrect-password');

        $auth = new \pmill\Auth\Authenticate;
        $auth->login($user, 'incorrect-password');
    }

    public function testCorrectPassword()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Authenticate;
        $auth->login($user, $this->rawPassword);

        $this->assertEquals(1, $auth->getLoggedInUserId());
    }

    public function testLoginUser()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);

        $auth = new \pmill\Auth\Authenticate;
        $auth->loginUser($user);

        $this->assertEquals(1, $auth->getLoggedInUserId());
    }

    public function testLogout()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Authenticate;
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

        $auth = new \pmill\Auth\Authenticate;
        $this->assertFalse($auth->isLoggedIn());

        $auth->login($user, $this->rawPassword);
        $this->assertTrue($auth->isLoggedIn());
    }

    public function testResetLoginAttempts()
    {
        $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
        $user->method('getAuthId')->willReturn(1);
        $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

        $auth = new \pmill\Auth\Authenticate;

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

        $auth = new \pmill\Auth\Authenticate;
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

    public function testSetPasswordHelper()
    {
        $savedHash = 'savedHash';

        $passwordHelperStub = $this->getMock('\pmill\Auth\Interfaces\PasswordHelper');
        $passwordHelperStub->method('hash')->willReturn($savedHash);
        $passwordHelperStub->method('verify')->willReturn(true);

        $auth = new \pmill\Auth\Authenticate;
        $auth->setPasswordHelper($passwordHelperStub);

        $this->assertInstanceOf('\pmill\Auth\Interfaces\PasswordHelper', $auth->getPasswordHelper());
        $this->assertEquals($savedHash, $passwordHelperStub->hash('test'));
        $this->assertEquals(true, $passwordHelperStub->verify('test', 'value'));
    }

    public function testSessionKey()
    {
        $authInstances = array();

        for ($i = 1; $i <= 2; $i++) {
            $user = $this->getMockBuilder('\pmill\Auth\Interfaces\AuthUser')->getMock();
            $user->method('getAuthId')->willReturn($i);
            $user->method('getAuthPassword')->willReturn($this->correctHashedPassword);

            $auth = new \pmill\Auth\Authenticate;
            $auth->setSessionKey('testSessionKey' . $i);
            $auth->login($user, $this->rawPassword);
            $authInstances[$i] = $auth;
        }

        $this->assertEquals('testSessionKey1', $authInstances[1]->getSessionKey());
        $this->assertEquals('testSessionKey2', $authInstances[2]->getSessionKey());
        $this->assertEquals(1, $authInstances[1]->getLoggedInUserId());
        $this->assertEquals(2, $authInstances[2]->getLoggedInUserId());
    }

    public function testSetTwoFactorAuthHelper()
    {
        $twoFactorHelperStub = $this->getMock('\pmill\Auth\Interfaces\TwoFactorAuthenticationHelper');
        $twoFactorHelperStub->method('required')->willReturn(true);
        $twoFactorHelperStub->method('verify')->willReturn(true);

        $auth = new \pmill\Auth\Authenticate;
        $auth->setTwoFactorAuthHelper($twoFactorHelperStub);

        $this->assertInstanceOf('\pmill\Auth\Interfaces\TwoFactorAuthenticationHelper', $auth->getTwoFactorAuthHelper());
    }

}