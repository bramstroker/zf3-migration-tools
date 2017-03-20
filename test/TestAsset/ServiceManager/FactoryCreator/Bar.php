<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 8:23
 */

namespace StrokerTest\Zf3MigrationTools\TestAsset\ServiceManager\FactoryCreator;


class Bar
{
    /**
     * @var Foo
     */
    private $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}