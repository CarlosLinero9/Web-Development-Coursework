<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

header('Content-Type: application/json; charset=utf-8');

$q = limpiar_texto($_GET['q'] ?? '');

if (mb_strlen($q, 'UTF-8') < 2) {
    echo json_encode(['ok' => true, 'noticias' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conexion = conectarBD();
    $noticias = buscar_noticias_portada_ajax($conexion, $q);
    $conexion->close();

    echo json_encode(['ok' => true, 'noticias' => $noticias], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al buscar noticias.'], JSON_UNESCAPED_UNICODE);
}
