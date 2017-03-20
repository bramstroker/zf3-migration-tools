<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 11:53
 */

namespace Stroker\Zf3MigrationTools\Migration;


use Zend\Code\Generator\FileGenerator;

interface MigrationInterface
{
    /**
     * @param FileGenerator $fileGenerator
     * @param string $filePath
     * @return FileGenerator
     */
    public function migrate(FileGenerator $fileGenerator, string $filePath): FileGenerator;

    /**
     * @param FileGenerator $fileGenerator
     * @return boolean
     */
    public function supports(FileGenerator $fileGenerator): bool;

    /**
     * @return string
     */
    public function getName(): string;
}