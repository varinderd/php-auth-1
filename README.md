php-auth 
========

[![Build Status](https://secure.travis-ci.org/pmill/php-auth.svg?branch=master)](http://travis-ci.org/pmill/php-auth) [![Code Climate](https://codeclimate.com/github/pmill/php-auth/badges/gpa.svg)](https://codeclimate.com/github/pmill/php-auth) [![Test Coverage](https://codeclimate.com/github/pmill/php-auth/badges/coverage.svg)](https://codeclimate.com/github/pmill/php-auth/coverage) [![Test Coverage](https://scrutinizer-ci.com/g/pmill/php-auth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pmill/php-auth/)

Introduction
------------

This package contains a simple framework agnostic PHP authentication library.


Installation
------------

### Installing via Composer

The recommended way to install php-auth is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest version of php-auth:

```bash
composer.phar require pmill/php-auth
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```


Usage
-----

Implement the AuthUser interface on your existing user class. Make sure when you set the user's password, you hash it first.

    class User implements \pmill\Auth\Interfaces\AuthUser
    {
        public function setPassword($password)
        {
            $passwordHelper = new \pmill\Auth\Password;
            $this->password = $passwordHelper->hash($password);
        }
        
        /**
         * The rest of your user class
         */
    }

Create your user instance

    $user = new User();
    $user->setId(1);
    $user->setUsername('username');
    $user->setPassword('password');

Attempt the login
     
    $auth = new \pmill\Auth\Authenticate;

    try {
        $auth->login($user, 'password');
        echo 'login succeeded';
    }
    catch(\pmill\Auth\Exceptions\PasswordException $e) {
        echo 'login failed, incorrect password';
    }


Version History
---------------

0.2.3 (30/05/2015)

*   Added customisable session key

0.2.2 (25/05/2015)

*   Separated out the two factor authentication code in Authentication.php into a separate injectable class

0.2.1 (24/05/2015)

*   Fixed a bug where we were coding against the implementation of PasswordHelper rather than the interface

0.2.0 (23/05/2015)

*   Separated Auth class into Authentication and Password

0.1.1 (23/05/2015)

*   Added unit tests

0.1.0 (22/05/2015)

*   First public release of php-auth


Copyright
---------

php-auth
Copyright (c) 2015 pmill (dev.pmill@gmail.com)
All rights reserved.
