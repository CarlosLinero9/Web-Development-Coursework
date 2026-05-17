<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

$errores = [];
$datos = ['usuario' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['usuario'] = limpiar_texto($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($datos['usuario'] === '' || $password === '') {
        $errores[] = 'Debes introducir usuario y contraseña.';
    } else {
        try {
            $conexion = conectarBD();
            $usuario = buscar_usuario_login($conexion, $datos['usuario']);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                actualizar_sesion_usuario($conexion, (int) $usuario['id']);
                $conexion->close();
                header('Location: portada.php');
                exit;
            }

            $conexion->close();
            $errores[] = 'Usuario o contraseña incorrectos.';
        } catch (Throwable $e) {
            $errores[] = 'No se ha podido iniciar sesión.';
        }
    }
}

echo $twig->render('login.twig', [
    'page_title' => 'Entrar',
    'errores' => $errores,
    'datos' => $datos,
]);
