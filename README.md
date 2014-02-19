saxulum/saxulum-doctrine-orm-manager-registry-provider
======================================================

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-doctrine-orm-manager-registry-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-doctrine-orm-manager-registry-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-doctrine-orm-manager-registry-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-orm-manager-registry-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-doctrine-orm-manager-registry-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-orm-manager-registry-provider)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/saxulum/saxulum-doctrine-orm-manager-registry-provider/badges/quality-score.png?s=cc4f9fc8723947e7e3e4c14f20d3b7cb20695a5a)](https://scrutinizer-ci.com/g/saxulum/saxulum-doctrine-orm-manager-registry-provider/)

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
 
Through [Composer](http://getcomposer.org) as [saxulum/saxulum-doctrine-orm-manager-registry-provider][6].

```{.php}
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;

$app->register(new DoctrineOrmManagerRegistryProvider());
```

### Form `Entity` Type

If you like to have `Entity` Type Support within [Symfony Form Component][5], install the [Doctrine Bridge][4] and register the form provider first.

```{.json}
{
    "require": {
        "symfony/doctrine-bridge": "~2.2",
        "symfony/form": "~2.2"
    }
}

```

```{.php}
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Silex\Provider\FormServiceProvider;

$app->register(new FormServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());
```

### Doctrine ORM Console

If you like to use [Doctrine ORM Console][7] commands, install [Doctrine ORM Console][7] and the [Saxulum Console Provider][8] and register the console provider.

```{.json}
"saxulum-doctrine-orm-commands": "~1.0.0",
"saxulum/saxulum-console": "~1.1.1",
```

```{.php}
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Saxulum\Console\Silex\Provider\ConsoleProvider;

$app->register(new ConsoleProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());
```

Usage
-----

```{.php}
<?php

// get the default connection name
$app['doctrine']->getDefaultConnectionName();

// get the default connection 
$app['doctrine']->getConnection();

// get a connection by name
$app['doctrine']->getConnection('name');

// all connections as array access (pimple)
$app['doctrine']->getConnections();

// all connection names as array
$app['doctrine']->getConnectionNames();

// get the default manager name
$app['doctrine']->getDefaultManagerName();

// get the default manager
$app['doctrine']->getManager();

// get a manager by name
$app['doctrine']->getManager('name');

// all manager as array access (pimple)
$app['doctrine']->getManagers();

// all manager names as array
$app['doctrine']->getManagerNames();
...
```

[1]: http://silex.sensiolabs.org/doc/providers/doctrine.html
[2]: https://github.com/Cilex/Cilex/blob/master/src/Cilex/Provider/DoctrineServiceProvider.php
[3]: https://github.com/dflydev/dflydev-doctrine-orm-service-provider
[4]: https://github.com/symfony/DoctrineBridge
[5]: https://github.com/symfony/Form
[6]: https://packagist.org/packages/saxulum/saxulum-doctrine-orm-manager-registry-provider
[7]: https://packagist.org/packages/saxulum/saxulum-doctrine-orm-commands
[8]: https://packagist.org/packages/saxulum/saxulum-console
