<?php

use App\Database;
use App\Repository\NewsRepository;
use App\Support\Input;
use App\Support\TextFormatter;

['twig' => $twig, 'config' => $config] = require __DIR__ . '/config/bootstrap.php';

$newsId = Input::validateNewsId($_GET['id'] ?? null);

if ($newsId === null) {
    http_response_code(400);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Identificador incorrecto',
        'error_message' => 'La noticia solicitada no es válida.',
        'print_view' => true,
    ]);
    exit;
}

try {
    $database = new Database($config['database']);
    $connection = $database->getConnection();

    $newsRepository = new NewsRepository($connection);
    $noticia = $newsRepository->findById($newsId);

    if ($noticia === null) {
        $database->close();
        http_response_code(404);
        echo $twig->render('error.twig', [
            'page_title' => 'Error',
            'error_title' => 'Noticia no encontrada',
            'error_message' => 'No existe ninguna noticia con ese identificador.',
            'print_view' => true,
        ]);
        exit;
    }

    $imagenes = $newsRepository->getImagesByNewsId($newsId);
    $database->close();

    echo $twig->render('noticia_imprimir.twig', [
        'page_title' => $noticia['titulo'] . ' · Imprimir',
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'descripcion_parrafos' => TextFormatter::formatDescription($noticia['descripcion']),
        'print_view' => true,
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    echo $twig->render('error.twig', [
        'page_title' => 'Error',
        'error_title' => 'Error interno',
        'error_message' => 'Se ha producido un error al cargar la versión de impresión.',
        'print_view' => true,
    ]);
}
