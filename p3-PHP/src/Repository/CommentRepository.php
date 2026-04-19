<?php

declare(strict_types=1);

namespace App\Repository;

use mysqli;

final class CommentRepository
{
    public function __construct(private readonly mysqli $connection)
    {
    }

    public function getByNewsId(int $newsId): array
    {
        $sql = <<<SQL
            SELECT id, nombre, email, texto, fecha_comentario
            FROM comentarios
            WHERE noticia_id = ?
            ORDER BY fecha_comentario DESC, id DESC
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comentarios = [];

        while ($row = $result->fetch_assoc()) {
            $comentarios[] = [
                'id' => (int) $row['id'],
                'nombre' => $row['nombre'],
                'email' => $row['email'],
                'texto' => $row['texto'],
                'fecha_comentario' => $row['fecha_comentario'],
            ];
        }

        $stmt->close();

        return $comentarios;
    }

    public function insert(int $newsId, string $name, string $email, string $text): void
    {
        $sql = 'INSERT INTO comentarios (noticia_id, nombre, email, texto, fecha_comentario) VALUES (?, ?, ?, ?, NOW())';
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('isss', $newsId, $name, $email, $text);
        $stmt->execute();
        $stmt->close();
    }
}
