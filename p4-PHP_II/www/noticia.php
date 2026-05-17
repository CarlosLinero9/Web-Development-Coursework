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
        ]);
        exit;
    }

    $localidades = obtener_localidades($conexion);
    $usuario = usuario_actual();
    $form_data = [
        'nombre' => $usuario['nombre'] ?? '',
        'email' => $usuario['email'] ?? '',
        'texto' => '',
    ];
    $form_errors = [];
    $modal_title = null;
    $modal_message = null;
    $open_panel = false;
    $open_form = false;

    if (($_GET['comentario'] ?? '') === 'ok') {
        $modal_title = 'Comentario enviado';
        $modal_message = 'Tu comentario se ha guardado correctamente.';
        $open_panel = true;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!esta_logueado()) {
            $form_errors[] = 'Debes iniciar sesión para comentar.';
        } else {
            $form_data['texto'] = limpiar_texto($_POST['texto'] ?? '');

            if ($form_data['texto'] === '') {
                $form_errors[] = 'El comentario no puede estar vacío.';
            }

            if (mb_strlen($form_data['texto'], 'UTF-8') > 1200) {
                $form_errors[] = 'El comentario es demasiado largo.';
            }

            if (empty($form_errors)) {
                $texto = poner_localidades_mayusculas($form_data['texto'], $localidades);
                insertar_comentario($conexion, $noticia_id, (int) $usuario['id'], $usuario['nombre'], $usuario['email'], $texto);
                $conexion->close();

                header('Location: noticia.php?id=' . $noticia_id . '&comentario=ok');
                exit;
            }
        }

        $modal_title = 'Revisa el formulario';
        $modal_message = implode(' ', $form_errors);
        $open_panel = true;
        $open_form = true;
    }

    $imagenes = obtener_imagenes($conexion, $noticia_id);
    $comentarios = obtener_comentarios($conexion, $noticia_id);
    $hashtags = obtener_hashtags_noticia($conexion, $noticia_id);
    $conexion->close();

    echo $twig->render('noticia.twig', [
        'page_title' => $noticia['titulo'],
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'comentarios' => $comentarios,
        'hashtags' => $hashtags,
        'descripcion_parrafos' => formatear_descripcion($noticia['descripcion']),
        'localidades_json' => json_encode($localidades, JSON_UNESCAPED_UNICODE),
        'form_data' => $form_data,
        'form_errors' => $form_errors,
        'modal_title' => $modal_title,
        'modal_message' => $modal_message,
        'open_panel' => $open_panel,
        'open_form' => $open_form,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'Se ha producido un error al cargar la noticia.',
    ]);
}
