<?php
namespace pmill\Auth;

use Aura\Session\Segment;
use pmill\Auth\Exceptions\PasswordException;
use pmill\Auth\Exceptions\TwoFactorAuthException;
use pmill\Auth\Interfaces\AuthUser;

class Authenticate
{

    /**
     * @var int
     */
    protected $loggedInUserId;

    /**
     * @var int
     */
    protected $maxAttempts;

    /**
     * @var Segment
     */
    protected $session;

    /**
     * @var string
     */
    protected $sessionKey = 'pmill\Auth';

    /**
     * @var bool
     */
    protected $loginLimitReached = false;

    /**
     * @var Interfaces\PasswordHelper
     */
    protected $passwordHelper;

    /**
     * @var int
     */
    protected $loginAttempts = 0;

    public function __construct()
    {
        $sessionFactory = new \Aura\Session\SessionFactory;
        $session = $sessionFactory->newInstance($_COOKIE);
        $this->session = $session->getSegment($this->sessionKey);

        $this->passwordHelper = new Password;

        $this->loggedInUserId = $this->session->get('loggedInUserId');

        $this->loginAttempts = $this->session->get('loginAttempts');
        $this->checkLoginAttempts();
    }

    /**
     * @param AuthUser $userToAuthenticate
     * @param string $submittedPassword
     * @param string|null $submittedTwoFactorSecret
     * @throws PasswordException
     * @throws TwoFactorAuthException
     */
    public function login(AuthUser $userToAuthenticate, $submittedPassword, $submittedTwoFactorSecret = null)
    {
        $this->checkLoginAttempts();
        $this->increaseLoginAttempts();

        if (!$this->passwordHelper->verify($userToAuthenticate->getAuthPassword(), $submittedPassword)) {
            throw new PasswordException('The supplied password is incorrect for the user {' . $userToAuthenticate->getAuthUsername() . '}');
        }

        if ($this->isTwoFactorAuthenticationRequired($userToAuthenticate)) {
            if (is_null($submittedTwoFactorSecret) || !$this->verifyTwoFactorAuth($userToAuthenticate->getTwoFactorSecret(), $submittedTwoFactorSecret)) {
                throw new TwoFactorAuthException('The supplied 2fa secret is incorrect for the user {' . $userToAuthenticate->getAuthUsername() . '}');
            }
        }

        $this->loginUser($userToAuthenticate);
    }

    /**
     * @param AuthUser $userToAuthenticate
     */
    public function loginUser(AuthUser $userToAuthenticate)
    {
        $this->loggedInUserId = $userToAuthenticate->getAuthId();
        $this->session->set('loggedInUserId', $this->loggedInUserId);
    }

    public function resetLoginAttempts()
    {
        $this->loginAttempts = 0;
        $this->session->set('loginAttempts', $this->loginAttempts);
    }

    protected function checkLoginAttempts()
    {
        if (!is_null($this->maxAttempts) && $this->loginAttempts >= $this->maxAttempts) {
            $this->loginLimitReached = true;
        }
    }

    protected function increaseLoginAttempts()
    {
        $this->loginAttempts++;
        $this->session->set('loginAttempts', $this->loginAttempts);
    }

    /**
     * @param string $savedSecret
     * @param string $submittedSecret
     * @return bool
     */
    public function verifyTwoFactorAuth($savedSecret, $submittedSecret)
    {
        return $savedSecret == $submittedSecret;
    }

    public function logout()
    {
        $this->session->set('loggedInUserId', null);
        $this->loggedInUserId = null;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return !is_null($this->loggedInUserId);
    }

    /**
     * @param AuthUser $userToAuthenticate
     * @return bool
     */
    protected function isTwoFactorAuthenticationRequired(AuthUser $userToAuthenticate)
    {
        return !is_null($userToAuthenticate->getTwoFactorSecret());
    }

    /**
     * @return int
     */
    public function getLoggedInUserId()
    {
        return $this->loggedInUserId;
    }

    /**
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * @param int $maxAttempts
     */
    public function setMaxAttempts($maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * @return boolean
     */
    public function isLoginLimitReached()
    {
        return $this->loginLimitReached;
    }

    /**
     * @param boolean $loginLimitReached
     */
    public function setLoginLimitReached($loginLimitReached)
    {
        $this->loginLimitReached = $loginLimitReached;
    }

    /**
     * @return int
     */
    public function getLoginAttempts()
    {
        return $this->loginAttempts;
    }

    /**
     * @param int $loginAttempts
     */
    public function setLoginAttempts($loginAttempts)
    {
        $this->loginAttempts = $loginAttempts;
    }

    /**
     * @return Interfaces\PasswordHelper
     */
    public function getPasswordHelper()
    {
        return $this->passwordHelper;
    }

    /**
     * @param Interfaces\PasswordHelper $passwordHelper
     */
    public function setPasswordHelper(Interfaces\PasswordHelper $passwordHelper)
    {
        $this->passwordHelper = $passwordHelper;
    }

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * @param string $sessionKey
     */
    public function setSessionKey($sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }

}