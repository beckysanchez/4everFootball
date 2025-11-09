-- 1️⃣ ejecutar primero
SHOW CREATE TABLE usuarios;

--ejecutar segundo

-- 2️⃣ Eliminar las llaves foráneas anteriores
ALTER TABLE usuarios DROP FOREIGN KEY usuarios_ibfk_1;
ALTER TABLE usuarios DROP FOREIGN KEY usuarios_ibfk_2;

-- 3️⃣ Volver a crearlas con ON DELETE SET NULL
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_pais_nacimiento
    FOREIGN KEY (pais_nacimiento_id)
    REFERENCES pais(pais_id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_nacionalidad
    FOREIGN KEY (nacionalidad_id)
    REFERENCES pais(pais_id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
    
    ALTER TABLE publicacion DROP FOREIGN KEY publicacion_ibfk_5;

ALTER TABLE publicacion
ADD CONSTRAINT fk_publicacion_aprobada_por
    FOREIGN KEY (aprobada_por)
    REFERENCES usuarios(usuario_id)
    ON DELETE SET NULL;
    
    -- ejecutar tercero
    
    SHOW CREATE TABLE usuarios;
SHOW CREATE TABLE publicacion;


-- ejecutar cuarto

-- 🧩 Ajuste para USUARIOS
ALTER TABLE usuarios 
DROP FOREIGN KEY usuarios_ibfk_1,
DROP FOREIGN KEY usuarios_ibfk_2;

ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_pais_nacimiento
  FOREIGN KEY (pais_nacimiento_id)
  REFERENCES pais(pais_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
ADD CONSTRAINT fk_usuarios_nacionalidad
  FOREIGN KEY (nacionalidad_id)
  REFERENCES pais(pais_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

-- 🧩 Ajuste para PUBLICACION (solo aprobada_por)
ALTER TABLE publicacion 
DROP FOREIGN KEY publicacion_ibfk_5;

ALTER TABLE publicacion
ADD CONSTRAINT fk_publicacion_aprobada_por
  FOREIGN KEY (aprobada_por)
  REFERENCES usuarios(usuario_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;


-- cree este mundial para probar cositas
INSERT INTO mundial (nombre_oficial, anio)
VALUES ('Sudáfrica 2010', 2010);

SELECT * FROM mundial;


-- modifique la tabla mundial

-- Desactivar restricciones FK temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas dependientes si existen
DROP TABLE IF EXISTS mundial_sede;

-- Ahora sí puedes eliminar mundial
DROP TABLE IF EXISTS mundial;

-- Volver a activar verificaciones
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE mundial (
  mundial_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_comunidad VARCHAR(150) NOT NULL,
  descripcion TEXT,
  sede VARCHAR(150),
  logo_url VARCHAR(255),
  portada_url VARCHAR(255),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- esto va porque la cague
-- Recrear tabla mundial_sede
CREATE TABLE IF NOT EXISTS mundial_sede (
  mundial_id INT NOT NULL,
  pais_id INT NOT NULL,
  PRIMARY KEY (mundial_id, pais_id),
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id),
  FOREIGN KEY (pais_id) REFERENCES pais(pais_id)
);

-- Recrear tabla mundial_participante
CREATE TABLE IF NOT EXISTS mundial_participante (
  mundial_id INT NOT NULL,
  seleccion_id INT NOT NULL,
  PRIMARY KEY (mundial_id, seleccion_id),
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id),
  FOREIGN KEY (seleccion_id) REFERENCES seleccion(seleccion_id)
);

-- Si publicacion usa mundial_id, hay que recrear su FK también
ALTER TABLE publicacion
  ADD CONSTRAINT fk_publicacion_mundial
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id);
  
  DELETE FROM publicacion WHERE mundial_id NOT IN (SELECT mundial_id FROM mundial);
  
  SET SQL_SAFE_UPDATES = 0;

DELETE FROM publicacion 
WHERE mundial_id NOT IN (SELECT mundial_id FROM mundial);

SET SQL_SAFE_UPDATES = 1;

ALTER TABLE publicacion
  ADD CONSTRAINT fk_publicacion_mundial
  FOREIGN KEY (mundial_id) REFERENCES mundial(mundial_id);
  
  SHOW CREATE TABLE publicacion;

ALTER TABLE mundial ADD COLUMN slug VARCHAR(255) AFTER portada_url;


-- mas cambios jeje
INSERT INTO roles (nombre)
VALUES ('ADMIN'), ('USUARIO') AS new
ON DUPLICATE KEY UPDATE nombre = new.nombre;

INSERT INTO usuario_rol (usuario_id, rol_id)
SELECT u.usuario_id, r.rol_id
FROM usuarios u
JOIN roles r ON r.nombre = 'USUARIO'
WHERE u.usuario_id NOT IN (
    SELECT usuario_id FROM usuario_rol
);


-- ejectuar el nuevo sp_insert user porque se modifico




