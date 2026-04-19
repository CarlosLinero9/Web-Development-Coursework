<?php

declare(strict_types=1);

return [
    'database' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'sibwdb',
        'user' => getenv('DB_USER') ?: 'sibwuser',
        'password' => getenv('DB_PASSWORD') ?: 'sibwpass',
        'charset' => 'utf8mb4',
    ],
    'site' => [
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
            ['label' => 'Instagram', 'icon' => 'assets/img/instagram.png', 'url' => '#'],
            ['label' => 'Twitter', 'icon' => 'assets/img/twitter.png', 'url' => '#'],
            ['label' => 'TikTok', 'icon' => 'assets/img/tiktok.png', 'url' => '#'],
        ],
        'copyright' => '© 2026 Incidencias Iznalloz',
    ],
];
