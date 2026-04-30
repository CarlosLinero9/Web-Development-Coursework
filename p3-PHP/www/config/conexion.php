<?php

require_once __DIR__ . '/config.php';

function conectarBD(): mysqli
{
    global $bd_host, $bd_puerto, $bd_nombre, $bd_usuario, $bd_password, $bd_charset;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conexion = new mysqli($bd_host, $bd_usuario, $bd_password, $bd_nombre, $bd_puerto);
    $conexion->set_charset($bd_charset);

    return $conexion;
}
