<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

$noticia_id = validar_id($_GET['id'] ?? null);

if ($noticia_id === null) {
    http_response_code(400);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Identificador incorrecto',
        'error_message' => 'La noticia solicitada no es válida.',
        'print_view' => true,
    ]);
    exit;
}

try {
    $conexion = conectarBD();
    $noticia = obtener_noticia($conexion, $noticia_id);

    if ($noticia === null) {
        $conexion->close();
        http_response_code(404);
        echo $twig->render('error.twig', [
            'page_title' => 'Error',
            'error_title' => 'Noticia no encontrada',
            'error_message' => 'No existe ninguna noticia con ese identificador.',
            'print_view' => true,
        ]);
        exit;
    }

    $imagenes = obtener_imagenes($conexion, $noticia_id);
    $conexion->close();

    echo $twig->render('noticia_imprimir.twig', [
        'page_title' => $noticia['titulo'] . ' · Imprimir',
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'descripcion_parrafos' => formatear_descripcion($noticia['descripcion']),
        'print_view' => true,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'Se ha producido un error al cargar la versión de impresión.',
        'print_view' => true,
    ]);
}
