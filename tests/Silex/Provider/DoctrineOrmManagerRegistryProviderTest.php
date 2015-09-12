<?php

namespace Saxulum\Tests\DoctrineOrmManagerRegistry\Silex\Provider;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Saxulum\Tests\DoctrineOrmManagerRegistry\Entity\SampleEntity;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\WebTestCase;
use Symfony\Component\Validator\Validator;

class DoctrineOrmManagerRegistryProviderTest extends WebTestCase
{
    public function testSchema()
    {
        $app = $this->createApplication();

        /** @var EntityManager $em */
        $em = $app['doctrine']->getManager();

        $schemaTool = $this->getSchemaTool($em);
        $metadatas = $this->getMetadatas($em);

        $this->createSchema($schemaTool, $metadatas);
        $this->dropSchema($schemaTool, $metadatas);
    }

    public function testValidator()
    {
        $app = $this->createApplication();

        /** @var EntityManager $em */
        $em = $app['doctrine']->getManager();

        /** @var Validator $validator */
        $validator = $app['validator'];

        $schemaTool = $this->getSchemaTool($em);
        $metadatas = $this->getMetadatas($em);

        $this->createSchema($schemaTool, $metadatas);

        $sampleEntity = new SampleEntity();
        $sampleEntity->setName('name');

        $errors = $validator->validate($sampleEntity);

        $this->assertCount(0, $errors);

        $em->persist($sampleEntity);
        $em->flush();

        $sampleEntity = new SampleEntity();
        $sampleEntity->setName('name');

        $errors = $validator->validate($sampleEntity);

        $this->assertCount(1, $errors);

        $this->dropSchema($schemaTool, $metadatas);
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;

        $app->register(new ValidatorServiceProvider());
        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => $this->getCacheDir() . '/app.db',
            ),
        ));
        $app->register(new DoctrineOrmServiceProvider(), array(
            'orm.proxies_dir' => $this->getCacheDir() . '/doctrine/proxies',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'annotation',
                        'namespace' => 'Saxulum\Tests\DoctrineOrmManagerRegistry\Entity',
                        'path' => __DIR__.'/../../Entity',
                        'use_simple_annotation_reader' => false,
                    )
                )
            )
        ));
        $app->register(new DoctrineOrmManagerRegistryProvider());

        return $app;
    }

    /**
     * @param SchemaTool $schemaTool
     * @param $metadatas
     */
    protected function createSchema(SchemaTool $schemaTool, $metadatas)
    {
        $schemaTool->createSchema($metadatas);
    }

    /**
     * @param SchemaTool $schemaTool
     * @param $metadatas
     */
    protected function dropSchema(SchemaTool $schemaTool, $metadatas)
    {
        $schemaTool->dropSchema($metadatas);
    }

    /**
     * @param  EntityManager $em
     * @return SchemaTool
     */
    protected function getSchemaTool(EntityManager $em)
    {
        return new SchemaTool($em);
    }

    /**
     * @param  EntityManager $em
     * @return array
     */
    protected function getMetadatas(EntityManager $em)
    {
        return $em->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        $cacheDir =  __DIR__ . '/../../../cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return $cacheDir;
    }
}
