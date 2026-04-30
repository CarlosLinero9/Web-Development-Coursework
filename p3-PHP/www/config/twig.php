<?php

require_once __DIR__ . '/config.php';

if (file_exists('/usr/local/lib/php/vendor/autoload.php')) {
    require_once '/usr/local/lib/php/vendor/autoload.php';
} else {
    die('No se encuentra Twig. Usa el Docker de la práctica o instala Twig con Composer.');
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../plantillas');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'autoescape' => 'html',
]);

$twig->addGlobal('site', $site);
