DELIMITER //

CREATE PROCEDURE sp_crear_usuario (
    IN p_nombre_completo VARCHAR(150),
    IN p_fecha_nacimiento DATE,
    IN p_genero VARCHAR(10),
    IN p_pais_nacimiento VARCHAR(100),
    IN p_nacionalidad VARCHAR(100),
    IN p_email VARCHAR(120),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    DECLARE v_pais_nacimiento_id INT;
    DECLARE v_nacionalidad_id INT;
    DECLARE v_usuario_id INT;
    DECLARE v_rol_id INT;

    -- Validar si el email ya existe
    IF EXISTS (SELECT 1 FROM usuarios WHERE email = p_email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El correo electrónico ya está registrado.';
    END IF;

    -- Crear o reutilizar país de nacimiento
    SELECT pais_id INTO v_pais_nacimiento_id
    FROM pais WHERE nombre = p_pais_nacimiento
    LIMIT 1;

    IF v_pais_nacimiento_id IS NULL THEN
        INSERT INTO pais (nombre) VALUES (p_pais_nacimiento);
        SET v_pais_nacimiento_id = LAST_INSERT_ID();
    END IF;

    -- Crear o reutilizar nacionalidad
    SELECT pais_id INTO v_nacionalidad_id
    FROM pais WHERE nombre = p_nacionalidad
    LIMIT 1;

    IF v_nacionalidad_id IS NULL THEN
        INSERT INTO pais (nombre) VALUES (p_nacionalidad);
        SET v_nacionalidad_id = LAST_INSERT_ID();
    END IF;

    -- Insertar usuario
    INSERT INTO usuarios (
        nombre_completo, fecha_nacimiento, genero,
        pais_nacimiento_id, nacionalidad_id, email, password_hash
    )
    VALUES (
        p_nombre_completo, p_fecha_nacimiento, p_genero,
        v_pais_nacimiento_id, v_nacionalidad_id, p_email, p_password_hash
    );

    SET v_usuario_id = LAST_INSERT_ID();

    -- Asignar rol por defecto (USUARIO)
    SELECT rol_id INTO v_rol_id FROM roles WHERE nombre = 'USUARIO' LIMIT 1;

    IF v_rol_id IS NOT NULL THEN
        INSERT INTO usuario_rol (usuario_id, rol_id)
        VALUES (v_usuario_id, v_rol_id);
    END IF;

    SELECT 'Usuario creado exitosamente' AS mensaje,
           v_usuario_id AS nuevo_usuario_id;
END //

DELIMITER ;
