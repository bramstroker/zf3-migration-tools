<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 17-3-17
 * Time: 10:55
 */

namespace Stroker\Zf3MigrationTools\Migration\ServiceManager;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use Stroker\Zf3MigrationTools\Exception\UnexpectedFactoryFormatException;
use Stroker\Zf3MigrationTools\Migration\MigrationInterface;
use Stroker\Zf3MigrationTools\Parser\NodeVisitor\ReturnTypeVisitor;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

class FactoryMigration implements MigrationInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FactoryCreator constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function migrate(FileGenerator $fileGenerator, string $filePath): FileGenerator
    {
        $this->validateFileStructure($fileGenerator);

        $classGenerator = $fileGenerator->getClass();

        if (in_array('Interop\\Container\\ContainerInterface', $classGenerator->getImplementedInterfaces())) {
            $this->logger->info('File already migrated');
            return $fileGenerator;
        }

        $classGenerator->addUse('Interop\\Container\\ContainerInterface');

        $this->createInvokeMethod($classGenerator);
        $this->refactorCreateServiceMethod($classGenerator, $filePath);

        return $fileGenerator;
    }

    /**
     * @param ClassGenerator $classGenerator
     */
    protected function createInvokeMethod(ClassGenerator $classGenerator)
    {
        $createServiceMethod = $classGenerator->getMethod('createService');
        $serviceLocatorParameter = current($createServiceMethod->getParameters());

        $parameters = [];

        $parameter = new ParameterGenerator();
        $parameter->setName('container');
        $parameter->setType('Interop\\Container\\ContainerInterface');
        $parameters[] = $parameter;

        $parameter = new ParameterGenerator();
        $parameter->setName('requestedName');
        $parameters[] = $parameter;

        $parameter = new ParameterGenerator();
        $parameter->setName('options');
        $parameter->setType('array');
        $parameter->setDefaultValue(null);
        $parameters[] = $parameter;

        $body = $createServiceMethod->getBody();
        $body = str_replace('$' . $serviceLocatorParameter->getName(), '$container', $body);

        $classGenerator->addMethod(
            '__invoke',
            $parameters,
            MethodGenerator::FLAG_PUBLIC,
            $body,
            $createServiceMethod->getDocBlock()
        );
    }

    /**
     * @param ClassGenerator $classGenerator
     * @param string $filePath
     * @throws UnexpectedFactoryFormatException
     */
    protected function refactorCreateServiceMethod(ClassGenerator $classGenerator, string $filePath)
    {
        $createServiceMethod = $classGenerator->getMethod('createService');
        $serviceLocatorParameter = current($createServiceMethod->getParameters());

        // Reflection will only work for PHP7 return types
        $returnType = $createServiceMethod->getReturnType();
        if ($returnType === null) {
            $returnType = $this->getReturnType($filePath);
            if ($returnType === null) {
                throw new UnexpectedFactoryFormatException('No FullQualifiedClassName could be found for the return statement');
            }
        }

        $body = 'return $this($' . $serviceLocatorParameter->getName() . ', ' . $returnType . '::class);';

        $createServiceMethod->setBody($body);
    }

    /**
     * @param FileGenerator $fileGenerator
     * @throws UnexpectedFactoryFormatException
     * @return boolean
     */
    protected function validateFileStructure(FileGenerator $fileGenerator)
    {
        $classes = $fileGenerator->getClasses();
        if (count($classes) !== 1) {
            throw new UnexpectedFactoryFormatException('Only files which contain exactly one factory class are supported');
        }

        $classGenerator = $fileGenerator->getClass();
        if (!$classGenerator->getMethod('createService')) {
            throw new UnexpectedFactoryFormatException('Factory must be in legacy ZF2 format and contain at least a createService method');
        }
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getReturnType(string $filePath): ?string
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);

        $code = file_get_contents($filePath);

        $nodes = $parser->parse($code);

        $traverser     = new NodeTraverser;
        $visitor = new ReturnTypeVisitor();
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);
        return $visitor->getReturnType();
    }

    /**
     * @param FileGenerator $fileGenerator
     * @return boolean
     */
    public function supports(FileGenerator $fileGenerator): bool
    {
        $classGenerator = $fileGenerator->getClass();
        return in_array('Zend\ServiceManager\ServiceLocatorInterface', $classGenerator->getImplementedInterfaces());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'factory';
    }
}