<?php

declare(strict_types=1);

use App\Database;
use App\Repository\NewsRepository;

['twig' => $twig, 'config' => $config] = require __DIR__ . '/../config/bootstrap.php';

$database = new Database($config['database']);
$connection = $database->getConnection();
$newsRepository = new NewsRepository($connection);

$noticias = $newsRepository->getAllForPortada();
$database->close();

echo $twig->render('portada.html.twig', [
    'page_title' => 'Portada',
    'noticias' => $noticias,
]);
