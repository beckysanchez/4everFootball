ALTER TABLE comentario ADD COLUMN comentario_padre_id INT NULL AFTER comentario_id;
ALTER TABLE comentario
  ADD FOREIGN KEY (comentario_padre_id) REFERENCES comentario(comentario_id)
  ON DELETE CASCADE;


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
