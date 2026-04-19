<?php

declare(strict_types=1);

namespace App;

use mysqli;
use RuntimeException;

final class Database
{
    private mysqli $connection;

    public function __construct(array $config)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->connection = new mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['name'],
            $config['port']
        );

        if ($this->connection->connect_errno) {
            throw new RuntimeException('No se ha podido conectar con la base de datos.');
        }

        $this->connection->set_charset($config['charset'] ?? 'utf8mb4');
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function close(): void
    {
        $this->connection->close();
    }
}
