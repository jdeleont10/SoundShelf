-- SoundShelf: catálogo de música cristiana para Programación II.
-- Las descripciones y puntuaciones son textos originales del catálogo; no son letras de canciones.
-- Álbum, año, duración, portadas y enlaces: catálogo de Apple Music. Las portadas se cargan desde internet.
-- Son registros académicos almacenados en MySQL y consultados dinámicamente por PHP.
-- Hecho para MariaDB (la base de datos de XAMPP). Importar desde Inicio de phpMyAdmin.
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

-- Id de cada canción en Spotify, para el reproductor de la página.
-- Sale del enlace de la canción: open.spotify.com/track/<id>
-- "IF NOT EXISTS" deja volver a importar este archivo sin error (funciona en MariaDB, la base de XAMPP).
ALTER TABLE canciones ADD COLUMN IF NOT EXISTS spotify_id VARCHAR(22) NOT NULL DEFAULT '';

UPDATE canciones SET spotify_id = '5nmRDCoUya4XwQXCuQjzjm' WHERE id = 1;
UPDATE canciones SET spotify_id = '0U1vuPN1Qi8OO73f6SZglJ' WHERE id = 2;
UPDATE canciones SET spotify_id = '1AbSedPxXMKs3mALPjiNfM' WHERE id = 3;
UPDATE canciones SET spotify_id = '1ed3wNCecRIp6SugyR0w2J' WHERE id = 4;
UPDATE canciones SET spotify_id = '3foGcmDonUcTOCxtQZqZsb' WHERE id = 5;
UPDATE canciones SET spotify_id = '0TcZ7rMMo0WEU8iUgkbyEY' WHERE id = 6;
UPDATE canciones SET spotify_id = '1GvJ35amuk7oGjcnoYSKFA' WHERE id = 7;
UPDATE canciones SET spotify_id = '3xrjVH3MJedZc48OckezwS' WHERE id = 8;
UPDATE canciones SET spotify_id = '2JS2IF2YLPe4oLSIXpzZOK' WHERE id = 9;
UPDATE canciones SET spotify_id = '2FAYCnrTEJ2V0rS2i5PSi6' WHERE id = 10;
UPDATE canciones SET spotify_id = '0Bn1DSXfisvfKjGUwI6rzW' WHERE id = 11;
UPDATE canciones SET spotify_id = '7BY5JRklkQUu4e0tlZDBy7' WHERE id = 12;
UPDATE canciones SET spotify_id = '0pNizrRmvu5S16fH6ccm0v' WHERE id = 13;
UPDATE canciones SET spotify_id = '6WUJmzEMK7EBa2LLgp96s0' WHERE id = 14;
UPDATE canciones SET spotify_id = '0Q4svw5rQk5F31L7xorOUc' WHERE id = 15;
UPDATE canciones SET spotify_id = '05OD2x7K0pMZNLfFBUasmp' WHERE id = 16;
UPDATE canciones SET spotify_id = '0fsNha0jZp90cPvv2GCM9Z' WHERE id = 17;
UPDATE canciones SET spotify_id = '2dSiL2ArLakdep5bD2VuS5' WHERE id = 18;
UPDATE canciones SET spotify_id = '0vC82BouGPXm6X2K60RfQw' WHERE id = 19;
UPDATE canciones SET spotify_id = '1JL2sn4UvGr4elTouAHdFN' WHERE id = 20;

-- 40 canciones más (21 a 60). Los datos de título, año, duración y portada salen de Spotify.
INSERT IGNORE INTO canciones
    (id, genero_id, titulo, subtitulo, descripcion, artista, album, anio, duracion_segundos, puntuacion, imagen, enlace_apple, spotify_id)
VALUES
    (21, 1, 'Yo Soy la Ofrenda (Live)', 'Una entrega total en vivo.', 'Una canción en vivo de Montesanto que habla de entregarle todo a Dios como una ofrenda. Crece poco a poco y es ideal para momentos de alabanza en comunidad.', 'Montesanto', 'Nuestra Ofrenda (Live)', 2025, 639, 9.2, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b273ccbfff6aaee0105481781b87', '', '1YpVAxfdxxmBXeZw540xzV'),
    (22, 1, 'Praise', 'Una invitación a alabar con todo.', 'Una alabanza llena de energía de Elevation Worship junto a Brandon Lake, Chris Brown y Chandler Moore. Su mensaje es alabar a Dios en todo momento y se volvió una de las canciones más cantadas en las iglesias.', 'Elevation Worship, Brandon Lake, Chris Brown, Chandler Moore', 'Praise - EP', 2024, 305, 9.4, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27399bbbf9d819de8047df3bb1c', '', '1kvmCjy8Js6iWrHQDHAokY'),
    (23, 1, 'Battle Belongs', 'La batalla le pertenece a Dios.', 'Phil Wickham canta sobre confiar en Dios en medio de las dificultades. Empieza tranquila y termina con mucha fuerza.', 'Phil Wickham', 'Hymn of Heaven', 2021, 288, 9.1, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2737c40908823a648108d4b8dac', '', '5pF1L2TboIEiZQwHekySET'),
    (24, 1, 'Raise a Hallelujah', 'Alabar incluso en la dificultad.', 'Una canción de Bethel Music que nació en un momento difícil. Habla de alabar a Dios aun cuando las cosas no salen bien, con un ritmo que va creciendo.', 'Bethel Music, Jonathan David Helser, Melissa Helser', 'Raise a Hallelujah (Studio Version)', 2019, 244, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2732e4601f81296a4bf2c116d8f', '', '4LnHfZGEitmIVS17wndaUI'),
    (25, 1, 'Alive (Live)', 'Energía juvenil en vivo.', 'Una de las canciones más conocidas de Hillsong Young & Free, grabada en vivo. Tiene un sonido moderno y alegre pensado para jóvenes.', 'Hillsong Young & Free', 'We Are Young & Free (Live)', 2013, 233, 8.8, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273a63b608bbba60dba6b8cb3dd', '', '00HU5J2lFHDe4sHthe5lTq'),
    (26, 1, 'Enciende una Luz', 'Un clásico de la alabanza en español.', 'Una de las canciones más conocidas de Marcos Witt, uno de los pioneros de la música cristiana en español. Invita a ser luz en medio de la oscuridad.', 'Marcos Witt', 'Lo Mejor de Marcos Witt III', 2003, 299, 9.0, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b27378d5868ebf17b66c5a644bb1', '', '4Lg7fYkpYmNfJGw4Cf5zW5'),
    (27, 1, 'Gloria en lo Alto', 'Alabanza llena de alegría.', 'Una alabanza festiva de Christine D''Clario, del mismo álbum que Como Dijiste. Celebra la grandeza de Dios con un ritmo animado.', 'Christine D''Clario', 'De Vuelta al Jardín', 2012, 300, 8.9, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27372e1ca6495d65a09f9b69c92', '', '7x6tBBMlzv6OkHfYTYu3FO'),
    (28, 1, 'Mi Libertador', 'Celebración de la libertad en Cristo.', 'Una colaboración entre Miel San Marcos y Christine D''Clario. Celebra que Dios trae libertad, con la energía que caracteriza a Miel San Marcos.', 'Miel San Marcos, Christine D''Clario', 'Mi Libertador', 2020, 491, 9.3, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2732c9cc11bb8222c218a2dd06f', '', '6Mw27sYvigmxp0zDMGD2YN'),
    (29, 2, 'What a Beautiful Name', 'Una de las canciones de adoración más cantadas.', 'Ganó un premio Grammy y se canta en iglesias de todo el mundo. Habla del poder y la belleza del nombre de Jesús.', 'Hillsong Worship, Brooke Ligertwood', 'What a Beautiful Name', 2017, 240, 9.5, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273756031279f394f4db151063c', '', '7Lye5Pj3iMrCpHOdPM4xJz'),
    (30, 2, 'The Blessing (Live)', 'Una bendición cantada.', 'Está basada en la bendición de Números 6. Se grabó en vivo en 2020 y se hizo muy popular, con versiones en muchos idiomas.', 'Kari Jobe Carnes, Cody Carnes, Elevation Worship', 'The Blessing', 2020, 515, 9.4, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b27360e4e8ef24eea3ef1952233a', '', '5J6iGD1YeAXQFFi4zTPeBJ'),
    (31, 2, 'Renuévame (En Vivo)', 'Un clásico en una versión nueva.', 'Miel San Marcos y Marcos Witt cantan juntos este clásico de Marcos Witt. Es una oración corta para pedirle a Dios que renueve el corazón.', 'Miel San Marcos, Marcos Witt', 'EVANGELIO (En Vivo)', 2023, 168, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273814510b818515d3df96c4ca7', '', '6H259zTmtDMScX41kyqjel'),
    (32, 2, 'Te Daré lo Mejor', 'Entregarle a Dios lo mejor.', 'Una canción tranquila de Jesús Adrián Romero, uno de los cantautores cristianos más conocidos de México. Habla de darle a Dios lo mejor de uno.', 'Jesús Adrián Romero', 'Te Daré lo Mejor', 2017, 319, 9.1, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27326e010afa494ab6956ef2cdf', '', '4sEMb9c3lGsVnpkHNM1ebA'),
    (33, 2, 'Jireh', 'Dios es suficiente.', 'Jireh es uno de los nombres de Dios en la Biblia y significa que él provee. Es una canción larga y emotiva, grabada en vivo por dos grupos muy conocidos.', 'Elevation Worship, Maverick City Music, Chandler Moore, Naomi Raine', 'Old Church Basement', 2021, 599, 9.3, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273a996b44f11f30c8bf37cf53e', '', '1goiRWxiG3GTlODrdDZ7NR'),
    (34, 2, 'Believe For It', 'Fe para lo imposible.', 'CeCe Winans, una de las voces más premiadas del gospel, canta con Lauren Daigle sobre creer que Dios puede hacer lo imposible.', 'CeCe Winans, Lauren Daigle', 'Believe For It', 2021, 338, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2736d82d667a87e0a24fdc93535', '', '09VSG1KLDlanpc5o0TNRJE'),
    (35, 2, 'Ya No Soy Esclavo', 'Libre del miedo.', 'Es la versión en español de la canción No Longer Slaves. Habla de dejar atrás el miedo y saberse hijo de Dios.', 'Christine D''Clario', 'Emanuel', 2018, 317, 9.2, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273160fc5bdb59d0a10cd39fa86', '', '1q3KDl1xS0ldFbNZ2eVfvT'),
    (36, 2, 'Tú Proveerás', 'Confianza en la provisión de Dios.', 'Una canción de Majo y Dan con Christine D''Clario sobre confiar en que Dios provee en cada necesidad.', 'Majo y Dan, Christine D''Clario', 'Tú Proveerás', 2023, 368, 8.9, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273c689d964dc9794b7dfbce38b', '', '6nLdm3keaBYtW3PXkVQBQQ'),
    (37, 3, 'Can''t Steal My Joy', 'Una alegría que nadie quita.', 'Josiah Queen y Brandon Lake cantan sobre una alegría que no depende de las circunstancias. Mezcla guitarras acústicas con un coro fácil de cantar.', 'Josiah Queen, Brandon Lake', 'Mt. Zion', 2025, 214, 9.0, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b273538455e5e56e2725e885f54c', '', '3UZMfcbNTfMLvMqOK1PV4Q'),
    (38, 3, 'My Promised Land', 'Folk sobre la tierra prometida.', 'Del mismo álbum que The Prodigal. Josiah Queen usa la imagen de la tierra prometida para hablar de la esperanza en Dios.', 'Josiah Queen', 'The Prodigal', 2024, 195, 8.7, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273e58cec4bf807e528193065cf', '', '0sXSFQXGhkXzmdt07flWXl'),
    (39, 3, 'My Lighthouse', 'Dios como un faro.', 'Una canción alegre de Rend Collective, banda de Irlanda del Norte conocida por su folk con banjos y guitarras. Compara a Dios con un faro en medio de la tormenta.', 'Rend Collective', 'The Art of Celebration', 2014, 224, 9.1, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27381517cf28f00a3e0a30acc44', '', '09U8e6hRIneuizovCFzacQ'),
    (40, 3, 'Build Your Kingdom Here', 'Folk para construir comunidad.', 'Una de las primeras canciones conocidas de Rend Collective. Habla de que la iglesia sea un lugar de esperanza para su comunidad.', 'Rend Collective', 'Homemade Worship by Handmade People', 2012, 258, 8.8, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273b0b8ce9b025347e806095638', '', '1syLk0ow1xPP3EU2TzGYeg'),
    (41, 3, 'White Owl', 'Folk tranquilo y poético.', 'Josh Garrels es un cantautor independiente con un estilo folk muy personal. Esta canción tiene un sonido tranquilo y letras llenas de imágenes.', 'Josh Garrels', 'Love & War & The Sea In Between', 2011, 348, 8.6, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2737e6e79bd6d3f3b61269717e8', '', '1bqnR4LChgaQ2H4U0yipaS'),
    (42, 3, 'Is He Worthy?', 'Preguntas y respuestas de fe.', 'Andrew Peterson la escribió como una conversación de preguntas y respuestas basada en el libro de Apocalipsis. Se canta mucho en las iglesias.', 'Andrew Peterson', 'Is He Worthy?', 2018, 274, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273c742aefbe7b8e6b7f9780260', '', '5kZEQi54zLu0rfIbgk7YxC'),
    (43, 3, 'Find You Here', 'Esperanza en los días difíciles.', 'Ellie Holcomb, que ya está en el catálogo con I Will Carry You, canta sobre encontrar a Dios en los momentos difíciles.', 'Ellie Holcomb', 'Red Sea Road', 2017, 228, 8.7, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273d0aacb7f7ce80e32e600cc5e', '', '61D7vLaZ8mLDEUc2wgUyDE'),
    (44, 3, 'Old Church Choir', 'Folk con alma de country.', 'Zach Williams mezcla folk, country y rock en esta canción sobre la alegría que da la fe, como la de un coro de iglesia.', 'Zach Williams', 'Chain Breaker', 2016, 176, 8.8, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2730a2e6732217b085d39f22a9a', '', '2YyP3BkhmEUmcnlPhuXFrN'),
    (45, 4, 'El Nombre de Jesús', 'Rap y adoración juntos.', 'Redimi2 une el rap con la voz de Christine D''Clario en una canción que exalta el nombre de Jesús. Es una de sus colaboraciones más escuchadas.', 'Redimi2, Christine D''Clario', 'Operación Mundial', 2014, 270, 9.1, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27379df781bca454ba04306bf89', '', '3TV8XC0r6VPHSBLdR1Fi2v'),
    (46, 4, 'Espíritu Santo', 'Urbano con mensaje de adoración.', 'Redimi2 y el grupo Barak cantan sobre la presencia del Espíritu Santo, mezclando rap y coros de adoración.', 'Redimi2, Barak', 'Pura Sal', 2017, 278, 8.9, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27336eefb0d387410574edc3914', '', '1n5re2pJhPGXh1iNC81s5H'),
    (47, 4, 'Pentagrama', 'Reggaetón cristiano moderno.', 'Alex Zurdo, que ya está en el catálogo con Guarda Tu Corazón, usa la imagen del pentagrama musical para hablar de la vida con Dios.', 'Alex Zurdo', 'DTOX', 2022, 197, 8.7, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27323729fc255fee65292836b64', '', '1DofNh9ulHsQkybB81XMPj'),
    (48, 4, 'A Pesar de Mí', 'Seis artistas en una canción.', 'Una gran colaboración del género urbano cristiano. Habla del amor de Dios, que no depende de lo que uno haga.', 'Alex Zurdo, Funky, Redimi2, Un Corazón, Abby Valdez, Indiomar', 'UNO', 2021, 320, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273ed9a2719f60c7e18ee01dab5', '', '19NHUhKO0q6Z2wX6yyGpap'),
    (49, 4, 'Eres Mi Bendición', 'Gratitud con ritmo urbano.', 'Funky y Alex Zurdo le agradecen a Dios sus bendiciones en una canción con ritmo de reggaetón.', 'Funky, Alex Zurdo', 'Indestructible', 2015, 240, 8.6, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b27391028c3b6463e84009b8ee01', '', '3lMZAnQgm5Sjr6KDR0uZNw'),
    (50, 4, 'Blessings', 'Hip hop sobre la gratitud.', 'Lecrae es uno de los raperos cristianos más conocidos de Estados Unidos. En esta canción habla de reconocer las bendiciones de Dios.', 'Lecrae, Ty Dolla $ign', 'All Things Work Together', 2017, 231, 8.8, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273344fbb002886a31c9ba1de34', '', '0waAsYpoKrgP286vYFAshE'),
    (51, 4, 'You Can''t Stop Me', 'Rap sobre la perseverancia.', 'Andy Mineo, del sello Reach Records, rapea sobre seguir adelante a pesar de los obstáculos.', 'Andy Mineo', 'You Can''t Stop Me', 2014, 240, 8.5, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2732fa5696156a7ffa6bccac6fe', '', '6a4twJ0UQyz5pm4WJjvuIm'),
    (52, 4, 'Alabaré', 'Una alabanza con ritmo urbano.', 'Una colaboración reciente entre Nacho, Redimi2 y Alex Zurdo que convierte la alabanza en una canción con ritmo urbano para bailar.', 'Nacho, Redimi2, Alex Zurdo', 'Sobre la Roca', 2025, 178, 8.9, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2737eac3b9377fa31fb2ceaee8c', '', '5DSFBAHNPyjghnMBZCJ8RN'),
    (53, 5, 'Your Way''s Better', 'Pop alegre sobre confiar en Dios.', 'Del mismo artista de GOOD DAY. Forrest Frank canta, con un ritmo relajado, que el camino de Dios es mejor que el propio.', 'Forrest Frank', 'CHILD OF GOD (DELUXE)', 2024, 198, 8.9, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b273c7d6942542cc33eb5fedbb30', '', '7JDfWHxOFo63yQmVs5wSPM'),
    (54, 5, 'Indudablemente', 'Rock argentino con mensaje.', 'Una canción de Rescate, la banda argentina que ya está en el catálogo con ¿Sabes?. Tiene el estilo de rock que los hizo conocidos.', 'Rescate', 'Indudablemente', 2014, 231, 8.6, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b2733b03818cae41addd2c87eecf', '', '2423N8ZVbFoRnRJVAlPryQ'),
    (55, 5, 'God Only Knows', 'Esperanza para los que sufren.', 'El dúo australiano for KING & COUNTRY canta sobre que Dios conoce lo que cada persona está viviendo, aunque nadie más lo sepa.', 'for KING & COUNTRY', 'God Only Knows', 2018, 230, 9.2, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b27367c27ed759aa817f70aa1b6d', '', '1vI0q3cnFFfzTj9WT9kFEH'),
    (56, 5, 'You Say', 'Lo que Dios dice de ti.', 'Una de las canciones cristianas más exitosas de los últimos años. Lauren Daigle canta sobre dejar de creer las inseguridades y escuchar lo que Dios dice.', 'Lauren Daigle', 'Look Up Child', 2018, 275, 9.4, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273152dff2321bbb3c4fbb757bb', '', '6Up545NUflOiXo8cEraH49'),
    (57, 5, 'Who Am I', 'Un clásico del pop rock cristiano.', 'La canción que dio a conocer a Casting Crowns. Se pregunta quién es uno para que Dios lo ame tanto.', 'Casting Crowns', 'Casting Crowns', 2003, 336, 9.0, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b273b6b4afa5bec4ca0e9e586e2f', '', '5VUQsLff8A3ruAyCdTxqzg'),
    (58, 5, 'Hero', 'Rock fuerte con mensaje.', 'Skillet es una de las bandas de rock cristiano más famosas. Hero es una de sus canciones más conocidas, con guitarras fuertes.', 'Skillet', 'Awake', 2009, 187, 8.8, 'https://image-cdn-ak.spotifycdn.com/image/ab67616d0000b27389f8d16ffe15fccfa0a7e31d', '', '4CbKVDZkYKdv69I4bCaKUq'),
    (59, 5, 'Al Taller del Maestro', 'Un clásico en una nueva versión.', 'Nueva versión de la canción más conocida de Alex Campos, ahora con Jesús Adrián Romero y Lilly Goodman. Habla de dejar que Dios repare el corazón.', 'Alex Campos, Jesús Adrián Romero, Lilly Goodman', 'Al Taller del Maestro', 2022, 325, 9.1, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b2737d8ea59453720d870624b0e5', '', '7Eyk7Fpn04yhOYppojynnA'),
    (60, 5, 'Yo Seré Tu Sol', 'Pop y rap en una misma canción.', 'Una mezcla del rap de Redimi2 con las voces de Tercer Cielo, uno de los grupos de pop cristiano más conocidos en español.', 'Redimi2, Tercer Cielo', 'Phenomenon Edition', 2009, 212, 8.7, 'https://image-cdn-fa.spotifycdn.com/image/ab67616d0000b273c26d6c55532f7d353d2a82c9', '', '1BuEp7Gt4hdzKwNaZIxMfI');

-- Enlace de Apple Music de las canciones 21 a 60 (las 20 primeras ya lo traen en su INSERT).
-- Se eligió en cada caso la versión que dura lo mismo que la canción guardada.
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/yo-soy-la-ofrenda-live/1852606136?i=1852606137' WHERE id = 21;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/praise-feat-brandon-lake-chris-brown-chandler-moore/1731471415?i=1731471423' WHERE id = 22;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/battle-belongs/1566041335?i=1566041336' WHERE id = 23;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/raise-a-hallelujah-studio-version/1747841331?i=1747841332' WHERE id = 24;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/alive-live/1829981085?i=1829981099' WHERE id = 25;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/enciende-una-luz/1398679239?i=1398679240' WHERE id = 26;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/gloria-en-lo-alto/1321567767?i=1321568111' WHERE id = 27;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/mi-libertador/1515559441?i=1515559442' WHERE id = 28;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/what-a-beautiful-name-feat-hillsong-young-free/1786284955?i=1786284966' WHERE id = 29;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/the-blessing-live/1503421628?i=1503421630' WHERE id = 30;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/renue-vame-feat-marcos-witt-en-vivo/6799164268?i=6799164713' WHERE id = 31;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/te-dar%C3%A9-lo-mejor-en-vivo/1603412053?i=1603412378' WHERE id = 32;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/jireh-feat-chandler-moore-naomi-raine/1557087895?i=1557087898' WHERE id = 33;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/believe-for-it/1583644927?i=1583644930' WHERE id = 34;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/ya-no-soy-esclavo-feat-julio-melgar/1424583125?i=1424584335' WHERE id = 35;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/t%C3%BA-proveer%C3%A1s/1677845465?i=1677845663' WHERE id = 36;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/cant-steal-my-joy-feat-brandon-lake/1818407230?i=1818407508' WHERE id = 37;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/my-promised-land/1779547999?i=1779548023' WHERE id = 38;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/my-lighthouse/1792039779?i=1792039786' WHERE id = 39;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/build-your-kingdom-here/1794637147?i=1794637153' WHERE id = 40;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/white-owl/1776014623?i=1776014626' WHERE id = 41;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/is-he-worthy/1560396233?i=1560396835' WHERE id = 42;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/find-you-here/1192615067?i=1192615167' WHERE id = 43;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/old-church-choir/1282111719?i=1282112387' WHERE id = 44;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/el-nombre-de-jes%C3%BAs/1823787454?i=1823787462' WHERE id = 45;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/esp%C3%ADritu-santo/1823785637?i=1823786014' WHERE id = 46;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/pentagrama/1685959184?i=1685959399' WHERE id = 47;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/a-pesar-de-m%C3%AD-feat-un-coraz%C3%B3n-abby-valdez-indiomar/1656783120?i=1656784849' WHERE id = 48;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/eres-mi-bendici%C3%B3n-feat-alex-zurdo/1041052528?i=1041052532' WHERE id = 49;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/blessings-feat-ty-dolla-%24ign/1812456438?i=1812456832' WHERE id = 50;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/you-cant-stop-me/1533128905?i=1533129591' WHERE id = 51;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/alabar%C3%A9/1805178951?i=1805179708' WHERE id = 52;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/your-ways-better/1800088490?i=1800088968' WHERE id = 53;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/indudablemente/906478006?i=906478024' WHERE id = 54;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/god-only-knows/1398353335?i=1398353598' WHERE id = 55;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/you-say/1447184494?i=1447184844' WHERE id = 56;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/who-am-i/269440887?i=269441279' WHERE id = 57;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/hero/668424099?i=668424100' WHERE id = 58;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/al-taller-del-maestro/1888262252?i=1888262253' WHERE id = 59;
UPDATE canciones SET enlace_apple = 'https://music.apple.com/us/album/yo-ser%C3%A9-tu-sol-feat-tercer-cielo/1823785109?i=1823785116' WHERE id = 60;

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
