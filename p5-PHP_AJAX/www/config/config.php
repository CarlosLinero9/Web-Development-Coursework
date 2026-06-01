<?php

// Datos de conexión. En Docker se toman de las variables de entorno.
$bd_host = getenv('DB_HOST') ?: 'db';
$bd_puerto = (int) (getenv('DB_PORT') ?: 3306);
$bd_nombre = getenv('DB_NAME') ?: 'sibwdb';
$bd_usuario = getenv('DB_USER') ?: 'sibwuser';
$bd_password = getenv('DB_PASSWORD') ?: '1234';
$bd_charset = 'utf8mb4';

// Datos comunes de la web que usan las plantillas.
$site = [
    'title' => 'Ayuntamiento de Iznalloz · Noticias e Incidencias',
    'municipality_url' => 'https://www.iznalloz.es/',
    'header_links' => [
        ['label' => 'Web del Ayuntamiento', 'url' => 'https://www.iznalloz.es/'],
        ['label' => 'Sede Electrónica', 'url' => 'https://sede.iznalloz.es/'],
        ['label' => 'Teléfonos de Interés', 'url' => 'https://www.iznalloz.es/ayuntamiento/telefonos-de-interes'],
    ],
    'side_links' => [
        ['label' => 'Nueva incidencia', 'url' => '#'],
        ['label' => 'Consultar incidencia', 'url' => '#'],
        ['label' => 'Avisos', 'url' => '#'],
        ['label' => 'Contacto', 'url' => '#'],
    ],
    'footer_links' => [
        ['label' => 'Información Básica', 'url' => '#'],
        ['label' => 'Web del Ayuntamiento', 'url' => 'https://www.iznalloz.es/'],
        ['label' => 'Aviso Legal', 'url' => 'https://www.granada.org/v2010.nsf/xxtod/zavisolegal'],
    ],
    'social_links' => [
        ['label' => 'Instagram', 'icon' => 'img/instagram.png', 'url' => '#'],
        ['label' => 'Twitter', 'icon' => 'img/twitter.png', 'url' => '#'],
        ['label' => 'TikTok', 'icon' => 'img/tiktok.png', 'url' => '#'],
    ],
    'copyright' => '© 2026 Incidencias Iznalloz',
];
