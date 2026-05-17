<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_gestor()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los gestores pueden gestionar noticias.',
    ]);
    exit;
}

$q_titulo = limpiar_texto($_GET['titulo'] ?? '');
$q_descripcion = limpiar_texto($_GET['descripcion'] ?? '');

try {
    $conexion = conectarBD();
    $noticias = obtener_noticias_gestion($conexion, $q_titulo, $q_descripcion);
    $conexion->close();

    echo $twig->render('gestion_noticias.twig', [
        'page_title' => 'Gestionar noticias',
        'noticias' => $noticias,
        'q_titulo' => $q_titulo,
        'q_descripcion' => $q_descripcion,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'No se han podido cargar las noticias.',
    ]);
}
