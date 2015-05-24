<?php
require_once '../vendor/autoload.php';
require_once 'TwoAuthUser.php';

$output = [];

$user = new examples\TwoAuthUser();
$user->setId(1);
$user->setUsername('username');
$user->setPassword('hunter2');
$user->setTwoAuthSecret('abcdefg1234567');

$auth = new \pmill\Auth\Authenticate;
$auth->setMaxAttempts(3);

/**
 * Attempt login with correct password and 2fa secret
 */
$auth->login($user, 'hunter2', 'abcdefg1234567');
$output[] = 'successful login';
$auth->logout();

/**
 * Attempt login with incorrect password
 */
try {
    $auth->login($user, 'incorrect-password', 'abcdefg1234567');
}
catch (\pmill\Auth\Exceptions\PasswordException $e) {
    $output[] = 'login failed, incorrect password';
}

/**
 * Attempt login with incorrect 2fa secret
 */
try {
    $auth->login($user, 'hunter2', 'incorrect-secret');
}
catch (\pmill\Auth\Exceptions\TwoFactorAuthException $e) {
    $output[] = 'login failed, incorrect 2fa secret';
}

/**
 * Expected output:
 * successful login
 * login failed, incorrect password
 * login failed, incorrect 2fa secret
 */
print_r($output);