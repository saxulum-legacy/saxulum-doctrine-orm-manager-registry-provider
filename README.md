doctrine-orm-manager-registry-provider
======================================

[![Build Status](https://api.travis-ci.org/dominikzogg/doctrine-orm-manager-registry-provider.png?branch=master)](https://travis-ci.org/dominikzogg/doctrine-orm-manager-registry-provider)

Features
--------

 * Leverages the core [Doctrine Service Provider][1] for either Silex or Cilex and the [Doctrine ORM Service Provider][3]
 * The Registry manager registry can the used with the [Doctrine Bridge][4] from symfony, to use entity type in the [Symfony Form Component][5] 

Requirements
------------

 * PHP 5.3+
 * Doctrine ~2.3
 
Currently requires both **dbs** and **orm.ems** services in order to work.
These can be provided by a Doctrine Service Provider like the [Silex][1] or [Cilex][2] and the Doctrine ORM Serice Provider like the [dflydev-doctrine-orm-service-provider][3] service providers.
If you can or want to fake it, go for it. :)

Installation
------------
 
Through [Composer](http://getcomposer.org) as [dominikzogg/doctrine-orm-manager-registry-provider][6].

```php

$app->register(new Dominikzogg\Silex\Provider\DoctrineOrmManagerRegistryProvider());

// register the form extension of the doctrine bridge
$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions, $app) {
    $extensions[] = new Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($app['doctrine']);

    return $extensions;
}));
```

Usage
-----

```php
<?php

// Default entity manager.
$app['doctrine']->getManager()
```

[1]: http://silex.sensiolabs.org/doc/providers/doctrine.html
[2]: https://github.com/Cilex/Cilex/blob/master/src/Cilex/Provider/DoctrineServiceProvider.php
[3]: https://raw.github.com/dflydev/dflydev-doctrine-orm-service-provider
[4]: https://github.com/symfony/DoctrineBridge
[5]: https://github.com/symfony/Form
[6]: https://packagist.org/packages/dominikzogg/doctrine-orm-manager-registry-provider
