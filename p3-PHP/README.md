# Práctica 3 SIBW · PHP + MySQL + Twig

Proyecto completo de la práctica 3 de SIBW (UGR) montado a partir de las prácticas 1 y 2.

## Qué incluye

- MVC básico con separación clara entre **controladores PHP**, **repositorios/modelo** y **plantillas Twig**.
- Base de datos MySQL con tablas para:
  - `noticias`
  - `imagenes`
  - `comentarios`
  - `lugares`
- Plantillas Twig con herencia:
  - `base.html.twig`
  - `portada.html.twig`
  - `noticia.html.twig`
  - `noticia_imprimir.html.twig`
- Comentarios persistidos en base de datos.
- Localidades cargadas desde base de datos y pasadas al JS mediante `data-localidades` en JSON.
- Validación **cliente + servidor**.
- Uso de **consultas preparadas** para entradas del usuario.
- Usuario de base de datos distinto de `root`.
- Una sola conexión a base de datos por petición.
- Docker Compose con Apache, MySQL y phpMyAdmin.

## Estructura

```text
sibw_practica3/
├── composer.json
├── docker-compose.yml
├── Dockerfile
├── config/
├── db/
├── public/
│   ├── portada.php
│   ├── noticia.php
│   ├── noticia_imprimir.php
│   └── assets/
├── src/
└── templates/
```

## Opción recomendada: Docker

La memoria de la práctica indica que la opción preferible es **Docker**, y como alternativa local se puede usar **XAMPP** en Windows o **LAMPP** en Linux.

### Pasos

1. Abre una terminal dentro de la carpeta del proyecto.
2. Ejecuta:

```bash
docker compose up --build
```

3. Abre:
   - Web: `http://localhost:8080/`
   - phpMyAdmin: `http://localhost:8081/`

### Credenciales por defecto

- **BD**: `sibwdb`
- **Usuario app**: `sibwuser`
- **Password app**: `sibwpass`
- **Root MySQL**: `rootpass`

## Opción alternativa: XAMPP / LAMPP

1. Copia el proyecto dentro de `htdocs` (XAMPP) o el directorio equivalente.
2. Ejecuta:

```bash
composer install
```

3. Crea la BD y el usuario:
   - Ejecuta `db/create_user_local.sql`
   - Después ejecuta `db/init.sql`
4. Ajusta `config/config.php` si cambia host, puerto o contraseña.
5. Abre `http://localhost/sibw_practica3/public/portada.php`

## Decisiones de diseño para la defensa

### 1. Modelo de datos

Se ha usado un diseño sencillo y defendible:

- `noticias` guarda la información principal.
- `lugares` guarda las localidades del municipio y alrededores.
- `imagenes` permite una relación **1:N** entre noticia e imágenes.
- `comentarios` permite una relación **1:N** entre noticia y comentarios.

### 2. Imágenes

Se ha optado por guardar en la base de datos la **ruta del archivo**, no el binario de la imagen.

Motivo: es más simple para esta práctica, más fácil de mantener y encaja bien con el material visto en clase.

### 3. Twig

No hay HTML dentro de los controladores `.php`.

Los controladores:
- validan parámetros,
- consultan la base de datos,
- preparan variables,
- y renderizan Twig.

Las vistas Twig solo muestran datos.

### 4. Seguridad

- Validación de `id` con entero positivo.
- Validación de campos `POST` en servidor.
- Validación de email con `filter_var`.
- Sanitización básica con `trim`, `strip_tags` y normalización de espacios.
- Consultas preparadas para evitar inyección SQL.
- Usuario de conexión distinto de root.
- Configuración centralizada en `config/config.php`.
- Desactivación de funciones peligrosas en `docker/php.ini`.

### 5. Comentarios

El alta de comentario se hace por `POST` a `noticia.php?id=...`.

- JS mantiene la validación del lado cliente.
- PHP repite la validación en servidor.
- Si todo va bien, inserta en BD y redirige a GET (`POST/REDIRECT/GET`).

### 6. Localidades en mayúscula

Las localidades ya no están hardcodeadas en JS.

Ahora:
- PHP las consulta desde la tabla `lugares`.
- Twig las pasa en JSON mediante `data-localidades`.
- JS las lee y las pone en mayúscula mientras se escribe.
- PHP vuelve a aplicar la conversión antes de guardar el comentario para mantener consistencia.

## Posibles cambios que te pueden pedir en la defensa

1. Añadir una noticia nueva en `db/init.sql`.
2. Cambiar el orden de las noticias en portada.
3. Añadir una localidad nueva en `lugares` y comprobar que JS la detecta.
4. Convertir un campo como `tipo` en tabla aparte.
5. Mostrar el número de imágenes por noticia en portada.
6. Añadir más validaciones de longitud en comentarios.

## Nota

Para que Twig funcione, necesitas ejecutar `composer install`.
