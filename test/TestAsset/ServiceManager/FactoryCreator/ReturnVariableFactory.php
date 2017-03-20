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

class ReturnVariableFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return Foo
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $bar = $services->get(Bar::class);
        $foo = new Foo($bar);
        return $foo;
    }
}