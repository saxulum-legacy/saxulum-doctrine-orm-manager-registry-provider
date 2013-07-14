<?php

namespace Dominikzogg\Doctrine\Registry;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\ORMException;

class ManagerRegistry implements ManagerRegistryInterface
{
    /**
     * @var \Pimple
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
     * @var ObjectManager[]
     */
    protected $managers;

    /**
     * @var string
     */
    protected $defaultManagerName;

    /**
     * @var string
     */
    protected $proxyInterfaceName;

    /**
     * @param \Pimple $container
     * @param string  $proxyInterfaceName
     */
    public function __construct(\Pimple $container, $proxyInterfaceName = 'Doctrine\ORM\Proxy\Proxy')
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

        if ($name === null) {
            $name = $this->getDefaultConnectionName();
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Connection named "%s" does not exist.', $name));
        }

        return $this->connections[$name];
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        $this->loadConnections();

        return $this->connections;
    }

    /**
     * @return array
     */
    public function getConnectionNames()
    {
        $this->loadConnections();

        return array_keys($this->connections);
    }

    protected function loadConnections()
    {
        if (is_null($this->connections)) {
            $this->connections = array();
            foreach ($this->container['dbs']->keys() as $name) {
                $this->connections[$name] = $this->container['dbs']->raw($name);
            }
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
     * @param  null                      $name
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    public function getManager($name = null)
    {
        $this->loadManagers();

        if ($name === null) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        return $this->managers[$name];
    }

    /**
     * @return array
     */
    public function getManagers()
    {
        $this->loadManagers();

        return $this->managers;
    }

    /**
     * @return array
     */
    public function getManagerNames()
    {
        $this->loadManagers();

        return array_keys($this->managers);
    }

    /**
     * @param  null                      $name
     * @return void
     * @throws \InvalidArgumentException
     */
    public function resetManager($name = null)
    {
        $this->loadManagers();

        if (null === $name) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        $this->managers[$name] = null;
    }

    protected function loadManagers()
    {
        if (is_null($this->managers)) {
            $this->managers = array();
            foreach ($this->container['orm.ems']->keys() as $name) {
                $this->managers[$name] = $this->container['orm.ems']->raw($name);
            }
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
            }
        }
        throw ORMException::unknownEntityNamespace($alias);
    }

    /**
     * @param  string           $persistentObject
     * @param  null             $persistentManagerName
     * @return ObjectRepository
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * @param  string             $class
     * @return ObjectManager|null
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

