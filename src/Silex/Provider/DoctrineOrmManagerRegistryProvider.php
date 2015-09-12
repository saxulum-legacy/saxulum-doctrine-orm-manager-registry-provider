<?php

namespace Saxulum\DoctrineOrmManagerRegistry\Silex\Provider;

use Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider as PimpleDoctrineOrmManagerRegistryProvider;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DoctrineOrmManagerRegistryProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $pimpleServiceProvider = new PimpleDoctrineOrmManagerRegistryProvider;
        $pimpleServiceProvider->register($app);
    }
}
