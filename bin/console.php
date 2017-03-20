#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/../vendor/autoload.php';

use Stroker\Zf3MigrationTools\Command\MigrateCommand;
use Stroker\Zf3MigrationTools\Command\MigrateCommandFactory;
use Symfony\Component\Console\Application;
use Zend\ServiceManager\ServiceManager;

$application = new Application();

// ... register commands

//$serviceManager = new ServiceManager();
//$serviceManager->configure([
//    'factories' => [
//        MigrateCommand::class => MigrateCommandFactory::class
//    ]
//]);

$application->add(new MigrateCommand());

$application->run();