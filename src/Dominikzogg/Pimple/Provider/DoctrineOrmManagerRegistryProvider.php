<?php

namespace Dominikzogg\Pimple\Provider;

use Dominikzogg\Doctrine\Registry\ManagerRegistry;

class DoctrineOrmManagerRegistryProvider
{
    public function register(\Pimple $container)
    {
        $container['doctrine'] = $container->share(function($container) {
            return new ManagerRegistry($container);
        });
    }
}
