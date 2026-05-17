<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_moderador()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los moderadores pueden borrar comentarios.',
    ]);
    exit;
}

$id = validar_id($_POST['id'] ?? $_GET['id'] ?? null);
$volver = $_POST['volver'] ?? 'gestion_comentarios.php';

if ($id !== null) {
    $conexion = conectarBD();
    borrar_comentario($conexion, $id);
    $conexion->close();
}

header('Location: ' . $volver);
exit;
