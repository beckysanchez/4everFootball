-- =====================================
-- PROCEDIMIENTO: sp_crear_comentario
-- =====================================

DELIMITER //

CREATE PROCEDURE sp_crear_comentario(
    IN p_usuario_id INT,
    IN p_publicacion_id INT,
    IN p_contenido TEXT
)
BEGIN
    -- Validar que el usuario exista
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario_id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no existe';
    END IF;

    -- Validar que la publicación exista
    IF NOT EXISTS (SELECT 1 FROM publicacion WHERE publicacion_id = p_publicacion_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La publicación no existe';
    END IF;

    -- Insertar comentario
    INSERT INTO comentario (usuario_id, publicacion_id, contenido, creado_en)
    VALUES (p_usuario_id, p_publicacion_id, p_contenido, NOW());

    SELECT 'Comentario agregado exitosamente' AS mensaje,
           LAST_INSERT_ID() AS nuevo_comentario_id;
END //

DELIMITER ;

-- lo sig es solo para verificarlo
SHOW PROCEDURE STATUS WHERE Db = 'mundial_reddit';

