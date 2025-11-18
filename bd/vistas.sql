-- =====================================
-- VISTA: vista_usuarios_pais_rol
-- Muestra usuarios con su país y rol
-- =====================================

CREATE OR REPLACE VIEW vista_usuarios_pais_rol AS
SELECT 
    u.usuario_id,
    u.nombre_completo,
    u.fecha_nacimiento,
    u.genero,
    p1.nombre AS pais_nacimiento,
    p2.nombre AS nacionalidad,
    r.nombre AS rol
FROM usuarios u
LEFT JOIN pais p1 ON u.pais_nacimiento_id = p1.pais_id
LEFT JOIN pais p2 ON u.nacionalidad_id = p2.pais_id
LEFT JOIN usuario_rol ur ON ur.usuario_id = u.usuario_id
LEFT JOIN roles r ON ur.rol_id = r.rol_id;


-- =====================================
-- VISTA: vista_publicaciones_detalle
-- Muestra publicaciones con información extendida
-- =====================================

CREATE OR REPLACE VIEW vista_publicaciones_detalle AS
SELECT 
    p.publicacion_id,
    p.titulo,
    p.descripcion,
    p.tipo_media,
    p.media_url,
    p.estatus,
    p.creada_en,
    p.views, 
    u.nombre_completo AS autor,
    c.nombre AS categoria,
    m.nombre_comunidad AS mundial,
    s.apodo AS seleccion

FROM publicacion p
LEFT JOIN usuarios u   ON p.usuario_id = u.usuario_id
LEFT JOIN categoria c  ON p.categoria_id = c.categoria_id
LEFT JOIN mundial m    ON p.mundial_id = m.mundial_id
LEFT JOIN seleccion s  ON p.seleccion_id = s.seleccion_id;


-- la siguiente vista se supone que es para los comentarios
CREATE OR REPLACE VIEW vista_comentarios_detalle AS
SELECT 
    c.comentario_id,
    c.contenido,
    c.creado_en,
    u_com.nombre_completo AS autor_comentario,
    p.publicacion_id,
    p.titulo AS publicacion_titulo,
    u_pub.nombre_completo AS autor_publicacion
FROM comentario c
LEFT JOIN usuarios u_com ON c.usuario_id = u_com.usuario_id
LEFT JOIN publicacion p ON c.publicacion_id = p.publicacion_id
LEFT JOIN usuarios u_pub ON p.usuario_id = u_pub.usuario_id
WHERE c.eliminado = FALSE;

-- la siguiente es para las reacciones

CREATE OR REPLACE VIEW vista_reacciones_detalle AS
SELECT 
    r.reaccion_id,
    r.tipo,
    r.valor,
    r.creado_en,
    u_reac.nombre_completo AS usuario_reaccion,
    p.publicacion_id,
    p.titulo AS publicacion_titulo,
    u_pub.nombre_completo AS autor_publicacion
FROM reaccion r
LEFT JOIN usuarios u_reac ON r.usuario_id = u_reac.usuario_id
LEFT JOIN publicacion p ON r.publicacion_id = p.publicacion_id
LEFT JOIN usuarios u_pub ON p.usuario_id = u_pub.usuario_id;

-- la siguiente es para ver los datos de usuarios de locos

CREATE OR REPLACE VIEW vista_dashboard_usuarios AS
SELECT 
    u.usuario_id,
    u.nombre_completo,
    u.email,
    p1.nombre AS pais_nacimiento,
    p2.nombre AS nacionalidad,
    r.nombre AS rol,
    
    -- métricas
    COUNT(DISTINCT pub.publicacion_id) AS total_publicaciones,
    COUNT(DISTINCT com.comentario_id) AS total_comentarios,
    COUNT(DISTINCT reac.reaccion_id) AS reacciones_realizadas,
    COUNT(DISTINCT reac_pub.reaccion_id) AS reacciones_recibidas

FROM usuarios u
LEFT JOIN pais p1 ON u.pais_nacimiento_id = p1.pais_id
LEFT JOIN pais p2 ON u.nacionalidad_id = p2.pais_id
LEFT JOIN usuario_rol ur ON ur.usuario_id = u.usuario_id
LEFT JOIN roles r ON ur.rol_id = r.rol_id
LEFT JOIN publicacion pub ON pub.usuario_id = u.usuario_id
LEFT JOIN comentario com ON com.usuario_id = u.usuario_id
LEFT JOIN reaccion reac ON reac.usuario_id = u.usuario_id
LEFT JOIN publicacion pub2 ON pub2.usuario_id = u.usuario_id
LEFT JOIN reaccion reac_pub ON reac_pub.publicacion_id = pub2.publicacion_id

GROUP BY u.usuario_id, u.nombre_completo, u.email, p1.nombre, p2.nombre, r.nombre;



-- verifica las vistas
SHOW FULL TABLES IN mundial_reddit WHERE TABLE_TYPE = 'VIEW';
SHOW FULL TABLES IN mundial_reddit WHERE TABLE_TYPE = 'VIEW';



