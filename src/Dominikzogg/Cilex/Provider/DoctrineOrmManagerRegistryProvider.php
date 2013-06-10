<?php

namespace Dominikzogg\Cilex\Provider;

use Dominikzogg\Pimple\Provider\DoctrineOrmManagerRegistryProvider as PimpleDoctrineOrmManagerRegistryProvider;
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
