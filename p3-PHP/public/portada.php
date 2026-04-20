<?php

declare(strict_types=1);

['base_url' => $baseUrl] = require __DIR__ . '/../config/bootstrap.php';

header('Location: ' . ($baseUrl !== '' ? $baseUrl : '') . '/', true, 302);
exit;
