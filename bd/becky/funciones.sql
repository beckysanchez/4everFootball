DELIMITER //
CREATE FUNCTION fn_edad_usuario(p_usuario_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_edad INT;

    SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())
    INTO v_edad
    FROM usuarios
    WHERE usuario_id = p_usuario_id;

    RETURN v_edad;
END //
DELIMITER ;

DROP FUNCTION fn_edad_usuario;


DELIMITER //
CREATE FUNCTION fn_total_reacciones_publicacion(p_publicacion_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COUNT(*)
    INTO v_total
    FROM reaccion
    WHERE publicacion_id = p_publicacion_id;

    RETURN v_total;
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION fn_total_publicaciones_usuario(p_usuario_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COUNT(*)
    INTO v_total
    FROM publicacion
    WHERE usuario_id = p_usuario_id;

    RETURN v_total;
END //
DELIMITER ;

