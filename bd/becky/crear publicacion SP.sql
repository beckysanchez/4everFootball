DELIMITER //

CREATE PROCEDURE sp_crear_publicacion(
    IN p_usuario_id INT,
    IN p_mundial_id INT,
    IN p_categoria_id INT,
    IN p_titulo VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_tipo_media VARCHAR(10),
    IN p_media_url VARCHAR(255)
)
BEGIN
    -- Validar que el usuario exista
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario_id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no existe';
    END IF;

    -- Insertar publicación
    INSERT INTO publicacion (
        usuario_id, mundial_id, categoria_id, titulo, descripcion, tipo_media, media_url, creada_en
    )
    VALUES (
        p_usuario_id, p_mundial_id, p_categoria_id, p_titulo, p_descripcion, p_tipo_media, p_media_url, NOW()
    );

    SELECT 'Publicación creada exitosamente' AS mensaje, LAST_INSERT_ID() AS nueva_publicacion_id;
END //

DELIMITER ;

-- el siguiente es para verificar
SHOW PROCEDURE STATUS WHERE Db = 'mundial_reddit';

