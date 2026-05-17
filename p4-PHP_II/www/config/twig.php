<?php

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$usuario_sesion = $_SESSION['usuario'] ?? null;

$twig->addGlobal('site', $site);
$twig->addGlobal('usuario', $usuario_sesion);
$twig->addGlobal('es_registrado', $usuario_sesion !== null);
$twig->addGlobal('es_moderador', !empty($usuario_sesion['es_moderador']) || !empty($usuario_sesion['es_superusuario']));
$twig->addGlobal('es_gestor', !empty($usuario_sesion['es_gestor']) || !empty($usuario_sesion['es_superusuario']));
$twig->addGlobal('es_superusuario', !empty($usuario_sesion['es_superusuario']));
$twig->addGlobal('flash', $_SESSION['flash'] ?? null);
unset($_SESSION['flash']);
