-- Base de datos "El Hogar de los Cuentos" (XAMPP: MySQL/MariaDB, phpMyAdmin > Importar)
CREATE DATABASE IF NOT EXISTS hogar_cuentos CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE hogar_cuentos;

CREATE TABLE IF NOT EXISTS usuarios_tecnicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('tecnico','admin') NOT NULL DEFAULT 'tecnico',
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cuentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  autor VARCHAR(120) NOT NULL DEFAULT '',
  categoria VARCHAR(80) NOT NULL DEFAULT '',
  edad_recomendada VARCHAR(20) NOT NULL DEFAULT '',
  idioma VARCHAR(10) NOT NULL DEFAULT 'es',
  nivel_lectura VARCHAR(30) NOT NULL DEFAULT '',
  duracion_min INT NOT NULL DEFAULT 0,
  vistas INT NOT NULL DEFAULT 0,
  fecha_carga DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_tecnico INT NULL,
  FOREIGN KEY (id_tecnico) REFERENCES usuarios_tecnicos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Cada fragmento (párrafo) del cuento. La sincronización es en segundos exactos (se muestra como min:seg).
CREATE TABLE IF NOT EXISTS cuentos_texto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cuento INT NOT NULL,
  contenido TEXT NOT NULL,
  fragmento_orden INT NOT NULL,
  segundo_inicio INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_cuento) REFERENCES cuentos(id) ON DELETE CASCADE,
  KEY (id_cuento, fragmento_orden)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cuentos_audio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cuento INT NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  origen ENUM('subido','generado_ia') NOT NULL DEFAULT 'subido',
  duracion_seg INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_cuento) REFERENCES cuentos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- La imagen se muestra desde su segundo_inicio hasta la siguiente imagen.
CREATE TABLE IF NOT EXISTS cuentos_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cuento INT NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  origen ENUM('subido','generado_ia') NOT NULL DEFAULT 'subido',
  texto_alternativo VARCHAR(500) NOT NULL,
  segundo_inicio INT NOT NULL DEFAULT 0,
  orden_escena INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_cuento) REFERENCES cuentos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS preguntas_ia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cuento INT NOT NULL,
  tipo ENUM('literal','inferencial','reflexiva') NOT NULL,
  pregunta TEXT NOT NULL,
  FOREIGN KEY (id_cuento) REFERENCES cuentos(id) ON DELETE CASCADE,
  UNIQUE KEY (id_cuento, tipo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS historial_busquedas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_sesion VARCHAR(64) NOT NULL,
  termino VARCHAR(200) NOT NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY (id_sesion)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS intentos_login (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45) NOT NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY (ip, fecha)
) ENGINE=InnoDB;
