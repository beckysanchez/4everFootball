ALTER TABLE comentario ADD COLUMN comentario_padre_id INT NULL AFTER comentario_id;
ALTER TABLE comentario
  ADD FOREIGN KEY (comentario_padre_id) REFERENCES comentario(comentario_id)
  ON DELETE CASCADE;
USE mundial_reddit;

CREATE TABLE comentario_reaccion (
  reaccion_id INT AUTO_INCREMENT PRIMARY KEY,
  comentario_id INT NOT NULL,
  usuario_id INT NOT NULL,
  tipo ENUM('LIKE') DEFAULT 'LIKE',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (comentario_id, usuario_id),
  FOREIGN KEY (comentario_id) REFERENCES comentario(comentario_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);


DROP VIEW IF EXISTS vista_publicaciones_detalle;


CREATE SQL SECURITY INVOKER VIEW vista_publicaciones_detalle AS
SELECT 
    p.publicacion_id,
    p.titulo,
    p.descripcion,
    p.tipo_media,
    p.media_url,
    p.estatus,
    p.creada_en,
    u.nombre_completo AS autor,
    c.nombre AS categoria,
    m.nombre_comunidad AS mundial,
    s.apodo AS seleccion
FROM publicacion p
LEFT JOIN usuarios u ON p.usuario_id = u.usuario_id
LEFT JOIN categoria c ON p.categoria_id = c.categoria_id
LEFT JOIN mundial m ON p.mundial_id = m.mundial_id
LEFT JOIN seleccion s ON p.seleccion_id = s.seleccion_id;

DESCRIBE mundial;

DROP VIEW IF EXISTS vista_publicaciones_detalle;

CREATE SQL SECURITY INVOKER VIEW vista_publicaciones_detalle AS
SELECT 
    p.publicacion_id,
    p.titulo,
    p.descripcion,
    p.tipo_media,
    p.media_url,
    p.estatus,
    p.creada_en,

    -- Autor
    u.nombre_completo AS autor,

    -- Categoría
    c.nombre AS categoria,

    -- Mundial / Comunidad
    m.nombre_comunidad AS mundial,
    m.sede AS seleccion

FROM publicacion p
LEFT JOIN usuarios u      ON p.usuario_id = u.usuario_id
LEFT JOIN categoria c     ON p.categoria_id = c.categoria_id
LEFT JOIN mundial m       ON p.mundial_id = m.mundial_id
LEFT JOIN seleccion s     ON p.seleccion_id = s.seleccion_id;


CREATE TABLE usuario_mundial_seguido (
    usuario_id INT NOT NULL,
    mundial_id INT NOT NULL,
    PRIMARY KEY (usuario_id, mundial_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
    FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id)
);


CREATE TABLE IF NOT EXISTS usuario_mundial_seguido (
    usuario_id INT NOT NULL,
    mundial_id INT NOT NULL,
    PRIMARY KEY (usuario_id, mundial_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
    FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id)
);


