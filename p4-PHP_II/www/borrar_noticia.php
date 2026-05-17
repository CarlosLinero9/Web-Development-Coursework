<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_gestor()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los gestores pueden borrar noticias.',
    ]);
    exit;
}

$id = validar_id($_POST['id'] ?? null);
if ($id !== null) {
    $conexion = conectarBD();
    borrar_noticia($conexion, $id);
    $conexion->close();
}

header('Location: gestion_noticias.php');
exit;
