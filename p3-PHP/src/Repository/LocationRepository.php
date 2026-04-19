<?php

declare(strict_types=1);

namespace App\Repository;

use mysqli;

final class LocationRepository
{
    public function __construct(private readonly mysqli $connection)
    {
    }

    public function getAll(): array
    {
        $sql = 'SELECT id, nombre FROM lugares ORDER BY nombre ASC';
        $result = $this->connection->query($sql);
        $lugares = [];

        while ($row = $result->fetch_assoc()) {
            $lugares[] = [
                'id' => (int) $row['id'],
                'nombre' => $row['nombre'],
            ];
        }

        return $lugares;
    }

    public function getNames(): array
    {
        return array_map(
            static fn (array $lugar): string => $lugar['nombre'],
            $this->getAll()
        );
    }
}
