SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS sibwdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'sibwuser'@'%' IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON sibwdb.* TO 'sibwuser'@'%';
FLUSH PRIVILEGES;

USE sibwdb;

DROP TABLE IF EXISTS comentarios;
DROP TABLE IF EXISTS imagenes;
DROP TABLE IF EXISTS noticias;
DROP TABLE IF EXISTS lugares;

CREATE TABLE lugares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    fecha_publicacion DATE NOT NULL,
    tipo VARCHAR(120) NOT NULL,
    concejalia VARCHAR(180) NOT NULL,
    personas_responsables VARCHAR(255) NOT NULL,
    lugar_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    CONSTRAINT fk_noticias_lugar FOREIGN KEY (lugar_id) REFERENCES lugares(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    pie VARCHAR(255) NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    es_portada TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_imagenes_noticia FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    texto TEXT NOT NULL,
    fecha_comentario DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comentarios_noticia FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO lugares (nombre) VALUES
('Iznalloz'),
('Deifontes'),
('Peligros'),
('Albolote'),
('Alfacar'),
('Víznar'),
('Pulianas'),
('Colomera'),
('Cogollos Vega'),
('Nívar');

INSERT INTO noticias (titulo, fecha_publicacion, tipo, concejalia, personas_responsables, lugar_id, descripcion) VALUES
(
    'Corte temporal de agua en zona centro (mañana)',
    '2026-03-11',
    'Incidencia',
    'Urbanismo y Mantenimiento',
    'Ayuntamiento de Iznalloz y Servicios Técnicos Municipales',
    1,
    'El Ayuntamiento de Iznalloz ha informado de un corte temporal en el suministro de agua potable que afecta a varias calles del municipio debido a una avería detectada en la red general de abastecimiento.\n\nLa incidencia se localiza en una tubería principal que presenta una fuga y que requiere una intervención urgente para evitar daños mayores y restablecer el servicio en condiciones de seguridad.\n\nLos operarios ya están trabajando en la zona afectada para sustituir el tramo dañado y comprobar el estado del resto de la instalación. Mientras duren las labores de reparación, los vecinos de las calles afectadas podrán experimentar falta de suministro o bajadas de presión.\n\nDesde el Ayuntamiento se recomienda hacer un uso responsable del agua almacenada hasta que el servicio quede completamente restablecido. También se ruega a los vecinos que eviten abrir grifos de forma continuada mientras dure la incidencia, ya que el suministro se recuperará progresivamente una vez finalicen los trabajos.'
),
(
    'Obras de mejora en la Calle Real',
    '2026-03-09',
    'Urbanismo',
    'Urbanismo y Obras Públicas',
    'Concejalía de Urbanismo y empresa adjudicataria MGS Construcción',
    1,
    'Han comenzado las obras de mejora del firme y renovación de infraestructuras en la Calle Real. El proyecto contempla la sustitución de pavimento deteriorado, la mejora de la accesibilidad peatonal y la actualización de canalizaciones de servicios básicos.\n\nDurante las próximas semanas se producirán cortes puntuales al tráfico y restricciones de estacionamiento. Los accesos a viviendas y comercios se mantendrán abiertos siempre que la evolución de la obra lo permita.\n\nEl Ayuntamiento recomienda utilizar itinerarios alternativos y atender a la señalización provisional colocada en la zona.'
),
(
    'Actividades deportivas fin de semana',
    '2026-03-08',
    'Deportes',
    'Deportes y Juventud',
    'Área de Deportes del Ayuntamiento de Iznalloz',
    1,
    'El pabellón municipal acogerá durante el fin de semana varias actividades deportivas dirigidas a escolares, clubes locales y público general. Se celebrarán exhibiciones de gimnasia, entrenamientos abiertos y encuentros amistosos de diferentes disciplinas.\n\nAdemás, se habilitará una zona informativa para nuevas inscripciones en escuelas deportivas municipales y se reforzará la señalización de accesos y aparcamientos.\n\nLa entrada será libre hasta completar aforo.'
),
(
    'Aviso por viento y lluvia: recomendaciones',
    '2026-03-07',
    'Aviso',
    'Seguridad Ciudadana y Protección Civil',
    'Protección Civil, Policía Local y Ayuntamiento de Iznalloz',
    1,
    'Ante la previsión de rachas intensas de viento y episodios de lluvia, el Ayuntamiento pide extremar la precaución y evitar desplazamientos innecesarios durante los momentos de mayor intensidad.\n\nSe recomienda retirar objetos de balcones y terrazas, no estacionar junto a árboles o cornisas y consultar únicamente fuentes oficiales para seguir la evolución de la situación.\n\nCualquier incidencia urgente deberá comunicarse a los servicios municipales y de emergencias.'
),
(
    'Cambio horario de recogida de residuos',
    '2026-03-05',
    'Servicios Municipales',
    'Medio Ambiente y Limpieza',
    'Servicio de Limpieza Viaria y Recogida',
    1,
    'Desde la próxima semana se modificará el horario de recogida de residuos domésticos para mejorar la coordinación del servicio y reducir molestias en determinadas zonas del municipio.\n\nLos contenedores deberán utilizarse preferentemente a partir de las 20:00. El nuevo horario se aplicará de forma progresiva y se reforzará la información en barrios y pedanías.\n\nSe recuerda la importancia de respetar los horarios y de depositar correctamente cada fracción en su contenedor correspondiente.'
);

INSERT INTO imagenes (noticia_id, ruta, pie, orden, es_portada) VALUES
(1, 'img/n1.jpeg', 'Cartel oficial anunciando el corte temporal de agua.', 1, 1),
(1, 'img/n2.jpg', 'Zona afectada por la incidencia en el suministro de agua.', 2, 0),
(1, 'img/n3.jpg', 'Trabajos de apoyo logístico en instalaciones municipales.', 3, 0),
(1, 'img/n4.jpg', 'Aviso preventivo publicado en exteriores del municipio.', 4, 0),
(1, 'img/n5.jpg', 'Seguimiento nocturno de tareas de limpieza y señalización.', 5, 0),
(2, 'img/n2.jpg', 'Estado de la Calle Real durante las obras de mejora.', 1, 1),
(3, 'img/n3.jpg', 'Pabellón municipal preparado para las actividades deportivas.', 1, 1),
(4, 'img/n4.jpg', 'Cartelería de aviso por viento y lluvia.', 1, 1),
(5, 'img/n5.jpg', 'Recordatorio del nuevo horario de residuos en zonas de servicio.', 1, 1);

INSERT INTO comentarios (noticia_id, nombre, email, texto, fecha_comentario) VALUES
(1, 'María López García', 'maria@example.com', 'Gracias por el aviso. En la zona de la plaza de IZNALLOZ llevamos toda la tarde con muy poca presión y ya empezaba a ser preocupante.', '2026-03-10 18:20:00'),
(1, 'Antonio Ruiz Martínez', 'antonio@example.com', 'Sería importante saber si se va a habilitar algún punto de suministro alternativo, sobre todo para personas mayores que no pueden desplazarse fácilmente desde DEIFONTES.', '2026-03-10 19:05:00'),
(1, 'Lucía Fernández Sánchez', 'lucia@example.com', 'En mi calle ya han colocado señalización y hay operarios trabajando. Ojalá lo solucionen pronto porque está afectando a bastantes vecinos de PELIGROS y ALBOLOTE.', '2026-03-10 20:11:00'),
(2, 'Javier Molina', 'javier@example.com', 'Las obras parecen necesarias. Estaría bien publicar también el calendario aproximado por tramos.', '2026-03-09 11:30:00'),
(3, 'Paula Torres', 'paula@example.com', 'Muy buena iniciativa para animar a más gente joven a participar en las escuelas deportivas.', '2026-03-08 09:15:00'),
(4, 'Raúl Pérez', 'raul@example.com', 'Gracias por compartir las recomendaciones. En VÍZNAR también hemos tenido bastante viento estos días.', '2026-03-07 16:45:00'),
(5, 'Carmen Díaz', 'carmen@example.com', 'Convendría reforzar la cartelería para que nadie saque las bolsas antes de tiempo.', '2026-03-05 21:10:00');
