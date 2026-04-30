<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Twig instalado como indicó la profesora en el Dockerfile del entorno LAMP.
if (file_exists('/usr/local/lib/php/vendor/autoload.php')) {
    require_once '/usr/local/lib/php/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    die('No se encuentra Twig. Instala Twig con Composer o usa el Docker de la asignatura.');
}

// Cargamos las clases propias del proyecto.
require_once __DIR__ . '/../php/Database.php';
require_once __DIR__ . '/../php/Repository/NewsRepository.php';
require_once __DIR__ . '/../php/Repository/CommentRepository.php';
require_once __DIR__ . '/../php/Repository/LocationRepository.php';
require_once __DIR__ . '/../php/Support/Input.php';
require_once __DIR__ . '/../php/Support/TextFormatter.php';

$config = require __DIR__ . '/config.php';

$loader = new FilesystemLoader(__DIR__ . '/../plantillas');
$twig = new Environment($loader, [
    'cache' => false,
    'autoescape' => 'html',
]);

$twig->addGlobal('site', $config['site']);

return [
    'twig' => $twig,
    'config' => $config,
];
