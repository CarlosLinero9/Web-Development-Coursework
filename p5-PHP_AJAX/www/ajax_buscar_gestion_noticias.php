<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

header('Content-Type: application/json; charset=utf-8');

if (!es_gestor()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Sin permisos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$q = limpiar_texto($_GET['q'] ?? '');

try {
    $conexion = conectarBD();
    $noticias = buscar_noticias_gestion_ajax($conexion, $q);
    $conexion->close();

    echo json_encode(['ok' => true, 'noticias' => $noticias], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al buscar noticias.'], JSON_UNESCAPED_UNICODE);
}
