<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 13:35
 */

namespace Stroker\Zf3MigrationTools;


use Iterator;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Stroker\Zf3MigrationTools\Migration\MigrationInterface;
use Throwable;
use Zend\Code\Generator\FileGenerator;

class Runner
{
    /** @var LoggerInterface */
    private $logger;

    /** @var MigrationInterface[] */
    private $migrators = [];

    /** @var Iterator */
    private $fileIterator;

    /**
     * Migrator constructor.
     * @param LoggerInterface $logger
     * @param Iterator $fileIterator
     */
    public function __construct(LoggerInterface $logger, Iterator $fileIterator)
    {
        $this->logger = $logger;
        $this->fileIterator = $fileIterator;
    }

    /**
     * @param MigrationInterface $migrator
     * @return self
     */
    public function addMigrator(MigrationInterface $migrator): Runner
    {
        $this->migrators[] = $migrator;
        return $this;
    }

    /**
     * Run migration
     */
    public function runMigration()
    {
        /** @var SplFileInfo $file */
        foreach ($this->fileIterator as $file) {
            try {
                $fileGenerator = FileGenerator::fromReflectedFileName($file->getPathname(), true);
            } catch(Throwable $ex) {
                echo 'ex';
            }
            $this->logger->info(sprintf('Handling file %s'), $file->getPathname());
            foreach ($this->migrators as $migrator) {
                $this->logger->info('Running migration ' . $migrator->getName());
                $output = $migrator->migrate($fileGenerator, $file->getPathname());
                $this->writeFile($file->getRealPath(), $output);
            }
        }
    }

    /**
     * @param string $file
     * @param string $output
     */
    protected function writeFile(string $file, string $output)
    {
        $bakFile = $file . '.bak';
        rename($file, $bakFile);
        file_put_contents($file, $output);
    }
}