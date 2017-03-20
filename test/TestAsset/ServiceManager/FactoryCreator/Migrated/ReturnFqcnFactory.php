<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 17-3-17
 * Time: 11:23
 */


namespace StrokerTest\Zf3MigrationTools\TestAsset\ServiceManager\FactoryCreator;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ReturnFqcnFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $services
     * @return Foo
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $services)
    {
        return $this($services, \StrokerTest\Zf3MigrationTools\TestAsset\ServiceManager\FactoryCreator\Foo::class);
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return Foo
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $bar = $container->get(\StrokerTest\Zf3MigrationTools\TestAsset\ServiceManager\FactoryCreator\Bar::class);
        return new \StrokerTest\Zf3MigrationTools\TestAsset\ServiceManager\FactoryCreator\Foo($bar);
    }


}

