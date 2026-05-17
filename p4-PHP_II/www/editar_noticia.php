<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_gestor()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los gestores pueden editar noticias.',
    ]);
    exit;
}

$id = validar_id($_GET['id'] ?? null);
$es_nueva = $id === null;
$errores = [];
$datos = [
    'titulo' => '',
    'fecha_publicacion' => date('Y-m-d'),
    'tipo' => '',
    'concejalia' => '',
    'personas_responsables' => '',
    'lugar_id' => 1,
    'descripcion' => '',
    'imagenes_texto' => "img/n1.jpeg|Imagen de la noticia.|1|1",
    'hashtags_texto' => '',
];

try {
    $conexion = conectarBD();
    $lugares = obtener_lugares($conexion);

    if (!$es_nueva) {
        $noticia = obtener_noticia($conexion, $id);
        if (!$noticia) {
            $conexion->close();
            http_response_code(404);
            echo $twig->render('error.twig', [
                'page_title' => 'Error',
                'error_title' => 'Noticia no encontrada',
                'error_message' => 'No existe esa noticia.',
            ]);
            exit;
        }
        $datos = [
            'titulo' => $noticia['titulo'],
            'fecha_publicacion' => $noticia['fecha_publicacion'],
            'tipo' => $noticia['tipo'],
            'concejalia' => $noticia['concejalia'],
            'personas_responsables' => $noticia['personas_responsables'],
            'lugar_id' => (int) $noticia['lugar_id'],
            'descripcion' => $noticia['descripcion'],
            'imagenes_texto' => obtener_imagenes_texto($conexion, $id),
            'hashtags_texto' => obtener_hashtags_texto($conexion, $id),
        ];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datos['titulo'] = limpiar_texto($_POST['titulo'] ?? '');
        $datos['fecha_publicacion'] = limpiar_texto($_POST['fecha_publicacion'] ?? '');
        $datos['tipo'] = limpiar_texto($_POST['tipo'] ?? '');
        $datos['concejalia'] = limpiar_texto($_POST['concejalia'] ?? '');
        $datos['personas_responsables'] = limpiar_texto($_POST['personas_responsables'] ?? '');
        $datos['lugar_id'] = validar_id($_POST['lugar_id'] ?? null) ?? 0;
        $datos['descripcion'] = trim(strip_tags($_POST['descripcion'] ?? ''));
        $datos['imagenes_texto'] = trim($_POST['imagenes_texto'] ?? '');
        $datos['hashtags_texto'] = limpiar_texto($_POST['hashtags_texto'] ?? '');

        if ($datos['titulo'] === '') {
            $errores[] = 'El título es obligatorio.';
        }
        if ($datos['fecha_publicacion'] === '') {
            $errores[] = 'La fecha es obligatoria.';
        }
        if ($datos['tipo'] === '') {
            $errores[] = 'El tipo es obligatorio.';
        }
        if ($datos['concejalia'] === '') {
            $errores[] = 'La concejalía es obligatoria.';
        }
        if ($datos['personas_responsables'] === '') {
            $errores[] = 'Los responsables son obligatorios.';
        }
        if ($datos['lugar_id'] < 1) {
            $errores[] = 'Debes elegir un lugar.';
        }
        if ($datos['descripcion'] === '') {
            $errores[] = 'La descripción es obligatoria.';
        }

        if (empty($errores)) {
            if ($es_nueva) {
                $id = insertar_noticia($conexion, $datos);
            } else {
                actualizar_noticia($conexion, $id, $datos);
            }
            guardar_imagenes_desde_texto($conexion, $id, $datos['imagenes_texto']);
            guardar_hashtags_noticia($conexion, $id, $datos['hashtags_texto']);
            $conexion->close();
            header('Location: noticia.php?id=' . $id);
            exit;
        }
    }

    $conexion->close();

    echo $twig->render('editar_noticia.twig', [
        'page_title' => $es_nueva ? 'Nueva noticia' : 'Editar noticia',
        'datos' => $datos,
        'lugares' => $lugares,
        'errores' => $errores,
        'es_nueva' => $es_nueva,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'No se ha podido guardar la noticia.',
    ]);
}
