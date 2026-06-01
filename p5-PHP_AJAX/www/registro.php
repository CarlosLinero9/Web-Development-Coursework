<?php

require_once __DIR__ . '/config/twig.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/php/funciones.php';

$errores = [];
$datos = ['usuario' => '', 'nombre' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['usuario'] = limpiar_texto($_POST['usuario'] ?? '');
    $datos['nombre'] = limpiar_texto($_POST['nombre'] ?? '');
    $datos['email'] = limpiar_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($datos['usuario'] === '' || $datos['nombre'] === '' || $datos['email'] === '' || $password === '') {
        $errores[] = 'Todos los campos son obligatorios.';
    }
    if ($datos['email'] !== '' && filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errores[] = 'El email no es válido.';
    }
    if ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }
    if (strlen($password) < 4) {
        $errores[] = 'La contraseña debe tener al menos 4 caracteres.';
    }

    if (empty($errores)) {
        try {
            $conexion = conectarBD();

            if (existe_usuario_o_email($conexion, $datos['usuario'], $datos['email'])) {
                $errores[] = 'Ya existe un usuario con ese nombre o email.';
            } else {
                $id = registrar_usuario($conexion, $datos['usuario'], $datos['nombre'], $datos['email'], $password);
                actualizar_sesion_usuario($conexion, $id);
                $conexion->close();
                header('Location: portada.php');
                exit;
            }

            $conexion->close();
        } catch (Throwable $e) {
            $errores[] = 'No se ha podido crear la cuenta.';
        }
    }
}

echo $twig->render('registro.twig', [
    'page_title' => 'Registro',
    'errores' => $errores,
    'datos' => $datos,
]);
