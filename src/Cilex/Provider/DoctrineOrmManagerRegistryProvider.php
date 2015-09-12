<?php

namespace Saxulum\DoctrineOrmManagerRegistry\Cilex\Provider;

use Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider as PimpleDoctrineOrmManagerRegistryProvider;
use Cilex\Application;
use Cilex\ServiceProviderInterface;

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
