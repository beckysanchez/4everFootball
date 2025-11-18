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

CREATE OR REPLACE VIEW vista_publicaciones_detalle AS
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

CREATE OR REPLACE VIEW vista_dashboard_usuarios AS
SELECT 
    u.usuario_id,
    u.nombre_completo,
    u.email,
    p1.nombre AS pais_nacimiento,
    p2.nombre AS nacionalidad,
    r.nombre AS rol,
  
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

CREATE OR REPLACE VIEW vista_publicaciones_con_recuentos AS
SELECT 
    p.publicacion_id,
    p.titulo,
    p.descripcion,
    u.nombre_completo AS autor,
    m.nombre_comunidad AS mundial,
    c.nombre AS categoria,

    -- Recuentos
    (SELECT COUNT(*) FROM comentario com WHERE com.publicacion_id = p.publicacion_id) AS total_comentarios,
    (SELECT COUNT(*) FROM reaccion r WHERE r.publicacion_id = p.publicacion_id) AS total_reacciones

FROM publicacion p
LEFT JOIN usuarios u ON u.usuario_id = p.usuario_id
LEFT JOIN mundial m ON m.mundial_id = p.mundial_id
LEFT JOIN categoria c ON c.categoria_id = p.categoria_id;

CREATE OR REPLACE VIEW vista_usuarios_actividad AS
SELECT 
    u.usuario_id,
    u.nombre_completo,
    u.email,
    r.nombre AS rol,

    COUNT(DISTINCT p.publicacion_id) AS publicaciones,
    COUNT(DISTINCT c.comentario_id) AS comentarios,
    COUNT(DISTINCT reac.reaccion_id) AS reacciones

FROM usuarios u
LEFT JOIN usuario_rol ur ON ur.usuario_id = u.usuario_id
LEFT JOIN roles r ON r.rol_id = ur.rol_id
LEFT JOIN publicacion p ON p.usuario_id = u.usuario_id
LEFT JOIN comentario c ON c.usuario_id = u.usuario_id
LEFT JOIN reaccion reac ON reac.usuario_id = u.usuario_id

GROUP BY u.usuario_id, u.nombre_completo, u.email, r.nombre;

CREATE OR REPLACE VIEW vista_mundiales_info AS
SELECT 
    m.mundial_id,
    m.nombre_comunidad,
    m.sede,
    m.portada_url,
    COUNT(DISTINCT ms.pais_id) AS total_sedes,
    COUNT(DISTINCT mp.seleccion_id) AS total_participantes
FROM mundial m
LEFT JOIN mundial_sede ms ON ms.mundial_id = m.mundial_id
LEFT JOIN mundial_participante mp ON mp.mundial_id = m.mundial_id
GROUP BY m.mundial_id, m.nombre_comunidad, m.sede, m.portada_url;




SHOW FULL TABLES IN mundial_reddit WHERE TABLE_TYPE = 'VIEW';
SHOW FULL TABLES IN mundial_reddit WHERE TABLE_TYPE = 'VIEW';