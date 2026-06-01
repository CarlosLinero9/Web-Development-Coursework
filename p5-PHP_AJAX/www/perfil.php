<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

if (!esta_logueado()) {
    header('Location: login.php');
    exit;
}

$errores = [];
$usuario = usuario_actual();
$datos = [
    'usuario' => $usuario['usuario'],
    'nombre' => $usuario['nombre'],
    'email' => $usuario['email'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'guardar';

    try {
        $conexion = conectarBD();

        if ($accion === 'eliminar') {
            if (borrar_usuario($conexion, (int) $usuario['id'])) {
                $conexion->close();
                $_SESSION = [];
                session_destroy();
                header('Location: portada.php');
                exit;
            }
            $errores[] = 'No se puede eliminar este usuario porque el sistema debe tener al menos un superusuario.';
        } else {
            $datos['usuario'] = limpiar_texto($_POST['usuario'] ?? '');
            $datos['nombre'] = limpiar_texto($_POST['nombre'] ?? '');
            $datos['email'] = limpiar_email($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($datos['usuario'] === '' || $datos['nombre'] === '' || $datos['email'] === '') {
                $errores[] = 'Usuario, nombre y email son obligatorios.';
            }
            if ($datos['email'] !== '' && filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errores[] = 'El email no es válido.';
            }
            if ($password !== '' && strlen($password) < 4) {
                $errores[] = 'La contraseña debe tener al menos 4 caracteres.';
            }
            if (empty($errores) && existe_usuario_o_email($conexion, $datos['usuario'], $datos['email'], (int) $usuario['id'])) {
                $errores[] = 'Ya existe otro usuario con ese nombre o email.';
            }
            if (empty($errores)) {
                actualizar_perfil($conexion, (int) $usuario['id'], $datos['usuario'], $datos['nombre'], $datos['email'], $password);
                actualizar_sesion_usuario($conexion, (int) $usuario['id']);
                $_SESSION['flash'] = 'Datos actualizados correctamente.';
                $conexion->close();
                header('Location: perfil.php');
                exit;
            }
        }

        $conexion->close();
    } catch (Throwable $e) {
        $errores[] = 'No se han podido guardar los cambios.';
    }
}

echo $twig->render('perfil.twig', [
    'page_title' => 'Mi perfil',
    'errores' => $errores,
    'datos' => $datos,
]);
