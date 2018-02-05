#!/usr/bin/env php
<?php
// application.php

require __DIR__ . '/vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Zend\ServiceManager\ServiceManager;

$container = new ServiceManager(
    [
        'factories' => [
            PDO::class => function () {
                return new \potfur\PSQLNotify\DB\LazyPDO($_ENV['URN'] ?? 'pgsql:host=localhost;port=5432;dbname=postgres;user=postgres');
            },
            \potfur\PSQLNotify\Commands\Clear::class => function (ContainerInterface $container) {
                return new \potfur\PSQLNotify\Commands\Clear($container->get(PDO::class));
            },
            \potfur\PSQLNotify\Commands\Initialize::class => function (ContainerInterface $container) {
                return new \potfur\PSQLNotify\Commands\Initialize($container->get(PDO::class));
            },
            \potfur\PSQLNotify\Commands\SendToChannel::class => function (ContainerInterface $container) {
                return new \potfur\PSQLNotify\Commands\SendToChannel($container->get(PDO::class));
            },
            \potfur\PSQLNotify\Commands\ListenToChannel::class => function (ContainerInterface $container) {
                return new \potfur\PSQLNotify\Commands\ListenToChannel($container->get(PDO::class));
            },
        ],
    ]
);

$application = new Application();

$application->add($container->get(\potfur\PSQLNotify\Commands\Clear::class));
$application->add($container->get(\potfur\PSQLNotify\Commands\Initialize::class));

$application->add($container->get(\potfur\PSQLNotify\Commands\SendToChannel::class));
$application->add($container->get(\potfur\PSQLNotify\Commands\ListenToChannel::class));

$application->run();