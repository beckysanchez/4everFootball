
CREATE INDEX idx_publicacion_mundial_categoria
ON publicacion (mundial_id, categoria_id);

CREATE INDEX idx_publicacion_estatus_aprobada
ON publicacion (estatus, aprobada_en);

CREATE INDEX idx_publicacion_filtros
ON publicacion (anio, categoria_id, usuario_id);

CREATE INDEX idx_reaccion_publicacion_tipo
ON reaccion (publicacion_id, tipo);

CREATE INDEX idx_comentario_publicacion
ON comentario (publicacion_id);

ALTER TABLE usuario_rol
DROP PRIMARY KEY,
ADD PRIMARY KEY (usuario_id, rol_id);

CREATE INDEX idx_usuarios_activo
ON usuarios (activo);

CREATE INDEX idx_usuarios_creado_en
ON usuarios (creado_en);


