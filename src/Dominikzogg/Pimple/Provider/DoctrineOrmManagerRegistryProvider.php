<?php

namespace Dominikzogg\Pimple\Provider;

use Dominikzogg\Doctrine\Registry\ManagerRegistry;
use Saxulum\DoctrineOrmCommands\Command\CreateDatabaseDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\DropDatabaseDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\ClearMetadataCacheDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\ClearQueryCacheDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\ClearResultCacheDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\ConvertMappingDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\CreateSchemaDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\DropSchemaDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\EnsureProductionSettingsDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\InfoDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\RunDqlDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\RunSqlDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\UpdateSchemaDoctrineCommand;
use Saxulum\DoctrineOrmCommands\Command\Proxy\ValidateSchemaCommand;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

class DoctrineOrmManagerRegistryProvider
{
    public function register(\Pimple $container)
    {
        $container['doctrine'] = $container->share(function ($container) {
            return new ManagerRegistry($container);
        });

        if (isset($container['form.extensions']) && class_exists('Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmExtension')) {
            $container['form.extensions'] = $container->share(
                $container->extend('form.extensions', function ($extensions, $container) {
                    $extensions[] = new DoctrineOrmExtension($container['doctrine']);

                    return $extensions;
                })
            );
        }

        if (isset($container['console.commands']) && class_exists('Saxulum\\DoctrineOrmCommands\\Command\\CreateDatabaseDoctrineCommand')) {
            $container['console.commands'] = $container->share(
                $container->extend('console.commands', function ($commands) use ($container) {
                    $commands[] = new CreateDatabaseDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new DropDatabaseDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new CreateSchemaDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new UpdateSchemaDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new DropSchemaDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new RunDqlDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new RunSqlDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new ConvertMappingDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new ClearMetadataCacheDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new ClearQueryCacheDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new ClearResultCacheDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new InfoDoctrineCommand(null, $container['doctrine']);
                    $commands[] = new ValidateSchemaCommand(null, $container['doctrine']);
                    $commands[] = new EnsureProductionSettingsDoctrineCommand(null, $container['doctrine']);

                    return $commands;
                })
            );
        }
    }
}
