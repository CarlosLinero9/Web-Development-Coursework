# SIBW Web Practices

Repositorio con prácticas desarrolladas para la asignatura de Sistemas de Información Basados en Web.

El proyecto consiste en la evolución progresiva de un portal web de noticias e incidencias municipales, comenzando con una versión estática en HTML y CSS y ampliándolo posteriormente con JavaScript, PHP, MySQL, gestión de usuarios, roles y funcionalidades AJAX.

## Contenido

El repositorio está organizado en distintas prácticas:

- `p1-HTML-CSS`: desarrollo inicial del portal web utilizando HTML y CSS.
- `p2-JavaScript`: incorporación de interactividad en el lado cliente mediante JavaScript.
- `p3-PHP`: adaptación del sitio a PHP, uso de plantillas Twig y conexión con base de datos MySQL.
- `p4-PHP_II`: ampliación del sistema con sesiones, autenticación, roles de usuario y gestión dinámica de contenido.
- `p5-PHP_AJAX`: incorporación de funcionalidades asíncronas mediante AJAX.

## Tecnologías utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Twig
- AJAX
- Docker
- Docker Compose
- phpMyAdmin

## Funcionalidades principales

- Portal de noticias e incidencias.
- Página principal con listado de noticias.
- Página de detalle de noticia.
- Versión de impresión de noticias.
- Sistema de comentarios.
- Conexión con base de datos MySQL.
- Uso de plantillas Twig para separar lógica y presentación.
- Registro e inicio de sesión de usuarios.
- Gestión de perfiles.
- Roles de usuario: registrado, moderador, gestor y superusuario.
- Gestión de comentarios y noticias.
- Búsqueda dinámica de noticias mediante AJAX.
- Cambio de estado de publicación de noticias de forma asíncrona.

## Ejecución

Las prácticas basadas en PHP incluyen configuración con Docker.

Para ejecutar una de ellas:

```bash
cd p3-PHP
docker compose up --build
```

También se puede ejecutar del mismo modo en:

```bash
cd p4-PHP_II
docker compose up --build
```
o:

```bash
cd p5-PHP_AJAX
docker compose up --build
```

Una vez levantados los contenedores, la aplicación estará disponible en el puerto configurado en el docker-compose.yml.