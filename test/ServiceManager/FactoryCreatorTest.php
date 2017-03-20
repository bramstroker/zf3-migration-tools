<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 17-3-17
 * Time: 11:16
 */

namespace StrokerTest\Zf3MigrationTools\ServiceManager;

use DirectoryIterator;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Stroker\Zf3MigrationTools\Migration\ServiceManager\FactoryMigration;
use Zend\Code\Generator\FileGenerator;

class FactoryCreatorTest extends TestCase
{
    /**
     * @return array
     */
    public function provideCases(): array
    {
        $fixtureDirectory = __DIR__ . '/../TestAsset/ServiceManager/FactoryCreator/';
        $namespace = 'StrokerTest\\Zf3MigrationTools\\TestAsset\\ServiceManager\\FactoryCreator';
        $factories = [];
        foreach (new DirectoryIterator($fixtureDirectory) as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            $filename = $fileInfo->getFilename();

            if (!strstr($filename, 'Factory')) {
                continue;
            }

            $className = $namespace . '\\' . substr($filename, 0, strrpos($filename, '.'));
            $expectedOutput = file_get_contents($fixtureDirectory . 'Migrated/' . $filename);

            $factories[$className] = [
                $fileInfo->getPathname(),
                $expectedOutput
            ];
        }
        return $factories;
    }

    /**
     * @param string $filePath
     * @param string $expectedOutput
     * @dataProvider provideCases
     */
    public function testCanModifyExistingFactory(string $filePath, string $expectedOutput)
    {
        $fileGenerator = FileGenerator::fromReflectedFileName($filePath, true);
        $migration = new FactoryMigration(new NullLogger());
        $fileGenerator = $migration->migrate($fileGenerator, $filePath);
        $this->assertEquals($expectedOutput, $fileGenerator->generate());
    }
}