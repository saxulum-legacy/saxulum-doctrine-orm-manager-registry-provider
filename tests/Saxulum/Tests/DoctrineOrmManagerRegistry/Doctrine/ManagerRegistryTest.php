<?php

namespace Saxulum\Tests\DoctrineOrmManagerRegistry\Doctrine;

use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider;

class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function createMockDefaultAppAndDeps()
    {
        $container = new Container();

        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container['dbs'] = new Container(array(
            'default' => $connection,
        ));

        $container['dbs.default'] = 'default';

        $configuration = $this->getMock('Doctrine\ORM\Configuration');

        $configuration
            ->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Saxulum\DoctrineOrmManagerRegistry\Doctrine\ManagerRegistry'))
        ;

        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $metadataFactory = $this->getMock('Doctrine\ORM\Mapping\ClassMetadataFactory');

        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $entityManager
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration))
        ;

        $entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository))
        ;

        $entityManager
            ->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory))
        ;

        $container['orm.ems'] = new Container(array(
            'default' => $entityManager,
        ));

        $container['orm.ems.default'] = 'default';

        return $container;
    }

    public function testRegisterDefaultImplementations()
    {
        $container = $this->createMockDefaultAppAndDeps();

        $doctrineOrmManagerRegistryProvider = new DoctrineOrmManagerRegistryProvider();
        $doctrineOrmManagerRegistryProvider->register($container);

        $this->assertEquals('default', $container['doctrine']->getDefaultConnectionName());
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $container['doctrine']->getConnection());
        $this->assertCount(1, $container['doctrine']->getConnections());
        $this->assertCount(1, $container['doctrine']->getConnectionNames());
        $this->assertEquals('default', $container['doctrine']->getDefaultManagerName());
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $container['doctrine']->getManager());
        $this->assertCount(1, $container['doctrine']->getManagers());
        $this->assertCount(1, $container['doctrine']->getManagerNames());
        $this->assertEquals($container['doctrine']->getAliasNamespace('Test'), 'Saxulum\DoctrineOrmManagerRegistry\Doctrine\ManagerRegistry');
        $this->assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $container['doctrine']->getRepository('Saxulum\DoctrineOrmManagerRegistry\Doctrine\ManagerRegistry'));
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $container['doctrine']->getManagerForClass('Saxulum\DoctrineOrmManagerRegistry\Doctrine\ManagerRegistry'));
    }
}
