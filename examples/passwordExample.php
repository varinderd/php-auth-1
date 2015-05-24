<?php
require_once '../vendor/autoload.php';
require_once 'PasswordUser.php';

$output = [];

$user = new examples\PasswordUser();
$user->setId(1);
$user->setUsername('username');
$user->setPassword('hunter2');

$auth = new \pmill\Auth\Authenticate;
$auth->setMaxAttempts(3);

/**
 * Attempt login with correct password
 */
$auth->login($user, 'hunter2');
$output[] = 'successful login';
$auth->logout();

/**
 * Attempt login with incorrect password
 */
try {
    $auth->login($user, 'incorrect-password');
}
catch (\pmill\Auth\Exceptions\PasswordException $e) {
    $output[] = 'login failed, incorrect password';
}

/**
 * Expected output:
 * successful login
 * login failed, incorrect password
 */
print_r($output);