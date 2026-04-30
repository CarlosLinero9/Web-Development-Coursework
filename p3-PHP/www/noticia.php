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
    $form_data = ['nombre' => '', 'email' => '', 'texto' => ''];
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
        $form_data['nombre'] = limpiar_texto($_POST['nombre'] ?? '');
        $form_data['email'] = limpiar_email($_POST['email'] ?? '');
        $form_data['texto'] = limpiar_texto($_POST['texto'] ?? '');

        if ($form_data['nombre'] === '') {
            $form_errors[] = 'El nombre es obligatorio.';
        }

        if ($form_data['email'] === '' || filter_var($form_data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $form_errors[] = 'Debes introducir un e-mail válido.';
        }

        if ($form_data['texto'] === '') {
            $form_errors[] = 'El comentario no puede estar vacío.';
        }

        if (mb_strlen($form_data['nombre'], 'UTF-8') > 120) {
            $form_errors[] = 'El nombre es demasiado largo.';
        }

        if (mb_strlen($form_data['texto'], 'UTF-8') > 1200) {
            $form_errors[] = 'El comentario es demasiado largo.';
        }

        if (empty($form_errors)) {
            $texto = poner_localidades_mayusculas($form_data['texto'], $localidades);
            insertar_comentario($conexion, $noticia_id, $form_data['nombre'], $form_data['email'], $texto);
            $conexion->close();

            header('Location: noticia.php?id=' . $noticia_id . '&comentario=ok');
            exit;
        }

        $modal_title = 'Revisa el formulario';
        $modal_message = implode(' ', $form_errors);
        $open_panel = true;
        $open_form = true;
    }

    $imagenes = obtener_imagenes($conexion, $noticia_id);
    $comentarios = obtener_comentarios($conexion, $noticia_id);
    $conexion->close();

    echo $twig->render('noticia.twig', [
        'page_title' => $noticia['titulo'],
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'comentarios' => $comentarios,
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
