<?php

declare(strict_types=1);

use App\Database;
use App\Repository\NewsRepository;
use App\Support\Input;
use App\Support\TextFormatter;

['twig' => $twig, 'config' => $config] = require __DIR__ . '/../config/bootstrap.php';

$newsId = Input::validateNewsId($_GET['id'] ?? null);
if ($newsId === null) {
    http_response_code(400);
    echo $twig->render('error.html.twig', [
        'page_title' => 'Error',
        'error_title' => 'Noticia no válida',
        'error_message' => 'El identificador recibido no es correcto.',
        'print_view' => true,
    ]);
    exit;
}

$database = new Database($config['database']);
$connection = $database->getConnection();
$newsRepository = new NewsRepository($connection);
$noticia = $newsRepository->findById($newsId);

if ($noticia === null) {
    $database->close();
    http_response_code(404);
    echo $twig->render('error.html.twig', [
        'page_title' => 'Error',
        'error_title' => 'Noticia no encontrada',
        'error_message' => 'No existe ninguna noticia con el identificador solicitado.',
        'print_view' => true,
    ]);
    exit;
}

$imagenes = $newsRepository->getImagesByNewsId($newsId);
$database->close();

echo $twig->render('noticia_imprimir.html.twig', [
    'page_title' => $noticia['titulo'] . ' · Imprimir',
    'noticia' => $noticia,
    'imagenes' => $imagenes,
    'descripcion_parrafos' => TextFormatter::formatDescription($noticia['descripcion']),
    'print_view' => true,
]);
