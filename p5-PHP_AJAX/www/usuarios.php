<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!es_superusuario()) {
    http_response_code(403);
    echo $twig->render('error.twig', [
        'page_title' => 'Sin permisos',
        'error_title' => 'Sin permisos',
        'error_message' => 'Solo los superusuarios pueden gestionar usuarios.',
    ]);
    exit;
}

$errores = [];

try {
    $conexion = conectarBD();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        $id = validar_id($_POST['id'] ?? null);

        if ($id !== null && $accion === 'roles') {
            $moderador = isset($_POST['es_moderador']) ? 1 : 0;
            $gestor = isset($_POST['es_gestor']) ? 1 : 0;
            $superusuario = isset($_POST['es_superusuario']) ? 1 : 0;

            if (!actualizar_roles_usuario($conexion, $id, $moderador, $gestor, $superusuario)) {
                $errores[] = 'No se han podido cambiar esos permisos. Debe quedar al menos un superusuario.';
            } else {
                $_SESSION['flash'] = 'Permisos actualizados.';
                if ($id === (int) usuario_actual()['id']) {
                    actualizar_sesion_usuario($conexion, $id);
                }
                $conexion->close();
                header('Location: usuarios.php');
                exit;
            }
        }

        if ($id !== null && $accion === 'borrar') {
            if (!borrar_usuario($conexion, $id)) {
                $errores[] = 'No se puede borrar ese usuario. Debe quedar al menos un superusuario.';
            } else {
                $_SESSION['flash'] = 'Usuario eliminado.';
                $conexion->close();
                header('Location: usuarios.php');
                exit;
            }
        }
    }

    $usuarios = obtener_usuarios($conexion);
    $conexion->close();

    echo $twig->render('usuarios.twig', [
        'page_title' => 'Gestionar usuarios',
        'usuarios' => $usuarios,
        'errores' => $errores,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'No se han podido cargar los usuarios.',
    ]);
}
