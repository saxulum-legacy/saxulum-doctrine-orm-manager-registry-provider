<?php

namespace Saxulum\DoctrineOrmManagerRegistry\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Pimple\Container;

class ManagerRegistry implements ManagerRegistryInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Connection[]
     */
    protected $connections;

    /**
     * @var string
     */
    protected $defaultConnectionName;

    /**
     * @var EntityManager[]
     */
    protected $originalManagers;

    /**
     * @var EntityManager[]
     */
    protected $resetManagers = [];

    /**
     * @var string
     */
    protected $defaultManagerName;

    /**
     * @var string
     */
    protected $proxyInterfaceName;

    /**
     * @param Container $container
     * @param string    $proxyInterfaceName
     */
    public function __construct(Container $container, $proxyInterfaceName = 'Doctrine\ORM\Proxy\Proxy')
    {
        $this->container = $container;
        $this->proxyInterfaceName = $proxyInterfaceName;
    }

    /**
     * @return string
     */
    public function getDefaultConnectionName()
    {
        $this->loadConnections();

        return $this->defaultConnectionName;
    }

    /**
     * @param  string|null               $name
     * @return Connection
     * @throws \InvalidArgumentException
     */
    public function getConnection($name = null)
    {
        $this->loadConnections();

        $name = $this->validateName(
            $this->connections,
            $name,
            $this->getDefaultConnectionName())
        ;

        return $this->connections[$name];
    }

    /**
     * @return Connection[]
     */
    public function getConnections()
    {
        $this->loadConnections();

        if ($this->connections instanceof Container) {
            $connections = array();
            foreach ($this->getConnectionNames() as $name) {
                $connections[$name] = $this->connections[$name];
            }
            $this->connections = $connections;
        }

        return $this->connections;
    }

    /**
     * @return array
     */
    public function getConnectionNames()
    {
        $this->loadConnections();

        if ($this->connections instanceof Container) {
            return $this->connections->keys();
        } else {
            return array_keys($this->connections);
        }
    }

    protected function loadConnections()
    {
        if (is_null($this->connections)) {
            $this->connections = $this->container['dbs'];
            $this->defaultConnectionName = $this->container['dbs.default'];
        }
    }

    /**
     * @return string
     */
    public function getDefaultManagerName()
    {
        $this->loadManagers();

        return $this->defaultManagerName;
    }

    /**
     * @param  string|null   $name
     * @return EntityManager
     */
    public function getManager($name = null)
    {
        $this->loadManagers();
        $name = $this->validateManagerName($name);

        return isset($this->resetManagers[$name]) ? $this->resetManagers[$name] : $this->originalManagers[$name];
    }

    /**
     * @param  string|null $name
     * @return string
     */
    protected function validateManagerName($name)
    {
        return $this->validateName(
            $this->originalManagers,
            $name,
            $this->getDefaultManagerName())
        ;
    }

    /**
     * @param  array                     $data
     * @param  string                    $default
     * @param  string|null               $name
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function validateName($data, $name, $default)
    {
        if ($name === null) {
            $name = $default;
        }

        if (!isset($data[$name])) {
            throw new \InvalidArgumentException(sprintf('Element named "%s" does not exist.', $name));
        }

        return $name;
    }

    /**
     * @return EntityManager[]
     */
    public function getManagers()
    {
        $this->loadManagers();

        if ($this->originalManagers instanceof Container) {
            $managers = array();
            foreach ($this->getManagerNames() as $name) {
                $managers[$name] = $this->originalManagers[$name];
            }
            $this->originalManagers = $managers;
        }

        return array_replace($this->originalManagers, $this->resetManagers);
    }

    /**
     * @return array
     */
    public function getManagerNames()
    {
        $this->loadManagers();

        if ($this->originalManagers instanceof Container) {
            return $this->originalManagers->keys();
        } else {
            return array_keys($this->originalManagers);
        }
    }

    /**
     * @param  string|null               $name
     * @return void
     */
    public function resetManager($name = null)
    {
        $this->loadManagers();
        $name = $this->validateManagerName($name);

        $this->resetManagers[$name] = $this->container['orm.ems.factory'][$name]();
    }

    protected function loadManagers()
    {
        if (is_null($this->originalManagers)) {
            $this->originalManagers = $this->container['orm.ems'];
            $this->defaultManagerName = $this->container['orm.ems.default'];
        }
    }

    /**
     * @param  string       $alias
     * @return string
     * @throws ORMException
     */
    public function getAliasNamespace($alias)
    {
        foreach ($this->getManagerNames() as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
                // throw the exception only if no manager can solve it
            }
        }
        throw ORMException::unknownEntityNamespace($alias);
    }

    /**
     * @param  string           $persistentObject
     * @param  null             $persistentManagerName
     * @return EntityRepository
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * @param  string             $class
     * @return EntityManager|null
     */
    public function getManagerForClass($class)
    {
        $proxyClass = new \ReflectionClass($class);
        if ($proxyClass->implementsInterface($this->proxyInterfaceName)) {
            $class = $proxyClass->getParentClass()->getName();
        }

        foreach ($this->getManagerNames() as $managerName) {
            if (!$this->getManager($managerName)->getMetadataFactory()->isTransient($class)) {
                return $this->getManager($managerName);
            }
        }
    }
}
