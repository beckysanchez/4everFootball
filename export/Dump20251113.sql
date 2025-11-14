CREATE DATABASE  IF NOT EXISTS `mundial_reddit` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mundial_reddit`;
-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: mundial_reddit
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`categoria_id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Jugadas','Jugadas destacadas o icónicas'),(2,'Entrevistas','Charlas con jugadores o entrenadores'),(3,'Partidos','Información sobre partidos específicos'),(4,'Estadísticas','Datos numéricos de mundiales o jugadores'),(5,'Sedes','Ciudades o países anfitriones'),(6,'Cultura','Aspectos culturales del mundial'),(7,'Incidentes','Hechos polémicos o curiosos');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentario`
--

DROP TABLE IF EXISTS `comentario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentario` (
  `comentario_id` int NOT NULL AUTO_INCREMENT,
  `comentario_padre_id` int DEFAULT NULL,
  `publicacion_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `contenido` text NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `eliminado` tinyint(1) DEFAULT '0',
  `eliminado_por` int DEFAULT NULL,
  PRIMARY KEY (`comentario_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `eliminado_por` (`eliminado_por`),
  KEY `idx_comentario_publicacion` (`publicacion_id`),
  KEY `comentario_padre_id` (`comentario_padre_id`),
  CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicacion` (`publicacion_id`),
  CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `comentario_ibfk_3` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `comentario_ibfk_4` FOREIGN KEY (`comentario_padre_id`) REFERENCES `comentario` (`comentario_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentario`
--

LOCK TABLES `comentario` WRITE;
/*!40000 ALTER TABLE `comentario` DISABLE KEYS */;
INSERT INTO `comentario` VALUES (1,NULL,9,6,'Literalmente ese españa era prime','2025-11-08 18:20:11',0,NULL),(2,1,9,12,'No solo españa, todo el mundial estuvo buenisimo','2025-11-08 19:01:07',0,NULL),(3,NULL,9,12,'Me encanto esta epoca','2025-11-08 19:01:25',0,NULL);
/*!40000 ALTER TABLE `comentario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentario_reaccion`
--

DROP TABLE IF EXISTS `comentario_reaccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentario_reaccion` (
  `reaccion_id` int NOT NULL AUTO_INCREMENT,
  `comentario_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo` enum('LIKE') DEFAULT 'LIKE',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reaccion_id`),
  UNIQUE KEY `comentario_id` (`comentario_id`,`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `comentario_reaccion_ibfk_1` FOREIGN KEY (`comentario_id`) REFERENCES `comentario` (`comentario_id`),
  CONSTRAINT `comentario_reaccion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentario_reaccion`
--

LOCK TABLES `comentario_reaccion` WRITE;
/*!40000 ALTER TABLE `comentario_reaccion` DISABLE KEYS */;
INSERT INTO `comentario_reaccion` VALUES (3,3,6,'LIKE','2025-11-08 19:03:35');
/*!40000 ALTER TABLE `comentario_reaccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mundial`
--

DROP TABLE IF EXISTS `mundial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mundial` (
  `mundial_id` int NOT NULL AUTO_INCREMENT,
  `nombre_comunidad` varchar(150) NOT NULL,
  `descripcion` text,
  `sede` varchar(150) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `portada_url` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mundial_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mundial`
--

LOCK TABLES `mundial` WRITE;
/*!40000 ALTER TABLE `mundial` DISABLE KEYS */;
INSERT INTO `mundial` VALUES (1,'Sudafrica 2010','La Copa Mundial de la FIFA Sudáfrica 2010 (en inglés: 2010 FIFA World Cup; en afrikáans: FIFA Sokker-Wêreldbekertoernooi in 2010) fue la decimonovena edición de la Copa Mundial de Fútbol. La competición se celebró en Sudáfrica, entre el 11 de junio y el 11 de julio de ese año. Fue la primera vez que el torneo se disputaba en África y la quinta que lo hacía en el hemisferio sur. El país anfitrión superó en la elección previa a Egipto y Marruecos.','Sudafrica','/4everFootball/data/uploads/img_690aff45a9e51.png','https://images.daznservices.com/di/library/DAZN_News/6c/7e/espana-paises-bajos-mundial-2010-andres-iniesta_gi4y8xqfwht31mm1d66rvk1s4.jpg?t=449527028','sudafrica-2010','2025-11-05 01:39:49'),(2,'Brasil 2014','La Copa Mundial de la FIFA Brasil 2014 (en portugués: Copa do Mundo FIFA de 2014) fue la vigésima edición de la Copa Mundial de Fútbol. Se realizó en Brasil entre el 12 de junio y el 13 de julio de 2014, por segunda vez en dicho país, tras el campeonato de 1950.','Brasil','/4everFootball/data/uploads/img_690ff3407f126.jpg','https://media.cnn.com/api/v1/images/stellar/prod/cnne-159503-es-la-segunda-vez-que-brasil-organiza-un-mundial-en-1950-perdio-la-final-en-su-casa-ante-uruguay.jpg?c=16x9&q=h_833,w_1480,c_fill','brasil-2014','2025-11-08 19:49:52');
/*!40000 ALTER TABLE `mundial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mundial_participante`
--

DROP TABLE IF EXISTS `mundial_participante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mundial_participante` (
  `mundial_id` int NOT NULL,
  `seleccion_id` int NOT NULL,
  PRIMARY KEY (`mundial_id`,`seleccion_id`),
  KEY `seleccion_id` (`seleccion_id`),
  CONSTRAINT `mundial_participante_ibfk_1` FOREIGN KEY (`mundial_id`) REFERENCES `mundial` (`mundial_id`),
  CONSTRAINT `mundial_participante_ibfk_2` FOREIGN KEY (`seleccion_id`) REFERENCES `seleccion` (`seleccion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mundial_participante`
--

LOCK TABLES `mundial_participante` WRITE;
/*!40000 ALTER TABLE `mundial_participante` DISABLE KEYS */;
/*!40000 ALTER TABLE `mundial_participante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mundial_sede`
--

DROP TABLE IF EXISTS `mundial_sede`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mundial_sede` (
  `mundial_id` int NOT NULL,
  `pais_id` int NOT NULL,
  PRIMARY KEY (`mundial_id`,`pais_id`),
  KEY `pais_id` (`pais_id`),
  CONSTRAINT `mundial_sede_ibfk_1` FOREIGN KEY (`mundial_id`) REFERENCES `mundial` (`mundial_id`),
  CONSTRAINT `mundial_sede_ibfk_2` FOREIGN KEY (`pais_id`) REFERENCES `pais` (`pais_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mundial_sede`
--

LOCK TABLES `mundial_sede` WRITE;
/*!40000 ALTER TABLE `mundial_sede` DISABLE KEYS */;
/*!40000 ALTER TABLE `mundial_sede` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pais`
--

DROP TABLE IF EXISTS `pais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pais` (
  `pais_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`pais_id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pais`
--

LOCK TABLES `pais` WRITE;
/*!40000 ALTER TABLE `pais` DISABLE KEYS */;
INSERT INTO `pais` VALUES (3,'Argentina'),(5,'Bélgica'),(9,'Colombia'),(10,'Colombiano'),(8,'dasda'),(2,'Mexicana'),(1,'México'),(6,'Perú'),(7,'Peruano');
/*!40000 ALTER TABLE `pais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicacion`
--

DROP TABLE IF EXISTS `publicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publicacion` (
  `publicacion_id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `mundial_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `seleccion_id` int DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `tipo_media` enum('IMAGEN','VIDEO','LINK') DEFAULT 'IMAGEN',
  `imagen_blob` longblob,
  `video_blob` longblob,
  `media_url` varchar(255) DEFAULT NULL,
  `estatus` enum('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  `creada_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `aprobada_en` datetime DEFAULT NULL,
  `aprobada_por` int DEFAULT NULL,
  PRIMARY KEY (`publicacion_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `seleccion_id` (`seleccion_id`),
  KEY `fk_publicacion_aprobada_por` (`aprobada_por`),
  KEY `idx_publicacion_mundial_categoria` (`mundial_id`,`categoria_id`),
  KEY `idx_publicacion_estatus_aprobada` (`estatus`,`aprobada_en`),
  KEY `idx_publicacion_filtros` (`mundial_id`,`categoria_id`,`usuario_id`),
  CONSTRAINT `fk_publicacion_aprobada_por` FOREIGN KEY (`aprobada_por`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_publicacion_mundial` FOREIGN KEY (`mundial_id`) REFERENCES `mundial` (`mundial_id`),
  CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `publicacion_ibfk_2` FOREIGN KEY (`mundial_id`) REFERENCES `mundial` (`mundial_id`),
  CONSTRAINT `publicacion_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`),
  CONSTRAINT `publicacion_ibfk_4` FOREIGN KEY (`seleccion_id`) REFERENCES `seleccion` (`seleccion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicacion`
--

LOCK TABLES `publicacion` WRITE;
/*!40000 ALTER TABLE `publicacion` DISABLE KEYS */;
INSERT INTO `publicacion` VALUES (9,6,1,4,NULL,'España campeon del mundial','Con un gol de Andrés Iniesta en el tiempo suplementario, la Roja venció por 1-0 a Holanda y se consagró campeón en Sudáfrica. Es su primer título mundial.','IMAGEN',NULL,NULL,'uploads/media_690c3593751b49.69826803.jpg','APROBADA','2025-11-05 23:43:47','2025-11-11 23:08:54',6);
/*!40000 ALTER TABLE `publicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reaccion`
--

DROP TABLE IF EXISTS `reaccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reaccion` (
  `reaccion_id` int NOT NULL AUTO_INCREMENT,
  `publicacion_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo` enum('LIKE','ESTRELLA') NOT NULL,
  `valor` int DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reaccion_id`),
  UNIQUE KEY `publicacion_id` (`publicacion_id`,`usuario_id`,`tipo`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_reaccion_publicacion_tipo` (`publicacion_id`,`tipo`),
  CONSTRAINT `reaccion_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicacion` (`publicacion_id`),
  CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `reaccion_chk_1` CHECK (((`valor` between 1 and 5) or (`valor` is null)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reaccion`
--

LOCK TABLES `reaccion` WRITE;
/*!40000 ALTER TABLE `reaccion` DISABLE KEYS */;
/*!40000 ALTER TABLE `reaccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `nombre` enum('ADMIN','USUARIO') NOT NULL,
  PRIMARY KEY (`rol_id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN'),(2,'USUARIO');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seleccion`
--

DROP TABLE IF EXISTS `seleccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seleccion` (
  `seleccion_id` int NOT NULL AUTO_INCREMENT,
  `pais_id` int NOT NULL,
  `apodo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`seleccion_id`),
  KEY `pais_id` (`pais_id`),
  CONSTRAINT `seleccion_ibfk_1` FOREIGN KEY (`pais_id`) REFERENCES `pais` (`pais_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seleccion`
--

LOCK TABLES `seleccion` WRITE;
/*!40000 ALTER TABLE `seleccion` DISABLE KEYS */;
/*!40000 ALTER TABLE `seleccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_mundial_seguido`
--

DROP TABLE IF EXISTS `usuario_mundial_seguido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_mundial_seguido` (
  `usuario_id` int NOT NULL,
  `mundial_id` int NOT NULL,
  PRIMARY KEY (`usuario_id`,`mundial_id`),
  KEY `mundial_id` (`mundial_id`),
  CONSTRAINT `usuario_mundial_seguido_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `usuario_mundial_seguido_ibfk_2` FOREIGN KEY (`mundial_id`) REFERENCES `mundial` (`mundial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_mundial_seguido`
--

LOCK TABLES `usuario_mundial_seguido` WRITE;
/*!40000 ALTER TABLE `usuario_mundial_seguido` DISABLE KEYS */;
INSERT INTO `usuario_mundial_seguido` VALUES (6,1);
/*!40000 ALTER TABLE `usuario_mundial_seguido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_rol`
--

DROP TABLE IF EXISTS `usuario_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_rol` (
  `usuario_id` int NOT NULL,
  `rol_id` int NOT NULL,
  PRIMARY KEY (`usuario_id`,`rol_id`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `usuario_rol_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `usuario_rol_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_rol`
--

LOCK TABLES `usuario_rol` WRITE;
/*!40000 ALTER TABLE `usuario_rol` DISABLE KEYS */;
INSERT INTO `usuario_rol` VALUES (6,1),(11,2),(12,2),(13,2);
/*!40000 ALTER TABLE `usuario_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') DEFAULT 'Otro',
  `pais_nacimiento_id` int DEFAULT NULL,
  `nacionalidad_id` int DEFAULT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `foto_blob` longblob,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuarios_pais_nacimiento` (`pais_nacimiento_id`),
  KEY `fk_usuarios_nacionalidad` (`nacionalidad_id`),
  KEY `idx_usuarios_activo` (`activo`),
  KEY `idx_usuarios_creado_en` (`creado_en`),
  CONSTRAINT `fk_usuarios_nacionalidad` FOREIGN KEY (`nacionalidad_id`) REFERENCES `pais` (`pais_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_pais_nacimiento` FOREIGN KEY (`pais_nacimiento_id`) REFERENCES `pais` (`pais_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (6,'Jose Hernandez Ruiz','2008-01-28','Masculino',1,2,'juan@4everAdmin.com','$2y$10$dtWhtHTGSvOx7O6qTPngH.eV4m4V7Nj4YwFhJUR1gJsSNmgku0/OW',NULL,'2025-10-26 14:40:43','2025-11-05 22:34:25',1),(11,'Ramiro Romero Sanchez','2013-11-01','Masculino',1,2,'ramiro@gmail.com','$2y$10$sgzYoe7RPr9Z0tBwkye0ueKfz5XyK7lAzB9FTIpb.RWFwsathwCXW','','2025-11-05 21:39:27','2025-11-08 19:25:03',1),(12,'Ruben Villalpando Martinez','2013-10-31','Masculino',1,2,'ruben@gmail.com','$2y$10$Ne6ZYL9eSm9ALyVZO9A9ne2z7QjtB35G5TpMiAser0RoRfPVjt2hC','','2025-11-08 18:37:59','2025-11-08 19:20:35',1),(13,'David Moreno Martinez','2013-11-13','Masculino',9,10,'david@gmail.com','$2y$10$0FyRzvjvuovzlqz282PbyuNP0u9TBIlHGwXDBaucNRLDBBzR41MEC','','2025-11-13 21:57:00','2025-11-13 21:57:00',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_comentarios_detalle`
--

DROP TABLE IF EXISTS `vista_comentarios_detalle`;
/*!50001 DROP VIEW IF EXISTS `vista_comentarios_detalle`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_comentarios_detalle` AS SELECT 
 1 AS `comentario_id`,
 1 AS `contenido`,
 1 AS `creado_en`,
 1 AS `autor_comentario`,
 1 AS `publicacion_id`,
 1 AS `publicacion_titulo`,
 1 AS `autor_publicacion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_dashboard_usuarios`
--

DROP TABLE IF EXISTS `vista_dashboard_usuarios`;
/*!50001 DROP VIEW IF EXISTS `vista_dashboard_usuarios`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_dashboard_usuarios` AS SELECT 
 1 AS `usuario_id`,
 1 AS `nombre_completo`,
 1 AS `email`,
 1 AS `pais_nacimiento`,
 1 AS `nacionalidad`,
 1 AS `rol`,
 1 AS `total_publicaciones`,
 1 AS `total_comentarios`,
 1 AS `reacciones_realizadas`,
 1 AS `reacciones_recibidas`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vista_publicacion`
--

DROP TABLE IF EXISTS `vista_publicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vista_publicacion` (
  `vista_id` int NOT NULL AUTO_INCREMENT,
  `publicacion_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `visto_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vista_id`),
  KEY `publicacion_id` (`publicacion_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `vista_publicacion_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicacion` (`publicacion_id`),
  CONSTRAINT `vista_publicacion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vista_publicacion`
--

LOCK TABLES `vista_publicacion` WRITE;
/*!40000 ALTER TABLE `vista_publicacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `vista_publicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_publicaciones_detalle`
--

DROP TABLE IF EXISTS `vista_publicaciones_detalle`;
/*!50001 DROP VIEW IF EXISTS `vista_publicaciones_detalle`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_publicaciones_detalle` AS SELECT 
 1 AS `publicacion_id`,
 1 AS `titulo`,
 1 AS `descripcion`,
 1 AS `tipo_media`,
 1 AS `media_url`,
 1 AS `estatus`,
 1 AS `creada_en`,
 1 AS `autor`,
 1 AS `categoria`,
 1 AS `mundial`,
 1 AS `seleccion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_reacciones_detalle`
--

DROP TABLE IF EXISTS `vista_reacciones_detalle`;
/*!50001 DROP VIEW IF EXISTS `vista_reacciones_detalle`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_reacciones_detalle` AS SELECT 
 1 AS `reaccion_id`,
 1 AS `tipo`,
 1 AS `valor`,
 1 AS `creado_en`,
 1 AS `usuario_reaccion`,
 1 AS `publicacion_id`,
 1 AS `publicacion_titulo`,
 1 AS `autor_publicacion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_usuarios_pais_rol`
--

DROP TABLE IF EXISTS `vista_usuarios_pais_rol`;
/*!50001 DROP VIEW IF EXISTS `vista_usuarios_pais_rol`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_usuarios_pais_rol` AS SELECT 
 1 AS `usuario_id`,
 1 AS `nombre_completo`,
 1 AS `fecha_nacimiento`,
 1 AS `genero`,
 1 AS `pais_nacimiento`,
 1 AS `nacionalidad`,
 1 AS `rol`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vista_comentarios_detalle`
--

/*!50001 DROP VIEW IF EXISTS `vista_comentarios_detalle`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_comentarios_detalle` AS select `c`.`comentario_id` AS `comentario_id`,`c`.`contenido` AS `contenido`,`c`.`creado_en` AS `creado_en`,`u_com`.`nombre_completo` AS `autor_comentario`,`p`.`publicacion_id` AS `publicacion_id`,`p`.`titulo` AS `publicacion_titulo`,`u_pub`.`nombre_completo` AS `autor_publicacion` from (((`comentario` `c` left join `usuarios` `u_com` on((`c`.`usuario_id` = `u_com`.`usuario_id`))) left join `publicacion` `p` on((`c`.`publicacion_id` = `p`.`publicacion_id`))) left join `usuarios` `u_pub` on((`p`.`usuario_id` = `u_pub`.`usuario_id`))) where (`c`.`eliminado` = false) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_dashboard_usuarios`
--

/*!50001 DROP VIEW IF EXISTS `vista_dashboard_usuarios`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_dashboard_usuarios` AS select `u`.`usuario_id` AS `usuario_id`,`u`.`nombre_completo` AS `nombre_completo`,`u`.`email` AS `email`,`p1`.`nombre` AS `pais_nacimiento`,`p2`.`nombre` AS `nacionalidad`,`r`.`nombre` AS `rol`,count(distinct `pub`.`publicacion_id`) AS `total_publicaciones`,count(distinct `com`.`comentario_id`) AS `total_comentarios`,count(distinct `reac`.`reaccion_id`) AS `reacciones_realizadas`,count(distinct `reac_pub`.`reaccion_id`) AS `reacciones_recibidas` from (((((((((`usuarios` `u` left join `pais` `p1` on((`u`.`pais_nacimiento_id` = `p1`.`pais_id`))) left join `pais` `p2` on((`u`.`nacionalidad_id` = `p2`.`pais_id`))) left join `usuario_rol` `ur` on((`ur`.`usuario_id` = `u`.`usuario_id`))) left join `roles` `r` on((`ur`.`rol_id` = `r`.`rol_id`))) left join `publicacion` `pub` on((`pub`.`usuario_id` = `u`.`usuario_id`))) left join `comentario` `com` on((`com`.`usuario_id` = `u`.`usuario_id`))) left join `reaccion` `reac` on((`reac`.`usuario_id` = `u`.`usuario_id`))) left join `publicacion` `pub2` on((`pub2`.`usuario_id` = `u`.`usuario_id`))) left join `reaccion` `reac_pub` on((`reac_pub`.`publicacion_id` = `pub2`.`publicacion_id`))) group by `u`.`usuario_id`,`u`.`nombre_completo`,`u`.`email`,`p1`.`nombre`,`p2`.`nombre`,`r`.`nombre` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_publicaciones_detalle`
--

/*!50001 DROP VIEW IF EXISTS `vista_publicaciones_detalle`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `vista_publicaciones_detalle` AS select `p`.`publicacion_id` AS `publicacion_id`,`p`.`titulo` AS `titulo`,`p`.`descripcion` AS `descripcion`,`p`.`tipo_media` AS `tipo_media`,`p`.`media_url` AS `media_url`,`p`.`estatus` AS `estatus`,`p`.`creada_en` AS `creada_en`,`u`.`nombre_completo` AS `autor`,`c`.`nombre` AS `categoria`,`m`.`nombre_comunidad` AS `mundial`,`m`.`sede` AS `seleccion` from ((((`publicacion` `p` left join `usuarios` `u` on((`p`.`usuario_id` = `u`.`usuario_id`))) left join `categoria` `c` on((`p`.`categoria_id` = `c`.`categoria_id`))) left join `mundial` `m` on((`p`.`mundial_id` = `m`.`mundial_id`))) left join `seleccion` `s` on((`p`.`seleccion_id` = `s`.`seleccion_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_reacciones_detalle`
--

/*!50001 DROP VIEW IF EXISTS `vista_reacciones_detalle`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_reacciones_detalle` AS select `r`.`reaccion_id` AS `reaccion_id`,`r`.`tipo` AS `tipo`,`r`.`valor` AS `valor`,`r`.`creado_en` AS `creado_en`,`u_reac`.`nombre_completo` AS `usuario_reaccion`,`p`.`publicacion_id` AS `publicacion_id`,`p`.`titulo` AS `publicacion_titulo`,`u_pub`.`nombre_completo` AS `autor_publicacion` from (((`reaccion` `r` left join `usuarios` `u_reac` on((`r`.`usuario_id` = `u_reac`.`usuario_id`))) left join `publicacion` `p` on((`r`.`publicacion_id` = `p`.`publicacion_id`))) left join `usuarios` `u_pub` on((`p`.`usuario_id` = `u_pub`.`usuario_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_usuarios_pais_rol`
--

/*!50001 DROP VIEW IF EXISTS `vista_usuarios_pais_rol`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_usuarios_pais_rol` AS select `u`.`usuario_id` AS `usuario_id`,`u`.`nombre_completo` AS `nombre_completo`,`u`.`fecha_nacimiento` AS `fecha_nacimiento`,`u`.`genero` AS `genero`,`p1`.`nombre` AS `pais_nacimiento`,`p2`.`nombre` AS `nacionalidad`,`r`.`nombre` AS `rol` from ((((`usuarios` `u` left join `pais` `p1` on((`u`.`pais_nacimiento_id` = `p1`.`pais_id`))) left join `pais` `p2` on((`u`.`nacionalidad_id` = `p2`.`pais_id`))) left join `usuario_rol` `ur` on((`ur`.`usuario_id` = `u`.`usuario_id`))) left join `roles` `r` on((`ur`.`rol_id` = `r`.`rol_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-13 22:30:48
