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
-- Table structure for table `calendario_mg`
--

DROP TABLE IF EXISTS `calendario_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendario_mg` (
  `id_hito` int NOT NULL AUTO_INCREMENT,
  `id_cohorte` int NOT NULL,
  `etapa` enum('previa','mg1','mg2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('taller','asignacion_tutor','asignacion_tribunal','informe','defensa','ingreso_mg2','otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` smallint unsigned NOT NULL DEFAULT '1',
  `fecha_limite` date NOT NULL,
  `avance_esperado_pct` tinyint unsigned DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hito`),
  UNIQUE KEY `uq_calendario_mg_orden` (`id_cohorte`,`etapa`,`orden`),
  KEY `idx_calendario_mg_cohorte` (`id_cohorte`),
  KEY `idx_calendario_mg_fecha` (`fecha_limite`),
  KEY `idx_calendario_mg_tipo` (`tipo`),
  CONSTRAINT `fk_calendario_mg_cohorte` FOREIGN KEY (`id_cohorte`) REFERENCES `cohortes_mg` (`id_cohorte`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_calendario_avance` CHECK (((`avance_esperado_pct` is null) or (`avance_esperado_pct` between 0 and 100)))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendario_mg`
--

LOCK TABLES `calendario_mg` WRITE;
/*!40000 ALTER TABLE `calendario_mg` DISABLE KEYS */;
INSERT INTO `calendario_mg` VALUES (1,1,'mg2','asignacion_tutor','primer informe',7,'2026-02-02',52,'2026-09-25 21:28:40');
/*!40000 ALTER TABLE `calendario_mg` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Table structure for table `cartas_designacion`
--

DROP TABLE IF EXISTS `cartas_designacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cartas_designacion` (
  `id_carta` int NOT NULL AUTO_INCREMENT,
  `id_tutoria` int NOT NULL,
  `id_tutor` int NOT NULL,
  `numero_carta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_carta` smallint unsigned NOT NULL DEFAULT '1',
  `fecha_generacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  `estado` enum('pendiente','aceptada','rechazada','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `tipo_rechazo` enum('sin_capacidad','sin_tiempo','no_corresponde','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_rechazo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_recepcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsabilidades` text COLLATE utf8mb4_unicode_ci,
  `documento_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vigente` tinyint(1) NOT NULL DEFAULT '1',
  `creado_por` int NOT NULL,
  PRIMARY KEY (`id_carta`),
  UNIQUE KEY `uq_carta_tutoria_version` (`id_tutoria`,`version_carta`),
  UNIQUE KEY `uq_carta_numero` (`numero_carta`),
  KEY `fk_cartas_creador` (`creado_por`),
  KEY `idx_cartas_tutor` (`id_tutor`),
  KEY `idx_cartas_estado` (`estado`),
  KEY `idx_cartas_vigente` (`id_tutoria`,`vigente`),
  CONSTRAINT `fk_cartas_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_cartas_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_cartas_tutoria` FOREIGN KEY (`id_tutoria`) REFERENCES `tutorias` (`id_tutoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_carta_version` CHECK ((`version_carta` >= 1)),
  CONSTRAINT `chk_carta_vigente` CHECK ((`vigente` in (0,1)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cartas_designacion`
--

LOCK TABLES `cartas_designacion` WRITE;
/*!40000 ALTER TABLE `cartas_designacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `cartas_designacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cohortes_mg`
--

DROP TABLE IF EXISTS `cohortes_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cohortes_mg` (
  `id_cohorte` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cohorte`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_cohortes_mg_activa` (`activa`),
  KEY `idx_cohortes_mg_fechas` (`fecha_inicio`,`fecha_fin`),
  CONSTRAINT `chk_cohorte_activa` CHECK ((`activa` in (0,1))),
  CONSTRAINT `chk_cohorte_fechas` CHECK (((`fecha_fin` is null) or (`fecha_fin` >= `fecha_inicio`)))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cohortes_mg`
--

LOCK TABLES `cohortes_mg` WRITE;
/*!40000 ALTER TABLE `cohortes_mg` DISABLE KEYS */;
INSERT INTO `cohortes_mg` VALUES (1,'MG-2026-1','Gestión 2026 - Primer semestre','2026-02-02','2026-06-30',1,'2026-09-25 20:57:34');
/*!40000 ALTER TABLE `cohortes_mg` ENABLE KEYS */;
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
-- Table structure for table `historial_tutores`
--

DROP TABLE IF EXISTS `historial_tutores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_tutores` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_tutoria` int NOT NULL,
  `id_tutor` int NOT NULL,
  `id_carta` int DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('propuesto','activo','rechazado','renuncio','reasignado','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'propuesto',
  `motivo_cambio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `fk_historial_carta` (`id_carta`),
  KEY `idx_historial_tutoria` (`id_tutoria`),
  KEY `idx_historial_tutor` (`id_tutor`),
  KEY `idx_historial_estado` (`estado`),
  CONSTRAINT `fk_historial_carta` FOREIGN KEY (`id_carta`) REFERENCES `cartas_designacion` (`id_carta`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_historial_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_historial_tutoria` FOREIGN KEY (`id_tutoria`) REFERENCES `tutorias` (`id_tutoria`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_tutores`
--

LOCK TABLES `historial_tutores` WRITE;
/*!40000 ALTER TABLE `historial_tutores` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_tutores` ENABLE KEYS */;
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
-- Table structure for table `modalidades_grado`
--

DROP TABLE IF EXISTS `modalidades_grado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modalidades_grado` (
  `id_modalidad` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requiere_tutor` tinyint(1) NOT NULL DEFAULT '0',
  `flujo` enum('perfil_mg','examen_areas','excelencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_modalidad`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nombre` (`nombre`),
  CONSTRAINT `chk_modalidad_activa` CHECK ((`activa` in (0,1))),
  CONSTRAINT `chk_modalidad_requiere_tutor` CHECK ((`requiere_tutor` in (0,1)))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modalidades_grado`
--

LOCK TABLES `modalidades_grado` WRITE;
/*!40000 ALTER TABLE `modalidades_grado` DISABLE KEYS */;
INSERT INTO `modalidades_grado` VALUES (1,'EXCELENCIA','Graduación por Excelencia','Modalidad sujeta a la normativa académica oficial.',0,'excelencia',1,'2026-09-25 19:37:34'),(2,'PROYECTO_GRADO','Proyecto de Grado','Desarrollo de una propuesta o solución aplicada.',1,'perfil_mg',1,'2026-09-25 19:37:34'),(3,'TESIS','Tesis','Trabajo de investigación académica.',1,'perfil_mg',1,'2026-09-25 19:37:34'),(4,'EXAMEN_GRADOSD','Examen de Grado','Evaluación individual organizada por áreas.',0,'examen_areas',1,'2026-09-25 19:37:34'),(5,'TRABAJO_DIRIGIDO','Trabajo Dirigido','Trabajo desarrollado dentro de una institución.',1,'perfil_mg',1,'2026-09-25 19:37:34');
/*!40000 ALTER TABLE `modalidades_grado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modalidades_graduacion`
--

DROP TABLE IF EXISTS `modalidades_graduacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modalidades_graduacion` (
  `id_modalidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_modalidad`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modalidades_graduacion`
--

LOCK TABLES `modalidades_graduacion` WRITE;
/*!40000 ALTER TABLE `modalidades_graduacion` DISABLE KEYS */;
INSERT INTO `modalidades_graduacion` VALUES (1,'Proyecto','Desarrollo de una propuesta o solución aplicada.',1,'2026-09-23 22:12:57'),(2,'Tesis','Trabajo de investigación académica.',1,'2026-09-23 22:12:57'),(3,'Trabajo Dirigido','Trabajo desarrollado dentro de una institución.',1,'2026-09-23 22:12:57');
/*!40000 ALTER TABLE `modalidades_graduacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametros_mg`
--

DROP TABLE IF EXISTS `parametros_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parametros_mg` (
  `clave` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fuente` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_evidencia` enum('confirmado','pendiente','propuesta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actualizado_por` int DEFAULT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`),
  KEY `fk_parametros_mg_usuario` (`actualizado_por`),
  CONSTRAINT `fk_parametros_mg_usuario` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametros_mg`
--

LOCK TABLES `parametros_mg` WRITE;
/*!40000 ALTER TABLE `parametros_mg` DISABLE KEYS */;
INSERT INTO `parametros_mg` VALUES ('dias_alerta_sin_reunion','10','Días sin reuniones antes de mostrar una alerta.xcddc','Plan de implementación v1','propuesta',1,'2026-09-25 20:26:13'),('dias_anticipacion_tribunal','14','Anticipación aproximada para asignar tribunales.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('duracion_mg1_meses','2','Duración aproximada de MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('duracion_mg2_meses','4','Duración aproximada de MG2.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('min_interesados_examen','12','Cantidad mencionada para habilitar Examen de Grado.','Normativa pendiente','pendiente',NULL,'2026-09-25 19:37:34'),('plazo_registro_reunion_dias','7','Días permitidos para registrar una reunión anterior.','Plan de implementación v1','propuesta',NULL,'2026-09-25 19:37:34'),('promedio_excelencia','90','Promedio mencionado para Graduación por Excelencia.','Normativa pendiente','pendiente',NULL,'2026-09-25 19:37:34'),('reuniones_min_semana_perfil','2','Cantidad mínima recomendada de reuniones semanales durante MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tribunales_por_defensa_mg1','2','Cantidad de tribunales para la defensa de MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tribunales_por_defensa_mg2','2','Cantidad provisional de tribunales para MG2.','C-02','pendiente',NULL,'2026-09-25 19:37:34'),('tutor_carga_recomendada','3','Carga recomendada de estudiantes vigentes por tutor.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tutor_max_estudiantes',NULL,'Máximo formal de estudiantes por tutor. No genera bloqueo mientras permanezca pendiente.','C-01','pendiente',NULL,'2026-09-25 19:37:34');
/*!40000 ALTER TABLE `parametros_mg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodos_inscripcion`
--

DROP TABLE IF EXISTS `periodos_inscripcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodos_inscripcion` (
  `id_periodo` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('planificado','abierto','cerrado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planificado',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_periodo`),
  UNIQUE KEY `codigo` (`codigo`),
  CONSTRAINT `chk_periodo_fechas` CHECK ((`fecha_fin` >= `fecha_inicio`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodos_inscripcion`
--

LOCK TABLES `periodos_inscripcion` WRITE;
/*!40000 ALTER TABLE `periodos_inscripcion` DISABLE KEYS */;
/*!40000 ALTER TABLE `periodos_inscripcion` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_accesos`
--

LOCK TABLES `registro_accesos` WRITE;
/*!40000 ALTER TABLE `registro_accesos` DISABLE KEYS */;
INSERT INTO `registro_accesos` VALUES (1,1,'2026-09-18 22:12:15','192.168.143.1','exitoso'),(2,1,'2026-09-18 23:55:58','192.168.178.1','exitoso'),(3,NULL,'2026-09-20 17:38:45','192.168.143.1','fallido'),(4,NULL,'2026-09-20 17:45:40','192.168.143.1','fallido'),(5,1,'2026-09-20 17:45:50','192.168.143.1','exitoso'),(6,1,'2026-09-20 19:17:27','192.168.143.1','exitoso'),(7,1,'2026-09-20 19:17:43','192.168.143.1','exitoso'),(8,7,'2026-09-20 20:23:57','192.168.143.1','exitoso'),(9,7,'2026-09-20 20:24:28','192.168.143.1','fallido'),(10,7,'2026-09-20 20:24:33','192.168.143.1','exitoso'),(11,8,'2026-09-20 20:34:51','192.168.143.1','fallido'),(12,1,'2026-09-20 20:35:08','192.168.143.1','exitoso'),(13,7,'2026-09-20 20:40:49','192.168.143.1','exitoso'),(14,1,'2026-09-20 20:41:32','192.168.143.1','exitoso'),(15,7,'2026-09-20 20:43:26','192.168.143.1','fallido'),(16,1,'2026-09-20 20:46:53','192.168.143.1','exitoso'),(17,7,'2026-09-20 20:47:46','192.168.143.1','exitoso'),(18,7,'2026-09-20 20:49:24','192.168.143.1','fallido'),(19,1,'2026-09-23 20:54:09','192.168.143.1','exitoso'),(20,2,'2026-09-23 21:25:19','192.168.143.1','fallido'),(21,1,'2026-09-23 21:25:27','192.168.143.1','exitoso'),(22,8,'2026-09-23 21:26:04','192.168.143.1','exitoso'),(23,1,'2026-09-23 21:26:49','192.168.143.1','exitoso'),(24,8,'2026-09-23 21:27:22','192.168.143.1','exitoso'),(25,1,'2026-09-24 23:47:38','192.168.178.1','exitoso'),(26,8,'2026-09-24 23:58:16','192.168.178.1','exitoso'),(27,1,'2026-09-25 00:02:18','192.168.178.1','exitoso'),(28,1,'2026-09-25 19:32:41','192.168.143.1','exitoso'),(29,1,'2026-09-25 20:06:40','192.168.143.1','exitoso');
/*!40000 ALTER TABLE `registro_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reglas_modalidad_etapa`
--

DROP TABLE IF EXISTS `reglas_modalidad_etapa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reglas_modalidad_etapa` (
  `id_regla` int NOT NULL AUTO_INCREMENT,
  `id_modalidad` int NOT NULL,
  `etapa` enum('grado_1','grado_2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimo_reuniones_semana` tinyint unsigned NOT NULL DEFAULT '0',
  `cantidad_informes` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_regla`),
  UNIQUE KEY `uq_regla_modalidad_etapa` (`id_modalidad`,`etapa`),
  CONSTRAINT `fk_reglas_modalidad` FOREIGN KEY (`id_modalidad`) REFERENCES `modalidades_graduacion` (`id_modalidad`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reglas_modalidad_etapa`
--

LOCK TABLES `reglas_modalidad_etapa` WRITE;
/*!40000 ALTER TABLE `reglas_modalidad_etapa` DISABLE KEYS */;
INSERT INTO `reglas_modalidad_etapa` VALUES (1,1,'grado_1',1,0),(2,2,'grado_1',1,0),(3,3,'grado_1',1,0),(4,1,'grado_2',0,4),(5,2,'grado_2',0,4),(6,3,'grado_2',0,4);
/*!40000 ALTER TABLE `reglas_modalidad_etapa` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrador'),(5,'auxiliar_mg'),(4,'coordinador_mg'),(3,'estudiante'),(2,'tutor');
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
-- Table structure for table `tutor_periodo`
--

DROP TABLE IF EXISTS `tutor_periodo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutor_periodo` (
  `id_tutor_periodo` int NOT NULL AUTO_INCREMENT,
  `id_tutor` int NOT NULL,
  `id_periodo` int NOT NULL,
  `cupo_maximo` tinyint unsigned NOT NULL DEFAULT '5',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_tutor_periodo`),
  UNIQUE KEY `uq_tutor_periodo` (`id_tutor`,`id_periodo`),
  KEY `fk_tutor_periodo_periodo` (`id_periodo`),
  CONSTRAINT `fk_tutor_periodo_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodos_inscripcion` (`id_periodo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tutor_periodo_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_tutor_periodo_cupo` CHECK ((`cupo_maximo` between 1 and 50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutor_periodo`
--

LOCK TABLES `tutor_periodo` WRITE;
/*!40000 ALTER TABLE `tutor_periodo` DISABLE KEYS */;
/*!40000 ALTER TABLE `tutor_periodo` ENABLE KEYS */;
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
  KEY `fk_tutorias_materia` (`id_materia`),
  KEY `idx_tutoria_fecha` (`fecha`),
  KEY `idx_tutoria_estado` (`estado`),
  KEY `fk_tutorias_tutor` (`id_tutor`),
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-25 21:43:26
