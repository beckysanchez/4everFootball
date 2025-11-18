DELIMITER //
CREATE TRIGGER trg_publicacion_set_default_status
BEFORE INSERT ON publicacion
FOR EACH ROW
BEGIN
    IF NEW.estatus IS NULL THEN
        SET NEW.estatus = 'PENDIENTE';
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_incrementar_comentarios
AFTER INSERT ON comentario
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET total_comentarios = total_comentarios + 1
    WHERE publicacion_id = NEW.publicacion_id;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_decrementar_comentarios
AFTER DELETE ON comentario
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET total_comentarios = total_comentarios - 1
    WHERE publicacion_id = OLD.publicacion_id;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_incrementar_likes
AFTER INSERT ON reaccion
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET total_likes = total_likes + 1
    WHERE publicacion_id = NEW.publicacion_id;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_decrementar_likes
AFTER DELETE ON reaccion
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET total_likes = total_likes - 1
    WHERE publicacion_id = OLD.publicacion_id;
END //
DELIMITER ;


