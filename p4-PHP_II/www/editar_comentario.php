<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_moderador()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los moderadores pueden editar comentarios.',
    ]);
    exit;
}

$id = validar_id($_GET['id'] ?? null);
if ($id === null) {
    http_response_code(400);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Comentario incorrecto',
        'error_message' => 'El comentario solicitado no es válido.',
    ]);
    exit;
}

$errores = [];

try {
    $conexion = conectarBD();
    $comentario = obtener_comentario($conexion, $id);

    if (!$comentario) {
        $conexion->close();
        http_response_code(404);
        echo $twig->render('error.twig', [
            'page_title' => 'Error',
            'error_title' => 'Comentario no encontrado',
            'error_message' => 'No existe ese comentario.',
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $texto = limpiar_texto($_POST['texto'] ?? '');
        if ($texto === '') {
            $errores[] = 'El comentario no puede estar vacío.';
        }
        if (empty($errores)) {
            actualizar_comentario($conexion, $id, $texto);
            $conexion->close();
            header('Location: gestion_comentarios.php');
            exit;
        }
        $comentario['texto'] = $texto;
    }

    $conexion->close();

    echo $twig->render('editar_comentario.twig', [
        'page_title' => 'Editar comentario',
        'comentario' => $comentario,
        'errores' => $errores,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'No se ha podido editar el comentario.',
    ]);
}
