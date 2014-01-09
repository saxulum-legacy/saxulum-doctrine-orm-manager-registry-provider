<?php

namespace Dominikzogg\Pimple\Provider;

use Dominikzogg\Doctrine\Registry\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

class DoctrineOrmManagerRegistryProvider
{
    public function register(\Pimple $container)
    {
        $container['doctrine'] = $container->share(function ($container) {
            return new ManagerRegistry($container);
        });

        if (class_exists('Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmExtension')) {
            $container['form.extensions'] = $container->share(
                $container->extend('form.extensions', function ($extensions, $app) {
                    $extensions[] = new DoctrineOrmExtension($app['doctrine']);

                    return $extensions;
                })
            );
        }
    }
}
