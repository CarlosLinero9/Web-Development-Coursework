<?php

declare(strict_types=1);

use App\Database;
use App\Repository\CommentRepository;
use App\Repository\LocationRepository;
use App\Repository\NewsRepository;
use App\Support\TextFormatter;

['twig' => $twig, 'config' => $config, 'base_url' => $baseUrl] = require __DIR__ . '/../config/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = resolveRequestPath($baseUrl);

try {
    if ($method === 'GET' && ($path === '/' || $path === '/portada')) {
        renderPortada($twig, $config);
        exit;
    }

    if (preg_match('#^/noticia/([1-9][0-9]*)$#', $path, $matches) === 1) {
        renderNoticia($twig, $config, (int) $matches[1], $baseUrl, $method);
        exit;
    }

    if ($method === 'GET' && preg_match('#^/noticia/([1-9][0-9]*)/imprimir$#', $path, $matches) === 1) {
        renderNoticiaImprimir($twig, $config, (int) $matches[1]);
        exit;
    }

    renderError($twig, 404, 'Página no encontrada', 'La ruta solicitada no existe.');
} catch (Throwable $exception) {
    renderError($twig, 500, 'Error interno', 'Se ha producido un error inesperado en el servidor.');
}

function resolveRequestPath(string $baseUrl): string
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
        return '/';
    }

    if ($baseUrl !== '' && str_starts_with($path, $baseUrl)) {
        $path = substr($path, strlen($baseUrl));
    }

    if ($path === '' || $path === false) {
        return '/';
    }

    return '/' . trim($path, '/');
}

function renderPortada(Twig\Environment $twig, array $config): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        renderError($twig, 405, 'Método no permitido', 'La portada solo admite peticiones GET.');
        return;
    }

    $database = new Database($config['database']);
    $connection = $database->getConnection();
    $newsRepository = new NewsRepository($connection);
    $noticias = $newsRepository->getAllForPortada();
    $database->close();

    echo $twig->render('portada.html.twig', [
        'page_title' => 'Portada',
        'noticias' => $noticias,
    ]);
}

function renderNoticia(Twig\Environment $twig, array $config, int $newsId, string $baseUrl, string $method): void
{
    if (!in_array($method, ['GET', 'POST'], true)) {
        renderError($twig, 405, 'Método no permitido', 'La noticia solo admite peticiones GET y POST.');
        return;
    }

    $database = new Database($config['database']);
    $connection = $database->getConnection();
    $newsRepository = new NewsRepository($connection);
    $commentRepository = new CommentRepository($connection);
    $locationRepository = new LocationRepository($connection);

    $noticia = $newsRepository->findById($newsId);
    if ($noticia === null) {
        $database->close();
        renderError($twig, 404, 'Noticia no encontrada', 'No existe ninguna noticia con el identificador solicitado.');
        return;
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

    if ($method === 'POST') {
        $formData = [
            'nombre' => App\Support\Input::cleanText($_POST['nombre'] ?? ''),
            'email' => App\Support\Input::cleanEmail($_POST['email'] ?? ''),
            'texto' => App\Support\Input::cleanText($_POST['texto'] ?? ''),
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
            header('Location: ' . $baseUrl . '/noticia/' . $newsId . '?comentario=ok');
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
}

function renderNoticiaImprimir(Twig\Environment $twig, array $config, int $newsId): void
{
    $database = new Database($config['database']);
    $connection = $database->getConnection();
    $newsRepository = new NewsRepository($connection);
    $noticia = $newsRepository->findById($newsId);

    if ($noticia === null) {
        $database->close();
        renderError($twig, 404, 'Noticia no encontrada', 'No existe ninguna noticia con el identificador solicitado.', true);
        return;
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
}

function renderError(Twig\Environment $twig, int $statusCode, string $title, string $message, bool $printView = false): void
{
    http_response_code($statusCode);

    echo $twig->render('error.html.twig', [
        'page_title' => 'Error',
        'error_title' => $title,
        'error_message' => $message,
        'print_view' => $printView,
    ]);
}
