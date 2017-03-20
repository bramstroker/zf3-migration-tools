<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 13:05
 */

namespace Stroker\Zf3MigrationTools\Command;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use Stroker\Zf3MigrationTools\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('migrate')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger('logger');
        $logger->pushHandler(new StreamHandler('php://stdout'));

        $iterator = new RecursiveIteratorIterator(
            new RecursiveRegexIterator(
                new RecursiveDirectoryIterator(
                    '/var/www/eurocampings/vendor/acsi/acsi-assetic',
                    RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                ),
                // match both php file extensions and directories
                '#(?<!/)\.php$#i'
            ),
            true
        );

        $runner = new Runner($logger, $iterator);
        $runner->runMigration();
    }
}