-- Importar en phpMyAdmin una sola vez; no borra canciones, usuarios ni favoritos previos.
USE soundshelf;
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  correo VARCHAR(190) NOT NULL UNIQUE,
  clave_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Favoritos de cada usuario (relación muchos a muchos entre usuarios y canciones).
CREATE TABLE IF NOT EXISTS favoritos (
  usuario_id INT UNSIGNED NOT NULL,
  cancion_id INT UNSIGNED NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (usuario_id, cancion_id),
  CONSTRAINT fk_favoritos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_favoritos_cancion FOREIGN KEY (cancion_id) REFERENCES canciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Intentos fallidos de inicio de sesión: después de 5 en 15 minutos se bloquea ese correo.
-- Es una tabla de control: se vacía sola (al entrar bien o después de un día).
CREATE TABLE IF NOT EXISTS intentos_login (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  correo VARCHAR(190) NOT NULL,
  ip VARCHAR(45) NOT NULL DEFAULT '',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_intentos_correo (correo, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo: 5 usuarios (contraseña de todos: Ejemplo123!) y 10 favoritos.
-- INSERT IGNORE no duplica nada si se vuelve a importar.
INSERT IGNORE INTO usuarios (nombre, correo, clave_hash) VALUES
  ('María López', 'maria@ejemplo.test', '$2y$10$DH2woRrkXTMXttKWZMkjnOyxO3m6o/e6hjluwOChwiYdeUWWQofem'),
  ('Carlos Pérez', 'carlos@ejemplo.test', '$2y$10$0Cb9G6gGmpCP6KvFfpGOTOuw3Ft3v.cqqAYEy.uJ2A4Y6mK8kNNdC'),
  ('Ana Gómez', 'ana@ejemplo.test', '$2y$10$lzjr0ByoxsYGbIUnmMdoWuvaS0jJE9TQGfxvoqrbz3wiWr6x4j5gS'),
  ('Luis Martínez', 'luis@ejemplo.test', '$2y$10$dujEg465wApc0PC0Z4mSY.Vzyr1utvz7gWl3nHX2VaZebGqC7dImy'),
  ('Sofía Ramírez', 'sofia@ejemplo.test', '$2y$10$dKnRYuLVaumDJqNVr1QKtundx9OOe7dXxW0g28VKBkr.G23KNR4lW');

-- Los favoritos se enlazan por correo, así funcionan aunque cambien los id.
INSERT IGNORE INTO favoritos (usuario_id, cancion_id)
SELECT u.id, f.cancion_id
FROM usuarios AS u
INNER JOIN (
  SELECT 'maria@ejemplo.test' AS correo, 4 AS cancion_id UNION ALL
  SELECT 'maria@ejemplo.test', 11 UNION ALL
  SELECT 'carlos@ejemplo.test', 8 UNION ALL
  SELECT 'carlos@ejemplo.test', 17 UNION ALL
  SELECT 'ana@ejemplo.test', 1 UNION ALL
  SELECT 'ana@ejemplo.test', 2 UNION ALL
  SELECT 'luis@ejemplo.test', 3 UNION ALL
  SELECT 'luis@ejemplo.test', 15 UNION ALL
  SELECT 'sofia@ejemplo.test', 19 UNION ALL
  SELECT 'sofia@ejemplo.test', 9
) AS f ON f.correo = u.correo;
