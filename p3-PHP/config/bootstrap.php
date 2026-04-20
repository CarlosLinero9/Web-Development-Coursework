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

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}

$twig->addGlobal('site', $config['site']);
$twig->addGlobal('base_url', $baseUrl);

return [
    'twig' => $twig,
    'config' => $config,
    'base_url' => $baseUrl,
];
