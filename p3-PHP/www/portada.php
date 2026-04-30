<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

try {
    $conexion = conectarBD();
    $noticias = obtener_noticias_portada($conexion);
    $conexion->close();

    echo $twig->render('portada.twig', [
        'page_title' => 'Portada',
        'noticias' => $noticias,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'Se ha producido un error al cargar la portada.',
    ]);
}
