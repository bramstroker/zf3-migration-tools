<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 13:51
 */

namespace Stroker\Zf3MigrationTools\Command;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Stroker\Zf3MigrationTools\Runner;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MigrateCommandFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $logger = new Logger('logger');
        $logger->pushHandler(new StreamHandler('php://stdout'));

        $runner = new Runner($logger);

        return new MigrateCommand($runner);
    }
}