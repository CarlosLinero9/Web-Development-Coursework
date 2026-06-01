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

$id = validar_id($_POST['id'] ?? null);
$publicado = isset($_POST['publicado']) && $_POST['publicado'] === '1' ? 1 : 0;

if ($id === null) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Identificador incorrecto.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conexion = conectarBD();
    cambiar_estado_publicado($conexion, $id, $publicado);
    $conexion->close();

    echo json_encode(['ok' => true, 'publicado' => $publicado], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se ha podido actualizar.'], JSON_UNESCAPED_UNICODE);
}
