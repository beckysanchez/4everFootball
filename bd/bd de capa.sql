CREATE DATABASE mundial_reddit;
USE mundial_reddit;

CREATE TABLE pais (
  pais_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE usuarios (
  usuario_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_completo VARCHAR(150) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  genero ENUM('Masculino','Femenino','Otro') DEFAULT 'Otro',
  pais_nacimiento_id INT,
  nacionalidad_id INT,
  email VARCHAR(120) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  foto_blob LONGBLOB,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  activo BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (pais_nacimiento_id) REFERENCES pais(pais_id),
  FOREIGN KEY (nacionalidad_id) REFERENCES pais(pais_id)
);

CREATE TABLE roles (
  rol_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre ENUM('ADMIN','USUARIO') UNIQUE NOT NULL
);

CREATE TABLE usuario_rol (
  usuario_id INT NOT NULL,
  rol_id INT NOT NULL,
  PRIMARY KEY (usuario_id, rol_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
  FOREIGN KEY (rol_id) REFERENCES roles(rol_id)
);

CREATE TABLE mundial (
  mundial_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_oficial VARCHAR(150) NOT NULL,
  anio YEAR UNIQUE NOT NULL,
  resena TEXT,
  logo_blob LONGBLOB,
  imagen_portada_blob LONGBLOB,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mundial_sede (
  mundial_id INT NOT NULL,
  pais_id INT NOT NULL,
  PRIMARY KEY (mundial_id, pais_id),
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id),
  FOREIGN KEY (pais_id) REFERENCES pais(pais_id)
);

CREATE TABLE seleccion (
  seleccion_id INT AUTO_INCREMENT PRIMARY KEY,
  pais_id INT NOT NULL,
  apodo VARCHAR(100),
  FOREIGN KEY (pais_id) REFERENCES pais(pais_id)
);

CREATE TABLE mundial_participante (
  mundial_id INT NOT NULL,
  seleccion_id INT NOT NULL,
  PRIMARY KEY (mundial_id, seleccion_id),
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id),
  FOREIGN KEY (seleccion_id) REFERENCES seleccion(seleccion_id)
);

CREATE TABLE categoria (
  categoria_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) UNIQUE NOT NULL,
  descripcion VARCHAR(255)
);

CREATE TABLE publicacion (
  publicacion_id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mundial_id INT NOT NULL,
  categoria_id INT NOT NULL,
  seleccion_id INT NULL,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  tipo_media ENUM('IMAGEN','VIDEO','LINK') DEFAULT 'IMAGEN',
  imagen_blob LONGBLOB,
  video_blob LONGBLOB,
  media_url VARCHAR(255),
  estatus ENUM('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  creada_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  aprobada_en DATETIME NULL,
  aprobada_por INT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id),
  FOREIGN KEY (categoria_id) REFERENCES categoria(categoria_id),
  FOREIGN KEY (seleccion_id) REFERENCES seleccion(seleccion_id),
  FOREIGN KEY (aprobada_por) REFERENCES usuarios(usuario_id)
);

CREATE TABLE comentario (
  comentario_id INT AUTO_INCREMENT PRIMARY KEY,
  publicacion_id INT NOT NULL,
  usuario_id INT NOT NULL,
  contenido TEXT NOT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  eliminado BOOLEAN DEFAULT FALSE,
  eliminado_por INT NULL,
  FOREIGN KEY (publicacion_id) REFERENCES publicacion(publicacion_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
  FOREIGN KEY (eliminado_por) REFERENCES usuarios(usuario_id)
);

CREATE TABLE reaccion (
  reaccion_id INT AUTO_INCREMENT PRIMARY KEY,
  publicacion_id INT NOT NULL,
  usuario_id INT NOT NULL,
  tipo ENUM('LIKE','ESTRELLA') NOT NULL,
  valor INT NULL CHECK (valor BETWEEN 1 AND 5 OR valor IS NULL),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (publicacion_id, usuario_id, tipo),
  FOREIGN KEY (publicacion_id) REFERENCES publicacion(publicacion_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);

CREATE TABLE vista_publicacion (
  vista_id INT AUTO_INCREMENT PRIMARY KEY,
  publicacion_id INT NOT NULL,
  usuario_id INT NULL,
  visto_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (publicacion_id) REFERENCES publicacion(publicacion_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);

INSERT INTO roles (nombre) VALUES ('ADMIN'), ('USUARIO');

INSERT INTO categoria (nombre, descripcion) VALUES
('Jugadas', 'Jugadas destacadas o icónicas'),
('Entrevistas', 'Charlas con jugadores o entrenadores'),
('Partidos', 'Información sobre partidos específicos'),
('Estadísticas', 'Datos numéricos de mundiales o jugadores'),
('Sedes', 'Ciudades o países anfitriones'),
('Cultura', 'Aspectos culturales del mundial'),
('Incidentes', 'Hechos polémicos o curiosos');
