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

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function esta_logueado(): bool
{
    return isset($_SESSION['usuario']);
}

function es_moderador(): bool
{
    return !empty($_SESSION['usuario']['es_moderador']) || !empty($_SESSION['usuario']['es_superusuario']);
}

function es_gestor(): bool
{
    return !empty($_SESSION['usuario']['es_gestor']) || !empty($_SESSION['usuario']['es_superusuario']);
}

function es_superusuario(): bool
{
    return !empty($_SESSION['usuario']['es_superusuario']);
}

function actualizar_sesion_usuario(mysqli $conexion, int $id): bool
{
    $stmt = $conexion->prepare('SELECT id, usuario, nombre, email, es_moderador, es_gestor, es_superusuario FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    if (!$usuario) {
        unset($_SESSION['usuario']);
        return false;
    }

    $_SESSION['usuario'] = [
        'id' => (int) $usuario['id'],
        'usuario' => $usuario['usuario'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'es_moderador' => (int) $usuario['es_moderador'],
        'es_gestor' => (int) $usuario['es_gestor'],
        'es_superusuario' => (int) $usuario['es_superusuario'],
    ];

    return true;
}

function buscar_usuario_login(mysqli $conexion, string $usuario): ?array
{
    $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1');
    $stmt->bind_param('ss', $usuario, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila ?: null;
}

function existe_usuario_o_email(mysqli $conexion, string $usuario, string $email, ?int $excluir_id = null): bool
{
    if ($excluir_id === null) {
        $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $usuario, $email);
    } else {
        $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE (usuario = ? OR email = ?) AND id <> ? LIMIT 1');
        $stmt->bind_param('ssi', $usuario, $email, $excluir_id);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $existe = $resultado->fetch_assoc() !== null;
    $stmt->close();
    return $existe;
}

function registrar_usuario(mysqli $conexion, string $usuario, string $nombre, string $email, string $password): int
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare('INSERT INTO usuarios (usuario, nombre, email, password_hash) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $usuario, $nombre, $email, $hash);
    $stmt->execute();
    $id = $conexion->insert_id;
    $stmt->close();
    return (int) $id;
}

function obtener_usuarios(mysqli $conexion): array
{
    $resultado = $conexion->query('SELECT id, usuario, nombre, email, es_moderador, es_gestor, es_superusuario, fecha_alta FROM usuarios ORDER BY id ASC');
    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }
    return $usuarios;
}

function obtener_usuario_por_id(mysqli $conexion, int $id): ?array
{
    $stmt = $conexion->prepare('SELECT id, usuario, nombre, email, es_moderador, es_gestor, es_superusuario FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    return $usuario ?: null;
}

function actualizar_perfil(mysqli $conexion, int $id, string $usuario, string $nombre, string $email, string $password = ''): void
{
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare('UPDATE usuarios SET usuario = ?, nombre = ?, email = ?, password_hash = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $usuario, $nombre, $email, $hash, $id);
    } else {
        $stmt = $conexion->prepare('UPDATE usuarios SET usuario = ?, nombre = ?, email = ? WHERE id = ?');
        $stmt->bind_param('sssi', $usuario, $nombre, $email, $id);
    }
    $stmt->execute();
    $stmt->close();
}

function contar_superusuarios(mysqli $conexion): int
{
    $resultado = $conexion->query('SELECT COUNT(*) AS total FROM usuarios WHERE es_superusuario = 1');
    $fila = $resultado->fetch_assoc();
    return (int) $fila['total'];
}

function actualizar_roles_usuario(mysqli $conexion, int $id, int $moderador, int $gestor, int $superusuario): bool
{
    $usuario = obtener_usuario_por_id($conexion, $id);
    if (!$usuario) {
        return false;
    }

    if ((int) $usuario['es_superusuario'] === 1 && $superusuario === 0 && contar_superusuarios($conexion) <= 1) {
        return false;
    }

    $stmt = $conexion->prepare('UPDATE usuarios SET es_moderador = ?, es_gestor = ?, es_superusuario = ? WHERE id = ?');
    $stmt->bind_param('iiii', $moderador, $gestor, $superusuario, $id);
    $stmt->execute();
    $stmt->close();
    return true;
}

function borrar_usuario(mysqli $conexion, int $id): bool
{
    $usuario = obtener_usuario_por_id($conexion, $id);
    if (!$usuario) {
        return false;
    }

    if ((int) $usuario['es_superusuario'] === 1 && contar_superusuarios($conexion) <= 1) {
        return false;
    }

    $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    return true;
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
               n.concejalia, n.personas_responsables, n.lugar_id,
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

function obtener_noticias_gestion(mysqli $conexion, string $titulo = '', string $descripcion = ''): array
{
    $sql = "
        SELECT n.id, n.titulo, n.fecha_publicacion, n.tipo, l.nombre AS lugar
        FROM noticias n
        INNER JOIN lugares l ON l.id = n.lugar_id
        WHERE 1 = 1
    ";
    $tipos = '';
    $params = [];

    if ($titulo !== '') {
        $sql .= ' AND n.titulo LIKE ?';
        $tipos .= 's';
        $params[] = '%' . $titulo . '%';
    }

    if ($descripcion !== '') {
        $sql .= ' AND n.descripcion LIKE ?';
        $tipos .= 's';
        $params[] = '%' . $descripcion . '%';
    }

    $sql .= ' ORDER BY n.fecha_publicacion DESC, n.id DESC';
    $stmt = $conexion->prepare($sql);

    if ($params) {
        $stmt->bind_param($tipos, ...$params);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $noticias = [];
    while ($fila = $resultado->fetch_assoc()) {
        $noticias[] = $fila;
    }
    $stmt->close();
    return $noticias;
}

function obtener_lugares(mysqli $conexion): array
{
    $resultado = $conexion->query('SELECT id, nombre FROM lugares ORDER BY nombre ASC');
    $lugares = [];
    while ($fila = $resultado->fetch_assoc()) {
        $lugares[] = $fila;
    }
    return $lugares;
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

function obtener_imagenes_texto(mysqli $conexion, int $noticia_id): string
{
    $imagenes = obtener_imagenes($conexion, $noticia_id);
    $lineas = [];
    foreach ($imagenes as $imagen) {
        $lineas[] = $imagen['ruta'] . '|' . $imagen['pie'] . '|' . $imagen['orden'] . '|' . $imagen['es_portada'];
    }
    return implode("\n", $lineas);
}

function guardar_imagenes_desde_texto(mysqli $conexion, int $noticia_id, string $texto): void
{
    $stmt = $conexion->prepare('DELETE FROM imagenes WHERE noticia_id = ?');
    $stmt->bind_param('i', $noticia_id);
    $stmt->execute();
    $stmt->close();

    $lineas = preg_split('/\R/u', trim($texto)) ?: [];
    $insertadas = 0;

    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '') {
            continue;
        }

        $partes = array_map('trim', explode('|', $linea));
        $ruta = $partes[0] ?? '';
        $pie = $partes[1] ?? '';
        $orden = (int) ($partes[2] ?? ($insertadas + 1));
        $es_portada = (int) ($partes[3] ?? ($insertadas === 0 ? 1 : 0));

        if ($ruta === '') {
            continue;
        }

        if ($orden < 1) {
            $orden = $insertadas + 1;
        }

        $stmt = $conexion->prepare('INSERT INTO imagenes (noticia_id, ruta, pie, orden, es_portada) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issii', $noticia_id, $ruta, $pie, $orden, $es_portada);
        $stmt->execute();
        $stmt->close();
        $insertadas++;
    }

    if ($insertadas === 0) {
        $ruta = 'img/n1.jpeg';
        $pie = 'Imagen de la noticia.';
        $orden = 1;
        $es_portada = 1;
        $stmt = $conexion->prepare('INSERT INTO imagenes (noticia_id, ruta, pie, orden, es_portada) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issii', $noticia_id, $ruta, $pie, $orden, $es_portada);
        $stmt->execute();
        $stmt->close();
    }
}

function insertar_noticia(mysqli $conexion, array $datos): int
{
    $stmt = $conexion->prepare('INSERT INTO noticias (titulo, fecha_publicacion, tipo, concejalia, personas_responsables, lugar_id, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssis', $datos['titulo'], $datos['fecha_publicacion'], $datos['tipo'], $datos['concejalia'], $datos['personas_responsables'], $datos['lugar_id'], $datos['descripcion']);
    $stmt->execute();
    $id = $conexion->insert_id;
    $stmt->close();
    return (int) $id;
}

function actualizar_noticia(mysqli $conexion, int $id, array $datos): void
{
    $stmt = $conexion->prepare('UPDATE noticias SET titulo = ?, fecha_publicacion = ?, tipo = ?, concejalia = ?, personas_responsables = ?, lugar_id = ?, descripcion = ? WHERE id = ?');
    $stmt->bind_param('sssssisi', $datos['titulo'], $datos['fecha_publicacion'], $datos['tipo'], $datos['concejalia'], $datos['personas_responsables'], $datos['lugar_id'], $datos['descripcion'], $id);
    $stmt->execute();
    $stmt->close();
}

function borrar_noticia(mysqli $conexion, int $id): void
{
    $stmt = $conexion->prepare('DELETE FROM noticias WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

function obtener_comentarios(mysqli $conexion, int $noticia_id): array
{
    $stmt = $conexion->prepare('SELECT id, nombre, email, texto, fecha_comentario, editado_moderador FROM comentarios WHERE noticia_id = ? ORDER BY fecha_comentario DESC, id DESC');
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

function obtener_comentario(mysqli $conexion, int $id): ?array
{
    $stmt = $conexion->prepare('SELECT c.*, n.titulo AS titulo_noticia FROM comentarios c INNER JOIN noticias n ON n.id = c.noticia_id WHERE c.id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $comentario = $resultado->fetch_assoc();
    $stmt->close();
    return $comentario ?: null;
}

function obtener_comentarios_gestion(mysqli $conexion, string $busqueda = ''): array
{
    if ($busqueda !== '') {
        $like = '%' . $busqueda . '%';
        $stmt = $conexion->prepare('SELECT c.*, n.titulo AS titulo_noticia FROM comentarios c INNER JOIN noticias n ON n.id = c.noticia_id WHERE c.nombre LIKE ? OR c.email LIKE ? OR c.texto LIKE ? OR n.titulo LIKE ? ORDER BY c.fecha_comentario DESC');
        $stmt->bind_param('ssss', $like, $like, $like, $like);
    } else {
        $stmt = $conexion->prepare('SELECT c.*, n.titulo AS titulo_noticia FROM comentarios c INNER JOIN noticias n ON n.id = c.noticia_id ORDER BY c.fecha_comentario DESC');
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $comentarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $comentarios[] = $fila;
    }
    $stmt->close();
    return $comentarios;
}

function insertar_comentario(mysqli $conexion, int $noticia_id, int $usuario_id, string $nombre, string $email, string $texto): void
{
    $stmt = $conexion->prepare('INSERT INTO comentarios (noticia_id, usuario_id, nombre, email, texto, fecha_comentario) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->bind_param('iisss', $noticia_id, $usuario_id, $nombre, $email, $texto);
    $stmt->execute();
    $stmt->close();
}

function actualizar_comentario(mysqli $conexion, int $id, string $texto): void
{
    $stmt = $conexion->prepare('UPDATE comentarios SET texto = ?, editado_moderador = 1 WHERE id = ?');
    $stmt->bind_param('si', $texto, $id);
    $stmt->execute();
    $stmt->close();
}

function borrar_comentario(mysqli $conexion, int $id): void
{
    $stmt = $conexion->prepare('DELETE FROM comentarios WHERE id = ?');
    $stmt->bind_param('i', $id);
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

function obtener_hashtags_noticia(mysqli $conexion, int $noticia_id): array
{
    $stmt = $conexion->prepare('SELECT h.nombre FROM hashtags h INNER JOIN noticia_hashtag nh ON nh.hashtag_id = h.id WHERE nh.noticia_id = ? ORDER BY h.nombre ASC');
    $stmt->bind_param('i', $noticia_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $hashtags = [];
    while ($fila = $resultado->fetch_assoc()) {
        $hashtags[] = $fila['nombre'];
    }
    $stmt->close();
    return $hashtags;
}

function obtener_hashtags_texto(mysqli $conexion, int $noticia_id): string
{
    return implode(', ', obtener_hashtags_noticia($conexion, $noticia_id));
}

function guardar_hashtags_noticia(mysqli $conexion, int $noticia_id, string $texto): void
{
    $stmt = $conexion->prepare('DELETE FROM noticia_hashtag WHERE noticia_id = ?');
    $stmt->bind_param('i', $noticia_id);
    $stmt->execute();
    $stmt->close();

    $partes = preg_split('/[,#]+/u', $texto) ?: [];
    $nombres = [];
    foreach ($partes as $parte) {
        $nombre = mb_strtolower(limpiar_texto($parte), 'UTF-8');
        $nombre = str_replace(' ', '-', $nombre);
        if ($nombre !== '') {
            $nombres[$nombre] = true;
        }
    }

    foreach (array_keys($nombres) as $nombre) {
        $stmt = $conexion->prepare('INSERT IGNORE INTO hashtags (nombre) VALUES (?)');
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $stmt->close();

        $stmt = $conexion->prepare('SELECT id FROM hashtags WHERE nombre = ?');
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $hashtag = $resultado->fetch_assoc();
        $stmt->close();

        if ($hashtag) {
            $hashtag_id = (int) $hashtag['id'];
            $stmt = $conexion->prepare('INSERT IGNORE INTO noticia_hashtag (noticia_id, hashtag_id) VALUES (?, ?)');
            $stmt->bind_param('ii', $noticia_id, $hashtag_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
