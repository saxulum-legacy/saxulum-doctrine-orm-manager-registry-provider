<?php

namespace Dominikzogg\Pimple\Doctrine\Registry;

use Doctrine\ORM\EntityManager;
use Dominikzogg\Pimple\Provider\DoctrineOrmManagerRegistryProvider;

class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function createMockDefaultAppAndDeps()
    {
        $app = new \Pimple;

        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $app['dbs'] = new \Pimple(array(
            'default' => $connection,
        ));

        $app['dbs.default'] = 'default';

        $configuration = $this->getMock('Doctrine\ORM\Configuration');

        $configuration
            ->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Dominikzogg\Pimple\Doctrine\Registry\ManagerRegistryTest'))
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

        $app['orm.ems'] = new \Pimple(array(
            'default' => $entityManager,
        ));

        $app['orm.ems.default'] = 'default';

        return $app;
    }

    public function testRegisterDefaultImplementations()
    {
        $app = $this->createMockDefaultAppAndDeps();

        $doctrineOrmManagerRegistryProvider = new DoctrineOrmManagerRegistryProvider();
        $doctrineOrmManagerRegistryProvider->register($app);

        $this->assertEquals('default', $app['doctrine']->getDefaultConnectionName());
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $app['doctrine']->getConnection());
        $this->assertCount(1, $app['doctrine']->getConnections());
        $this->assertCount(1, $app['doctrine']->getConnectionNames());
        $this->assertEquals('default', $app['doctrine']->getDefaultManagerName());
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $app['doctrine']->getManager());
        $this->assertCount(1, $app['doctrine']->getManagers());
        $this->assertCount(1, $app['doctrine']->getManagerNames());
        $this->assertEquals($app['doctrine']->getAliasNamespace('Test'), 'Dominikzogg\Pimple\Doctrine\Registry\ManagerRegistryTest');
        $this->assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $app['doctrine']->getRepository('Dominikzogg\Pimple\Doctrine\Registry\ManagerRegistryTest'));
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $app['doctrine']->getManagerForClass('Dominikzogg\Pimple\Doctrine\Registry\ManagerRegistryTest'));
    }
}

