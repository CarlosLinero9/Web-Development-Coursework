<?php

declare(strict_types=1);

use App\Database;
use App\Repository\CommentRepository;
use App\Repository\LocationRepository;
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
    ]);
    exit;
}

$database = new Database($config['database']);
$connection = $database->getConnection();
$newsRepository = new NewsRepository($connection);
$commentRepository = new CommentRepository($connection);
$locationRepository = new LocationRepository($connection);

$noticia = $newsRepository->findById($newsId);
if ($noticia === null) {
    $database->close();
    http_response_code(404);
    echo $twig->render('error.html.twig', [
        'page_title' => 'Error',
        'error_title' => 'Noticia no encontrada',
        'error_message' => 'No existe ninguna noticia con el identificador solicitado.',
    ]);
    exit;
}

$formData = ['nombre' => '', 'email' => '', 'texto' => ''];
$formErrors = [];
$modalTitle = null;
$modalMessage = null;
$openPanel = false;
$openForm = false;

$locationNames = $locationRepository->getNames();

if (($_GET['comentario'] ?? '') === 'ok') {
    $modalTitle = 'Comentario enviado';
    $modalMessage = 'Tu comentario se ha guardado correctamente en la base de datos.';
    $openPanel = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'nombre' => Input::cleanText($_POST['nombre'] ?? ''),
        'email' => Input::cleanEmail($_POST['email'] ?? ''),
        'texto' => Input::cleanText($_POST['texto'] ?? ''),
    ];

    if ($formData['nombre'] === '') {
        $formErrors[] = 'El nombre es obligatorio.';
    }

    if ($formData['email'] === '' || filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
        $formErrors[] = 'Debes introducir un e-mail válido.';
    }

    if ($formData['texto'] === '') {
        $formErrors[] = 'El comentario no puede estar vacío.';
    }

    if (mb_strlen($formData['nombre'], 'UTF-8') > 120) {
        $formErrors[] = 'El nombre es demasiado largo.';
    }

    if (mb_strlen($formData['texto'], 'UTF-8') > 1200) {
        $formErrors[] = 'El comentario es demasiado largo.';
    }

    if ($formErrors === []) {
        $commentText = TextFormatter::uppercaseLocations($formData['texto'], $locationNames);
        $commentRepository->insert($newsId, $formData['nombre'], $formData['email'], $commentText);
        $database->close();
        header('Location: noticia.php?id=' . $newsId . '&comentario=ok');
        exit;
    }

    $modalTitle = 'Revisa el formulario';
    $modalMessage = implode(' ', $formErrors);
    $openPanel = true;
    $openForm = true;
}

$imagenes = $newsRepository->getImagesByNewsId($newsId);
$comentarios = $commentRepository->getByNewsId($newsId);
$database->close();

echo $twig->render('noticia.html.twig', [
    'page_title' => $noticia['titulo'],
    'noticia' => $noticia,
    'imagenes' => $imagenes,
    'comentarios' => $comentarios,
    'descripcion_parrafos' => TextFormatter::formatDescription($noticia['descripcion']),
    'localidades_json' => json_encode($locationNames, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
    'form_data' => $formData,
    'form_errors' => $formErrors,
    'modal_title' => $modalTitle,
    'modal_message' => $modalMessage,
    'open_panel' => $openPanel,
    'open_form' => $openForm,
]);
