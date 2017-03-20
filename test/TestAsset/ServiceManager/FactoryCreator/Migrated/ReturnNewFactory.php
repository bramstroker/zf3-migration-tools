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

class ReturnNewFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $services
     * @return Foo
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $services)
    {
        return $this($services, Foo::class);
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return Foo
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $bar = $container->get(Bar::class);
        return new Foo($bar);
    }


}

