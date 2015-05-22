php-auth
========

Introduction
------------

This package contains a simple PHP authentication library.


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
            $auth = new \pmill\Auth\Auth;
            $this->password = $auth->hashPassword($password);
        }
        
        /**
         * The rest of your user class
         */
    }

Create a new Auth instance

    $user = new PasswordUser();
    $user->setId(1);
    $user->setUsername('username');
    $user->setPassword('password');
     
    $auth = new \pmill\Auth\Auth;

    try {
        $auth->login($user, 'password');
    }
    catch(\pmill\Auth\Exceptions\PasswordException $e) {
        echo 'login failed, incorrect password';
    }


Version History
---------------

0.1.0 (22/05/2015)

*   First public release of php-auth


Copyright
---------

php-auth
Copyright (c) 2015 pmill (dev.pmill@gmail.com)
All rights reserved.
