<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_moderador()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los moderadores pueden gestionar comentarios.',
    ]);
    exit;
}

$q = limpiar_texto($_GET['q'] ?? '');

try {
    $conexion = conectarBD();
    $comentarios = obtener_comentarios_gestion($conexion, $q);
    $conexion->close();

    echo $twig->render('gestion_comentarios.twig', [
        'page_title' => 'Gestionar comentarios',
        'comentarios' => $comentarios,
        'q' => $q,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'No se han podido cargar los comentarios.',
    ]);
}
