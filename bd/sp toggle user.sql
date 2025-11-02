/* toggle_usuario */

DELIMITER //

CREATE PROCEDURE sp_toggle_usuario (
    IN p_usuario_id INT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE v_nombre VARCHAR(150);

    -- Validar que el usuario exista
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario_id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El usuario especificado no existe.';
    END IF;

    -- Evitar desactivar al administrador principal
    IF p_activo = FALSE THEN
        IF EXISTS (
            SELECT 1 FROM usuario_rol ur
            JOIN roles r ON r.rol_id = ur.rol_id
            WHERE ur.usuario_id = p_usuario_id AND r.nombre = 'ADMIN'
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'No se puede desactivar una cuenta de administrador.';
        END IF;
    END IF;

    -- Actualizar estado
    UPDATE usuarios
    SET activo = p_activo,
        actualizado_en = CURRENT_TIMESTAMP
    WHERE usuario_id = p_usuario_id;

    SELECT CONCAT('Usuario actualizado correctamente (activo = ', p_activo, ')') AS mensaje;
END //

DELIMITER ;
