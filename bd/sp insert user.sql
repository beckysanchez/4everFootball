DROP PROCEDURE IF EXISTS sp_crear_usuario;
DELIMITER //

CREATE PROCEDURE sp_crear_usuario (
    IN p_nombre_completo VARCHAR(150),
    IN p_fecha_nacimiento DATE,
    IN p_genero VARCHAR(10),
    IN p_pais_nacimiento VARCHAR(100),
    IN p_nacionalidad VARCHAR(100),
    IN p_email VARCHAR(120),
    IN p_password_hash VARCHAR(255),
    IN p_foto LONGBLOB
)
BEGIN
    DECLARE v_pais_nacimiento_id INT;
    DECLARE v_nacionalidad_id INT;
    DECLARE v_usuario_id INT;
    DECLARE v_rol_id INT;

    IF EXISTS (SELECT 1 FROM usuarios WHERE email = p_email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El correo electrónico ya está registrado.';
    END IF;

    SELECT pais_id INTO v_pais_nacimiento_id
    FROM pais WHERE nombre = p_pais_nacimiento
    LIMIT 1;
    IF v_pais_nacimiento_id IS NULL THEN
        INSERT INTO pais (nombre) VALUES (p_pais_nacimiento);
        SET v_pais_nacimiento_id = LAST_INSERT_ID();
    END IF;

    SELECT pais_id INTO v_nacionalidad_id
    FROM pais WHERE nombre = p_nacionalidad
    LIMIT 1;
    IF v_nacionalidad_id IS NULL THEN
        INSERT INTO pais (nombre) VALUES (p_nacionalidad);
        SET v_nacionalidad_id = LAST_INSERT_ID();
    END IF;

    IF p_foto IS NOT NULL THEN
        INSERT INTO usuarios (
            nombre_completo, fecha_nacimiento, genero,
            pais_nacimiento_id, nacionalidad_id, email, password_hash,
            foto_blob, activo
        ) VALUES (
            p_nombre_completo, p_fecha_nacimiento, p_genero,
            v_pais_nacimiento_id, v_nacionalidad_id, p_email, p_password_hash,
            p_foto, 1
        );
    ELSE
        INSERT INTO usuarios (
            nombre_completo, fecha_nacimiento, genero,
            pais_nacimiento_id, nacionalidad_id, email, password_hash, activo
        ) VALUES (
            p_nombre_completo, p_fecha_nacimiento, p_genero,
            v_pais_nacimiento_id, v_nacionalidad_id, p_email, p_password_hash, 1
        );
    END IF;

    SET v_usuario_id = LAST_INSERT_ID();

    SELECT rol_id INTO v_rol_id FROM roles WHERE nombre = 'USUARIO' LIMIT 1;
    IF v_rol_id IS NULL THEN
        INSERT INTO roles (nombre) VALUES ('USUARIO');
        SET v_rol_id = LAST_INSERT_ID();
    END IF;

    INSERT INTO usuario_rol (usuario_id, rol_id)
    VALUES (v_usuario_id, v_rol_id);

    SELECT 'Usuario creado exitosamente' AS mensaje,
           v_usuario_id AS nuevo_usuario_id;
END //

DELIMITER ;
