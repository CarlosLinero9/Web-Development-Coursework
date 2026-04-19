<?php

declare(strict_types=1);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$loader = new FilesystemLoader(dirname(__DIR__) . '/templates');
$twig = new Environment($loader, [
    'cache' => false,
    'autoescape' => 'html',
    'strict_variables' => false,
]);

$twig->addGlobal('site', $config['site']);

return [
    'twig' => $twig,
    'config' => $config,
];
