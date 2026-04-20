<?php

declare(strict_types=1);

use App\Support\Input;

['base_url' => $baseUrl] = require __DIR__ . '/../config/bootstrap.php';

$newsId = Input::validateNewsId($_GET['id'] ?? null);
if ($newsId === null) {
    header('Location: ' . ($baseUrl !== '' ? $baseUrl : '') . '/', true, 302);
    exit;
}

$query = '';
if (isset($_GET['comentario']) && $_GET['comentario'] === 'ok') {
    $query = '?comentario=ok';
}

header('Location: ' . $baseUrl . '/noticia/' . $newsId . $query, true, 302);
exit;
