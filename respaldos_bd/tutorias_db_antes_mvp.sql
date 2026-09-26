-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: tutorias_db
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carreras` (
  `id_carrera` int NOT NULL AUTO_INCREMENT,
  `nombre_carrera` varchar(150) NOT NULL,
  PRIMARY KEY (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (1,'Ingeniería de Sistemas'),(2,'Ingeniería Industrial');
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidad_tutor`
--

DROP TABLE IF EXISTS `disponibilidad_tutor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidad_tutor` (
  `id_disponibilidad` int NOT NULL AUTO_INCREMENT,
  `id_tutor` int NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_disponibilidad`),
  KEY `fk_disp_tutor` (`id_tutor`),
  CONSTRAINT `fk_disp_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidad_tutor`
--

LOCK TABLES `disponibilidad_tutor` WRITE;
/*!40000 ALTER TABLE `disponibilidad_tutor` DISABLE KEYS */;
INSERT INTO `disponibilidad_tutor` VALUES (1,1,'Lunes','14:00:00','18:00:00'),(2,1,'Miercoles','14:00:00','18:00:00'),(3,1,'Viernes','09:00:00','12:00:00'),(5,2,'Sabado','15:00:00','17:00:00');
/*!40000 ALTER TABLE `disponibilidad_tutor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudiantes` (
  `id_estudiante` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_carrera` int NOT NULL,
  `semestre` tinyint NOT NULL,
  `registro_universitario` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_estudiante`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  UNIQUE KEY `registro_universitario` (`registro_universitario`),
  KEY `fk_estudiantes_carreras` (`id_carrera`),
  CONSTRAINT `fk_estudiantes_carreras` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON UPDATE CASCADE,
  CONSTRAINT `fk_estudiantes_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,3,1,4,'RU-2026-98765'),(3,9,2,1,'RU-2026-986556');
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluaciones_tutoria`
--

DROP TABLE IF EXISTS `evaluaciones_tutoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluaciones_tutoria` (
  `id_evaluacion` int NOT NULL AUTO_INCREMENT,
  `id_tutoria` int NOT NULL,
  `calificacion` tinyint NOT NULL,
  `comentario` text,
  `fecha_evaluacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evaluacion`),
  UNIQUE KEY `id_tutoria` (`id_tutoria`),
  CONSTRAINT `fk_evaluaciones_tutoria` FOREIGN KEY (`id_tutoria`) REFERENCES `tutorias` (`id_tutoria`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_tutoria_chk_1` CHECK ((`calificacion` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_tutoria`
--

LOCK TABLES `evaluaciones_tutoria` WRITE;
/*!40000 ALTER TABLE `evaluaciones_tutoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluaciones_tutoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `id_materia` int NOT NULL AUTO_INCREMENT,
  `nombre_materia` varchar(150) NOT NULL,
  `id_carrera` int DEFAULT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `fk_materias_carreras` (`id_carrera`),
  CONSTRAINT `fk_materias_carreras` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'Base de Datos I',1),(2,'Programación I',1),(3,'Tecnología Web I',1),(4,'Programación II',1);
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_accesos`
--

DROP TABLE IF EXISTS `registro_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_accesos` (
  `id_acceso` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_origen` varchar(45) DEFAULT NULL,
  `resultado` enum('exitoso','fallido') NOT NULL,
  PRIMARY KEY (`id_acceso`),
  KEY `fk_accesos_usuarios` (`id_usuario`),
  CONSTRAINT `fk_accesos_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_accesos`
--

LOCK TABLES `registro_accesos` WRITE;
/*!40000 ALTER TABLE `registro_accesos` DISABLE KEYS */;
INSERT INTO `registro_accesos` VALUES (1,1,'2026-09-18 22:12:15','192.168.143.1','exitoso'),(2,1,'2026-09-18 23:55:58','192.168.178.1','exitoso'),(3,NULL,'2026-09-20 17:38:45','192.168.143.1','fallido'),(4,NULL,'2026-09-20 17:45:40','192.168.143.1','fallido'),(5,1,'2026-09-20 17:45:50','192.168.143.1','exitoso'),(6,1,'2026-09-20 19:17:27','192.168.143.1','exitoso'),(7,1,'2026-09-20 19:17:43','192.168.143.1','exitoso'),(8,7,'2026-09-20 20:23:57','192.168.143.1','exitoso'),(9,7,'2026-09-20 20:24:28','192.168.143.1','fallido'),(10,7,'2026-09-20 20:24:33','192.168.143.1','exitoso'),(11,8,'2026-09-20 20:34:51','192.168.143.1','fallido'),(12,1,'2026-09-20 20:35:08','192.168.143.1','exitoso'),(13,7,'2026-09-20 20:40:49','192.168.143.1','exitoso'),(14,1,'2026-09-20 20:41:32','192.168.143.1','exitoso'),(15,7,'2026-09-20 20:43:26','192.168.143.1','fallido'),(16,1,'2026-09-20 20:46:53','192.168.143.1','exitoso'),(17,7,'2026-09-20 20:47:46','192.168.143.1','exitoso'),(18,7,'2026-09-20 20:49:24','192.168.143.1','fallido'),(19,1,'2026-09-23 20:54:09','192.168.143.1','exitoso'),(20,2,'2026-09-23 21:25:19','192.168.143.1','fallido'),(21,1,'2026-09-23 21:25:27','192.168.143.1','exitoso'),(22,8,'2026-09-23 21:26:04','192.168.143.1','exitoso'),(23,1,'2026-09-23 21:26:49','192.168.143.1','exitoso'),(24,8,'2026-09-23 21:27:22','192.168.143.1','exitoso');
/*!40000 ALTER TABLE `registro_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(30) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrador'),(3,'estudiante'),(2,'tutor');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutor_materia`
--

DROP TABLE IF EXISTS `tutor_materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutor_materia` (
  `id_tutor` int NOT NULL,
  `id_materia` int NOT NULL,
  PRIMARY KEY (`id_tutor`,`id_materia`),
  KEY `fk_tm_materia` (`id_materia`),
  CONSTRAINT `fk_tm_materia` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE,
  CONSTRAINT `fk_tm_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutor_materia`
--

LOCK TABLES `tutor_materia` WRITE;
/*!40000 ALTER TABLE `tutor_materia` DISABLE KEYS */;
INSERT INTO `tutor_materia` VALUES (1,1),(2,1),(1,2),(2,2),(2,3),(2,4);
/*!40000 ALTER TABLE `tutor_materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutores`
--

DROP TABLE IF EXISTS `tutores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutores` (
  `id_tutor` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `especialidad` varchar(150) DEFAULT NULL,
  `biografia` text,
  PRIMARY KEY (`id_tutor`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `fk_tutores_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
INSERT INTO `tutores` VALUES (1,2,'Desarrollo Web y Bases de Datos','Docente tutor especializado en desarrollo backend y arquitecturas web.'),(2,8,'Desarrollo web','ddd');
/*!40000 ALTER TABLE `tutores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutorias`
--

DROP TABLE IF EXISTS `tutorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorias` (
  `id_tutoria` int NOT NULL AUTO_INCREMENT,
  `id_estudiante` int NOT NULL,
  `id_tutor` int NOT NULL,
  `id_materia` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `modalidad` enum('presencial','virtual') NOT NULL DEFAULT 'presencial',
  `lugar_o_enlace` varchar(200) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','realizada','cancelada') NOT NULL DEFAULT 'pendiente',
  `observaciones` text,
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tutoria`),
  KEY `fk_tutorias_estudiante` (`id_estudiante`),
  KEY `fk_tutorias_tutor` (`id_tutor`),
  KEY `fk_tutorias_materia` (`id_materia`),
  KEY `idx_tutoria_fecha` (`fecha`),
  KEY `idx_tutoria_estado` (`estado`),
  CONSTRAINT `fk_tutorias_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON UPDATE CASCADE,
  CONSTRAINT `fk_tutorias_materia` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON UPDATE CASCADE,
  CONSTRAINT `fk_tutorias_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutorias`
--

LOCK TABLES `tutorias` WRITE;
/*!40000 ALTER TABLE `tutorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `tutorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `fk_usuarios_roles` (`id_rol`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,'Admin','Sistema','admin@tutorias.local','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000001','activo','2026-09-18 22:11:05'),(2,2,'Carlos','Docente','tutor@tutorias.local','tutor1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000002','activo','2026-09-18 22:11:05'),(3,3,'Maria','Estudiante','estudiante@tutorias.local','estudiante1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000003','activo','2026-09-18 22:11:05'),(5,3,'raul','Sandoval','paul@upds.edu.bo','angel123','$2y$12$z7J6iIRG6WFQC6u4qkg.tO.JIjts6Q9QN8Oi2vRcDlxU49dhduRAS',NULL,'inactivo','2026-09-18 22:31:38'),(6,1,'juan','fernando','juan123@gmail.com','juan12','$2y$12$muFtZoyMm61UAHyfK094KevYiyZLkWy6ZSC/7J9RVgUUtkcNTqbzu','77175134','activo','2026-09-19 00:54:49'),(7,1,'manuel','morales','manuel@gmail.com','manuel123','$2y$12$s5G7HXE6U8QVpkYMz2Aepeb9maZWs8OeDzwlZQCL9PB77HK4aLMaa','77174567','activo','2026-09-20 20:04:45'),(8,2,'saul','ortega','saul@gmail.com','tutor2','$2y$12$UTeQUMMccHdQT6FKliqDk.GDUHl2.4ZRH4IqmO9Mjq2aTub6/rSve','81174569','activo','2026-09-20 20:34:27'),(9,3,'Ana','Pérez','ana@gmail.com','anaperez','$2y$12$.9Icgv8SMT1ZGOTb9AAsPOeu9W2QkIcpSckOs6uRl1BFwC50ZleNS',NULL,'activo','2026-09-20 21:47:11');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tutorias_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-23 22:08:55
