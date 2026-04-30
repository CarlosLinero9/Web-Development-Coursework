<?php

function validar_id($valor): ?int
{
    $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

function limpiar_texto(string $texto): string
{
    $texto = trim($texto);
    $texto = strip_tags($texto);
    return preg_replace('/\s+/u', ' ', $texto) ?? $texto;
}

function limpiar_email(string $email): string
{
    return strtolower(trim($email));
}

function obtener_noticias_portada(mysqli $conexion): array
{
    $sql = "
        SELECT n.id, n.titulo, n.fecha_publicacion, n.tipo,
               LEFT(n.descripcion, 180) AS resumen,
               l.nombre AS lugar,
               i.ruta AS imagen_portada
        FROM noticias n
        INNER JOIN lugares l ON l.id = n.lugar_id
        INNER JOIN imagenes i ON i.id = (
            SELECT i2.id
            FROM imagenes i2
            WHERE i2.noticia_id = n.id
            ORDER BY i2.es_portada DESC, i2.orden ASC, i2.id ASC
            LIMIT 1
        )
        ORDER BY n.fecha_publicacion DESC, n.id DESC
    ";

    $resultado = $conexion->query($sql);
    $noticias = [];

    while ($fila = $resultado->fetch_assoc()) {
        $noticias[] = $fila;
    }

    return $noticias;
}

function obtener_noticia(mysqli $conexion, int $id): ?array
{
    $sql = "
        SELECT n.id, n.titulo, n.fecha_publicacion, n.tipo,
               n.concejalia, n.personas_responsables,
               l.nombre AS lugar, n.descripcion
        FROM noticias n
        INNER JOIN lugares l ON l.id = n.lugar_id
        WHERE n.id = ?
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $noticia = $resultado->fetch_assoc();
    $stmt->close();

    return $noticia ?: null;
}

function obtener_imagenes(mysqli $conexion, int $noticia_id): array
{
    $stmt = $conexion->prepare('SELECT id, ruta, pie, orden, es_portada FROM imagenes WHERE noticia_id = ? ORDER BY orden ASC, id ASC');
    $stmt->bind_param('i', $noticia_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $imagenes = [];

    while ($fila = $resultado->fetch_assoc()) {
        $imagenes[] = $fila;
    }

    $stmt->close();
    return $imagenes;
}

function obtener_comentarios(mysqli $conexion, int $noticia_id): array
{
    $stmt = $conexion->prepare('SELECT id, nombre, email, texto, fecha_comentario FROM comentarios WHERE noticia_id = ? ORDER BY fecha_comentario DESC, id DESC');
    $stmt->bind_param('i', $noticia_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $comentarios = [];

    while ($fila = $resultado->fetch_assoc()) {
        $comentarios[] = $fila;
    }

    $stmt->close();
    return $comentarios;
}

function insertar_comentario(mysqli $conexion, int $noticia_id, string $nombre, string $email, string $texto): void
{
    $stmt = $conexion->prepare('INSERT INTO comentarios (noticia_id, nombre, email, texto, fecha_comentario) VALUES (?, ?, ?, ?, NOW())');
    $stmt->bind_param('isss', $noticia_id, $nombre, $email, $texto);
    $stmt->execute();
    $stmt->close();
}

function obtener_localidades(mysqli $conexion): array
{
    $resultado = $conexion->query('SELECT nombre FROM lugares ORDER BY nombre ASC');
    $localidades = [];

    while ($fila = $resultado->fetch_assoc()) {
        $localidades[] = $fila['nombre'];
    }

    return $localidades;
}

function formatear_descripcion(string $texto): array
{
    $parrafos = preg_split('/\R{2,}/u', trim($texto)) ?: [];
    return array_values(array_filter(array_map('trim', $parrafos)));
}

function poner_localidades_mayusculas(string $texto, array $localidades): string
{
    foreach ($localidades as $localidad) {
        $patron = '/\b' . preg_quote($localidad, '/') . '\b/iu';
        $texto = preg_replace_callback($patron, function () use ($localidad) {
            return mb_strtoupper($localidad, 'UTF-8');
        }, $texto) ?? $texto;
    }

    return $texto;
}
