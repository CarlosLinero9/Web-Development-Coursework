<?php

declare(strict_types=1);

namespace App\Repository;

use mysqli;

final class NewsRepository
{
    public function __construct(private readonly mysqli $connection)
    {
    }

    public function getAllForPortada(): array
    {
        $sql = <<<SQL
            SELECT n.id,
                   n.titulo,
                   n.fecha_publicacion,
                   n.tipo,
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
        SQL;

        $result = $this->connection->query($sql);
        $noticias = [];

        while ($row = $result->fetch_assoc()) {
            $noticias[] = [
                'id' => (int) $row['id'],
                'titulo' => $row['titulo'],
                'fecha_publicacion' => $row['fecha_publicacion'],
                'tipo' => $row['tipo'],
                'resumen' => $row['resumen'],
                'lugar' => $row['lugar'],
                'imagen_portada' => $row['imagen_portada'],
            ];
        }

        return $noticias;
    }

    public function findById(int $id): ?array
    {
        $sql = <<<SQL
            SELECT n.id,
                   n.titulo,
                   n.fecha_publicacion,
                   n.tipo,
                   n.concejalia,
                   n.personas_responsables,
                   n.descripcion,
                   l.id AS lugar_id,
                   l.nombre AS lugar
            FROM noticias n
            INNER JOIN lugares l ON l.id = n.lugar_id
            WHERE n.id = ?
            LIMIT 1
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row === null) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'titulo' => $row['titulo'],
            'fecha_publicacion' => $row['fecha_publicacion'],
            'tipo' => $row['tipo'],
            'concejalia' => $row['concejalia'],
            'personas_responsables' => $row['personas_responsables'],
            'descripcion' => $row['descripcion'],
            'lugar_id' => (int) $row['lugar_id'],
            'lugar' => $row['lugar'],
        ];
    }

    public function getImagesByNewsId(int $newsId): array
    {
        $sql = <<<SQL
            SELECT id, ruta, pie, orden, es_portada
            FROM imagenes
            WHERE noticia_id = ?
            ORDER BY orden ASC, id ASC
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $imagenes = [];

        while ($row = $result->fetch_assoc()) {
            $imagenes[] = [
                'id' => (int) $row['id'],
                'ruta' => $row['ruta'],
                'pie' => $row['pie'],
                'orden' => (int) $row['orden'],
                'es_portada' => (bool) $row['es_portada'],
            ];
        }

        $stmt->close();

        return $imagenes;
    }
}
