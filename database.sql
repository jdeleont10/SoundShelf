-- SoundShelf: catálogo de música cristiana para Programación II.
-- Las descripciones y puntuaciones son textos originales del catálogo; no son letras de canciones.
-- Álbum, año, duración, portadas y enlaces: catálogo de Apple Music. Las portadas se cargan desde internet.
-- Son registros académicos almacenados en MySQL y consultados dinámicamente por PHP.
-- Compatible con MySQL 8 y MariaDB. Importar desde Inicio de phpMyAdmin en XAMPP.
-- Este archivo no borra bases de datos ni tablas existentes.

CREATE DATABASE IF NOT EXISTS soundshelf
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE soundshelf;

CREATE TABLE IF NOT EXISTS generos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(60) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS canciones (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    genero_id INT UNSIGNED NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    subtitulo VARCHAR(160) NOT NULL,
    descripcion TEXT NOT NULL,
    artista VARCHAR(100) NOT NULL,
    album VARCHAR(100) NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    duracion_segundos SMALLINT UNSIGNED NOT NULL,
    puntuacion DECIMAL(3,1) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    enlace_apple VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    INDEX idx_canciones_genero (genero_id),
    CONSTRAINT fk_canciones_genero
        FOREIGN KEY (genero_id) REFERENCES generos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación 1:N: un género puede tener varias canciones.
-- INSERT IGNORE permite reimportar los mismos registros sin duplicar sus ID.
INSERT IGNORE INTO generos (id, nombre, descripcion) VALUES
    (1, 'Alabanza', 'Canciones festivas y llenas de energía para celebrar y cantar en comunidad.'),
    (2, 'Adoración', 'Canciones tranquilas y profundas que acompañan momentos de oración y reflexión.'),
    (3, 'Folk cristiano', 'Guitarras acústicas y voces cercanas con un estilo sencillo y honesto.'),
    (4, 'Urbano cristiano', 'Rap y ritmos urbanos que llevan mensajes de fe a nuevas generaciones.'),
    (5, 'Pop rock cristiano', 'Guitarras y melodías de pop rock con mensajes de esperanza.');

INSERT IGNORE INTO canciones
    (id, genero_id, titulo, subtitulo, descripcion, artista, album, anio, duracion_segundos, puntuacion, imagen, enlace_apple)
VALUES
    (1, 1, 'La Sunamita (Live)', 'Una celebración grabada en vivo.',
     'Una canción de alabanza grabada en vivo junto a Alex Marquez, con mucha energía y un ambiente de fiesta. Su título hace referencia a la historia de la mujer sunamita de la Biblia.',
     'Montesanto, Alex Marquez', 'Bautizados En Fuego (LIVE)', 2022, 774, 9.6, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/85/39/46/85394650-b7f6-e845-4d67-fdb36f9b5ec8/706055467286.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/la-sunamita-feat-alex-marquez-oficial-live/1799833331?i=1799833621'),
    (2, 1, 'Fiesta En El Desierto (Live)', 'Alegría que se contagia.',
     'Una alabanza festiva grabada en vivo para el álbum Bautizados En Fuego. Su ritmo alegre la convierte en una canción para celebrar en comunidad.',
     'Montesanto', 'Bautizados En Fuego (LIVE)', 2023, 338, 9.5, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/85/39/46/85394650-b7f6-e845-4d67-fdb36f9b5ec8/706055467286.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/fiesta-en-el-desierto-live/1799833331?i=1799833614'),
    (3, 3, 'Watch Your Mouth', 'Folk acústico con un mensaje directo.',
     'Una canción de folk cristiano con un estilo acústico y una voz cercana. Su estilo sencillo pone el mensaje en primer plano.',
     'Josiah Queen', 'Mt. Zion', 2025, 222, 9.2, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/ea/50/30/ea5030f4-9832-9ac8-1589-c55d5e370e27/25SYMIM20370.rgb.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/watch-your-mouth/1818407230?i=1818407506'),
    (4, 2, 'So Will I (100 Billion X)', 'Una canción de adoración que crece poco a poco.',
     'Una canción de adoración en inglés que empieza de forma tranquila y va creciendo con cada parte. Su letra habla de la creación y es una de las canciones más conocidas de Hillsong UNITED.',
     'Hillsong UNITED, Benjamin William Hastings', 'Wonder', 2017, 411, 9.7, 'https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/20/4b/ef/204bef43-3729-dc96-f3db-e10d5459aa93/9320428328489.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/so-will-i-100-billion-x/1827103843?i=1827104048'),
    (5, 2, 'De Gloria en Gloria', 'Adoración en español.',
     'Una canción de adoración en español de Marco Barrientos, uno de los líderes de adoración más conocidos de Latinoamérica.',
     'Marco Barrientos', 'Amanece (Deluxe)', 2014, 262, 9.0, 'https://is1-ssl.mzstatic.com/image/thumb/Music115/v4/b1/bf/f0/b1bff0e6-0536-0246-5b37-2556e60b1711/mzm.izmokxdm.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/de-gloria-en-gloria/1056591571?i=1056592124'),
    (6, 1, 'Alaba (Live)', 'Una invitación a alabar.',
     'Una colaboración entre Evan Craft e Intimidad Worship, una canción de alabanza en español para cantar con alegría.',
     'Evan Craft, Intimidad Worship', 'Que Cante La Iglesia (Live)', 2024, 311, 9.1, 'https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/a5/f9/ac/a5f9accc-781a-b0dd-2649-7031e618ccf3/25SYMIM21516.rgb.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/alaba-live/1828815750?i=1828815772'),
    (7, 3, 'Dusty Bibles', 'Folk cristiano con guitarra y voz.',
     'Otra canción de folk cristiano de Josiah Queen, con un estilo acústico y relajado. Su título hace referencia a las Biblias guardadas que se llenan de polvo.',
     'Josiah Queen', 'Mt. Zion', 2025, 190, 9.0, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/ea/50/30/ea5030f4-9832-9ac8-1589-c55d5e370e27/25SYMIM20370.rgb.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/dusty-bibles/1818407230?i=1818407514'),
    (8, 4, 'Guarda Tu Corazón', 'Rap con un mensaje de fe.',
     'Una canción de rap cristiano con una base urbana. Como dice su título, su mensaje habla de cuidar el corazón.',
     'Alex Zurdo', 'Conxsión', 2023, 238, 8.8, 'https://is1-ssl.mzstatic.com/image/thumb/Music116/v4/4f/1a/f1/4f1af163-d52a-0d18-b9d0-e3576a33f92e/617513579314_cover.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/guarda-tu-coraz%C3%B3n/1687713062?i=1687713063'),
    (9, 5, 'Babel', 'Pop rock con guitarras y fuerza.',
     'Una colaboración entre la banda Un Corazón y Kim Richards, con el estilo de pop rock cristiano que caracteriza a la banda.',
     'Un Corazón, Kim Richards', 'KINTSUGI', 2024, 302, 8.9, 'https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/7b/6e/ec/7b6eec8e-455c-f8c8-66e0-be6a7e87b1ed/8114.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/babel-feat-kim-richards/1725883197?i=1725883409'),
    (10, 2, 'Escucha mi Oración', 'Una oración hecha canción.',
     'Una canción de adoración con un tono personal, pensada como una oración. Ideal para momentos de calma y reflexión.',
     'Jonathan Napoles', 'Intimidad - EP', 2025, 142, 8.7, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/a0/b5/39/a0b539ad-cf67-48bb-8060-a0e1ed886fe9/artwork.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/escucha-mi-oraci%C3%B3n/1797905271?i=1797905272');

-- Canciones agregadas: 2 por género. Datos de Apple Music.
INSERT IGNORE INTO canciones
    (id, genero_id, titulo, subtitulo, descripcion, artista, album, anio, duracion_segundos, puntuacion, imagen, enlace_apple)
VALUES
    (11, 2, 'Oceans (Where Feet May Fail)', 'Una canción sobre confiar en lo desconocido.',
     'Una de las canciones de adoración más conocidas de Hillsong UNITED, con la voz de Taya. Empieza de forma tranquila y va creciendo poco a poco hasta un final lleno de fuerza.',
     'Hillsong UNITED, TAYA', 'Zion (Deluxe Edition)', 2013, 536, 9.6, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/17/9b/c6/179bc683-9aaf-6cb3-3cb9-684cee8e7362/9320428362100.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/oceans-where-feet-may-fail/1827100780?i=1827100787'),
    (12, 2, 'Como Dijiste', 'Adoración con la voz de Christine D''Clario.',
     'Una canción de adoración en español de Christine D''Clario, una de las voces más reconocidas de la música cristiana latina.',
     'Christine D''Clario', 'De Vuelta al Jardín', 2010, 300, 9.1, 'https://is1-ssl.mzstatic.com/image/thumb/Music128/v4/78/07/b4/7807b45a-1c11-0e0f-00b8-5698d12f652b/192562004463.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/como-dijiste/1321567767?i=1321569045'),
    (13, 1, 'Graves into Gardens (Live)', 'Una celebración grabada en vivo.',
     'Una canción de Elevation Worship junto a Brandon Lake, grabada en vivo. Su título habla de convertir lo que parecía perdido en algo nuevo.',
     'Elevation Worship, Brandon Lake', 'Graves into Gardens (Live)', 2020, 452, 9.4, 'https://is1-ssl.mzstatic.com/image/thumb/Music115/v4/be/61/90/be619005-8f9e-17a2-2ca7-08b5bfefb4bf/886448352051.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/graves-into-gardens-live/1502315705?i=1502316157'),
    (14, 1, 'Danzando (Live)', 'Una fiesta de alabanza en vivo.',
     'Una alabanza en español grabada en vivo por Gateway Worship Español junto a varios invitados. Su ritmo alegre invita a celebrar.',
     'Gateway Worship Español, Christine D''Clario, Travy Joe', 'Grande y Fiel (Live)', 2022, 489, 9.3, 'https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/7f/d0/cc/7fd0cc17-9c5a-c3f2-32c5-10bb83e02200/23SYMIM05877.rgb.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/danzando-feat-daniel-calveti-becky-collazos-josh-morales/1700279987?i=1700280281'),
    (15, 3, 'The Prodigal', 'Folk inspirado en el hijo pródigo.',
     'La canción que dio a conocer a Josiah Queen. Con un estilo acústico, toma como inspiración la parábola del hijo pródigo.',
     'Josiah Queen', 'The Prodigal (Deluxe Edition)', 2023, 193, 9.3, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/c1/90/3b/c1903b62-8144-49d0-362f-b165f3a8ee45/artwork.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/the-prodigal/1779547999?i=1779548014'),
    (16, 3, 'I Will Carry You', 'Una canción acústica y tranquila.',
     'Una canción de Ellie Holcomb con un estilo acústico y cercano, ideal para momentos de calma.',
     'Ellie Holcomb', 'Canyon', 2021, 207, 8.6, 'https://is1-ssl.mzstatic.com/image/thumb/Music125/v4/8a/f5/04/8af5043d-ccc1-cc5a-c302-b6c43d658411/21UMGIM23088.rgb.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/i-will-carry-you/1560455403?i=1560455873'),
    (17, 4, 'Todo Va a Estar Bien', 'Un mensaje de esperanza.',
     'Una colaboración entre Redimi2, uno de los pioneros del rap cristiano, y Evan Craft. Su título resume su mensaje de esperanza.',
     'Redimi2, Evan Craft', '20/20', 2020, 192, 8.9, 'https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/8f/27/96/8f279618-debe-f779-f756-13bcc5291133/9318.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/todo-va-a-estar-bien/1823788261?i=1823788524'),
    (18, 4, 'Lo Que Traigo Es Flow', 'Un clásico del urbano cristiano.',
     'Una canción de Funky del álbum Los Vencedores, de la época en que el reggaetón cristiano empezó a crecer.',
     'Funky', 'Los Vencedores', 2004, 200, 8.5, 'https://is1-ssl.mzstatic.com/image/thumb/Music125/v4/6f/e1/cb/6fe1cbbc-efb6-7530-9bee-5ac6934fbdd6/859746278460_cover.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/lo-que-traigo-es-flow/1580217662?i=1580217786'),
    (19, 5, 'GOOD DAY', 'Pop alegre para empezar el día.',
     'Una canción pop de Forrest Frank con un ritmo alegre y relajado, que se hizo muy popular en redes sociales.',
     'Forrest Frank', 'CHILD OF GOD', 2024, 171, 9.0, 'https://is1-ssl.mzstatic.com/image/thumb/Music211/v4/a9/35/3b/a9353b28-9788-afea-43c1-0a91666ec5a9/810129994124.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/good-day/1755273435?i=1755274032'),
    (20, 5, '¿Sabes? (En Vivo)', 'Rock cristiano en vivo.',
     'Una canción de la banda argentina Rescate, grabada en vivo. Rescate es una de las bandas de rock cristiano más conocidas de Latinoamérica.',
     'Rescate', 'Una Raza Contra el Viento (Vivo)', 2004, 207, 8.7, 'https://is1-ssl.mzstatic.com/image/thumb/Music124/v4/a5/c8/2c/a5c82c65-e2d2-9c4b-0329-32505b77ca85/mzi.gvdrspvq.jpg/600x600bb.jpg', 'https://music.apple.com/us/album/sabes-en-vivo/470845156?i=470845227');

-- CONSULTA 1: listar todos los géneros en orden alfabético.
SELECT id, nombre, descripcion
FROM generos
ORDER BY nombre;

-- CONSULTA 2: obtener canciones con puntuación de 9 o más, de mayor a menor.
SELECT id, titulo, artista, album, puntuacion
FROM canciones
WHERE puntuacion >= 9.0
ORDER BY puntuacion DESC, id ASC;

-- CONSULTA 3 (JOIN): reunir cada canción con el nombre de su género relacionado.
SELECT c.id, c.titulo, c.artista, g.nombre AS genero, c.duracion_segundos
FROM canciones AS c
INNER JOIN generos AS g ON c.genero_id = g.id
ORDER BY c.titulo;

-- CONSULTA 4: consultar el detalle de La Sunamita por su clave primaria.
SELECT c.*, g.nombre AS genero
FROM canciones AS c
INNER JOIN generos AS g ON c.genero_id = g.id
WHERE c.id = 1;
