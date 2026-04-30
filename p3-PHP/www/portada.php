<?php

use App\Database;
use App\Repository\NewsRepository;

['twig' => $twig, 'config' => $config] = require __DIR__ . '/config/bootstrap.php';

try {
    $database = new Database($config['database']);
    $connection = $database->getConnection();

    $newsRepository = new NewsRepository($connection);
    $noticias = $newsRepository->getAllForPortada();

    $database->close();

    echo $twig->render('portada.twig', [
        'page_title' => 'Portada',
        'noticias' => $noticias,
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'Se ha producido un error al cargar la portada.',
    ]);
}
