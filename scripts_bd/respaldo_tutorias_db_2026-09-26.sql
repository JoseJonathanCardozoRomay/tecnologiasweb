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
-- Table structure for table `alertas_atendidas`
--

DROP TABLE IF EXISTS `alertas_atendidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alertas_atendidas` (
  `id_alerta_atendida` int NOT NULL AUTO_INCREMENT,
  `tipo_alerta` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_referencia` int NOT NULL,
  `clave_alerta` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nota` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `atendida_por` int NOT NULL,
  `fecha_atencion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_alerta_atendida`),
  UNIQUE KEY `uq_alerta_clave` (`clave_alerta`),
  KEY `fk_alerta_usuario` (`atendida_por`),
  KEY `idx_alerta_tipo` (`tipo_alerta`),
  KEY `idx_alerta_referencia` (`id_referencia`),
  KEY `idx_alerta_fecha` (`fecha_atencion`),
  CONSTRAINT `fk_alerta_usuario` FOREIGN KEY (`atendida_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alertas_atendidas`
--

LOCK TABLES `alertas_atendidas` WRITE;
/*!40000 ALTER TABLE `alertas_atendidas` DISABLE KEYS */;
/*!40000 ALTER TABLE `alertas_atendidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignaciones_tutor`
--

DROP TABLE IF EXISTS `asignaciones_tutor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignaciones_tutor` (
  `id_asignacion` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `id_tutor` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('vigente','finalizada','reemplazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vigente',
  `motivo_fin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `asignacion_vigente` tinyint DEFAULT '1',
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `uq_asignacion_vigente` (`id_expediente`,`asignacion_vigente`),
  KEY `fk_asignaciones_registrador` (`registrado_por`),
  KEY `idx_asignaciones_expediente` (`id_expediente`),
  KEY `idx_asignaciones_tutor` (`id_tutor`),
  KEY `idx_asignaciones_estado` (`estado`),
  CONSTRAINT `fk_asignaciones_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asignaciones_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_asignaciones_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_asignacion_estado` CHECK ((((`estado` = _utf8mb4'vigente') and (`asignacion_vigente` = 1) and (`fecha_fin` is null)) or ((`estado` in (_utf8mb4'finalizada',_utf8mb4'reemplazada')) and (`asignacion_vigente` is null) and (`fecha_fin` is not null)))),
  CONSTRAINT `chk_asignacion_fechas` CHECK (((`fecha_fin` is null) or (`fecha_fin` >= `fecha_asignacion`)))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignaciones_tutor`
--

LOCK TABLES `asignaciones_tutor` WRITE;
/*!40000 ALTER TABLE `asignaciones_tutor` DISABLE KEYS */;
INSERT INTO `asignaciones_tutor` VALUES (1,2,1,'2026-09-26 15:00:35',NULL,'vigente',NULL,1,1);
/*!40000 ALTER TABLE `asignaciones_tutor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora_mg`
--

DROP TABLE IF EXISTS `bitacora_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitacora_mg` (
  `id_bitacora` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `accion` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_registro` int DEFAULT NULL,
  `datos_antes` json DEFAULT NULL,
  `datos_despues` json DEFAULT NULL,
  `direccion_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agente_usuario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bitacora`),
  KEY `idx_bitacora_usuario` (`id_usuario`),
  KEY `idx_bitacora_accion` (`accion`),
  KEY `idx_bitacora_tabla` (`tabla_afectada`),
  KEY `idx_bitacora_registro` (`tabla_afectada`,`id_registro`),
  KEY `idx_bitacora_fecha` (`fecha_registro`),
  CONSTRAINT `fk_bitacora_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora_mg`
--

LOCK TABLES `bitacora_mg` WRITE;
/*!40000 ALTER TABLE `bitacora_mg` DISABLE KEYS */;
INSERT INTO `bitacora_mg` VALUES (1,1,'reemplazar_tribunal','tribunales_defensa',3,'{\"etapa\": \"mg1\", \"orden\": 1, \"estado\": \"vigente\", \"id_tutor\": 1, \"id_tribunal\": 1, \"id_expediente\": 2}','{\"etapa\": \"mg1\", \"orden\": 1, \"estado\": \"vigente\", \"id_tutor\": 3, \"id_expediente\": 2, \"motivo_cambio\": \"disponibilidad\", \"id_tribunal_anterior\": 1}','192.168.143.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0','2026-09-26 23:45:58');
/*!40000 ALTER TABLE `bitacora_mg` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Table structure for table `calificaciones_mg`
--

DROP TABLE IF EXISTS `calificaciones_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_mg` (
  `id_calificacion` int NOT NULL AUTO_INCREMENT,
  `id_defensa` int NOT NULL,
  `nota` decimal(5,2) NOT NULL,
  `observaciones` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publicada` tinyint(1) NOT NULL DEFAULT '0',
  `registrada_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_calificacion`),
  UNIQUE KEY `uq_calificacion_defensa` (`id_defensa`),
  KEY `fk_calificacion_registrador` (`registrada_por`),
  KEY `idx_calificacion_publicada` (`publicada`),
  CONSTRAINT `fk_calificacion_defensa` FOREIGN KEY (`id_defensa`) REFERENCES `defensas_mg` (`id_defensa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_calificacion_registrador` FOREIGN KEY (`registrada_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_calificacion_publicada` CHECK ((`publicada` in (0,1)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_mg`
--

LOCK TABLES `calificaciones_mg` WRITE;
/*!40000 ALTER TABLE `calificaciones_mg` DISABLE KEYS */;
/*!40000 ALTER TABLE `calificaciones_mg` ENABLE KEYS */;
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
-- Table structure for table `cartas_designacion_mg`
--

DROP TABLE IF EXISTS `cartas_designacion_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cartas_designacion_mg` (
  `id_carta` int NOT NULL AUTO_INCREMENT,
  `id_asignacion` int NOT NULL,
  `numero_carta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_carta` smallint unsigned NOT NULL DEFAULT '1',
  `estado` enum('pendiente','aceptada','rechazada','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_generacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  `tipo_rechazo` enum('sin_capacidad','sin_tiempo','no_corresponde','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_rechazo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsabilidades` text COLLATE utf8mb4_unicode_ci,
  `documento_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_por` int NOT NULL,
  PRIMARY KEY (`id_carta`),
  UNIQUE KEY `uq_carta_mg_version` (`id_asignacion`,`version_carta`),
  UNIQUE KEY `uq_carta_mg_numero` (`numero_carta`),
  KEY `fk_cartas_mg_creador` (`creado_por`),
  KEY `idx_cartas_mg_estado` (`estado`),
  KEY `idx_cartas_mg_fecha` (`fecha_generacion`),
  CONSTRAINT `fk_cartas_mg_asignacion` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones_tutor` (`id_asignacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cartas_mg_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_carta_mg_version` CHECK ((`version_carta` >= 1))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cartas_designacion_mg`
--

LOCK TABLES `cartas_designacion_mg` WRITE;
/*!40000 ALTER TABLE `cartas_designacion_mg` DISABLE KEYS */;
INSERT INTO `cartas_designacion_mg` VALUES (1,1,NULL,1,'aceptada','2026-09-26 15:00:35','2026-09-26 15:34:42',NULL,NULL,'asdcasd',NULL,1);
/*!40000 ALTER TABLE `cartas_designacion_mg` ENABLE KEYS */;
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
-- Table structure for table `contadores_documento`
--

DROP TABLE IF EXISTS `contadores_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contadores_documento` (
  `tipo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` smallint unsigned NOT NULL,
  `ultimo_numero` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tipo`,`anio`),
  CONSTRAINT `chk_contador_numero` CHECK ((`ultimo_numero` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contadores_documento`
--

LOCK TABLES `contadores_documento` WRITE;
/*!40000 ALTER TABLE `contadores_documento` DISABLE KEYS */;
/*!40000 ALTER TABLE `contadores_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defensas_mg`
--

DROP TABLE IF EXISTS `defensas_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defensas_mg` (
  `id_defensa` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `etapa` enum('mg1','mg2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `ambiente` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('programada','realizada','reprogramada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `observaciones_fondo` text COLLATE utf8mb4_unicode_ci,
  `observaciones_forma` text COLLATE utf8mb4_unicode_ci,
  `motivo_cambio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autorizado_por` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_autorizacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `defensa_vigente` tinyint DEFAULT '1',
  PRIMARY KEY (`id_defensa`),
  UNIQUE KEY `uq_defensa_vigente` (`id_expediente`,`etapa`,`defensa_vigente`),
  KEY `fk_defensa_registrador` (`registrado_por`),
  KEY `idx_defensa_fecha` (`fecha`,`hora_inicio`,`hora_fin`),
  KEY `idx_defensa_expediente` (`id_expediente`,`etapa`),
  KEY `idx_defensa_estado` (`estado`),
  KEY `idx_defensa_ambiente` (`ambiente`,`fecha`),
  CONSTRAINT `fk_defensa_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_defensa_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_defensa_horas` CHECK ((`hora_fin` > `hora_inicio`)),
  CONSTRAINT `chk_defensa_vigente` CHECK (((`defensa_vigente` is null) or (`defensa_vigente` = 1)))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defensas_mg`
--

LOCK TABLES `defensas_mg` WRITE;
/*!40000 ALTER TABLE `defensas_mg` DISABLE KEYS */;
INSERT INTO `defensas_mg` VALUES (1,2,'mg1','2026-10-03','09:00:00','10:00:00','Aula 204','programada',NULL,NULL,NULL,NULL,NULL,1,'2026-09-26 22:10:39','2026-09-26 22:10:39',1);
/*!40000 ALTER TABLE `defensas_mg` ENABLE KEYS */;
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
-- Table structure for table `documentos_generados`
--

DROP TABLE IF EXISTS `documentos_generados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_generados` (
  `id_documento` int NOT NULL AUTO_INCREMENT,
  `id_plantilla` int NOT NULL,
  `id_expediente` int NOT NULL,
  `id_defensa` int DEFAULT NULL,
  `tipo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinatario` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_correlativo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido_snapshot` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `generado_por` int NOT NULL,
  `fecha_generacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_documento`),
  UNIQUE KEY `uq_documento_correlativo` (`numero_correlativo`),
  KEY `fk_documento_plantilla` (`id_plantilla`),
  KEY `fk_documento_generador` (`generado_por`),
  KEY `idx_documento_expediente` (`id_expediente`),
  KEY `idx_documento_defensa` (`id_defensa`),
  KEY `idx_documento_tipo` (`tipo`),
  KEY `idx_documento_fecha` (`fecha_generacion`),
  CONSTRAINT `fk_documento_defensa` FOREIGN KEY (`id_defensa`) REFERENCES `defensas_mg` (`id_defensa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_documento_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_documento_generador` FOREIGN KEY (`generado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_documento_plantilla` FOREIGN KEY (`id_plantilla`) REFERENCES `plantillas_documento` (`id_plantilla`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_generados`
--

LOCK TABLES `documentos_generados` WRITE;
/*!40000 ALTER TABLE `documentos_generados` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentos_generados` ENABLE KEYS */;
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
-- Table structure for table `evidencias_reunion_mg`
--

DROP TABLE IF EXISTS `evidencias_reunion_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evidencias_reunion_mg` (
  `id_evidencia` int NOT NULL AUTO_INCREMENT,
  `id_reunion` int NOT NULL,
  `tipo` enum('acta','fotografia','documento','enlace','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'documento',
  `nombre_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_mime` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano_bytes` int unsigned DEFAULT NULL,
  `hash_archivo` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subido_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evidencia`),
  KEY `fk_evidencias_usuario` (`subido_por`),
  KEY `idx_evidencias_reunion` (`id_reunion`),
  KEY `idx_evidencias_tipo` (`tipo`),
  KEY `idx_evidencias_hash` (`hash_archivo`),
  CONSTRAINT `fk_evidencias_reunion` FOREIGN KEY (`id_reunion`) REFERENCES `reuniones_mg` (`id_reunion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_evidencias_usuario` FOREIGN KEY (`subido_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evidencias_reunion_mg`
--

LOCK TABLES `evidencias_reunion_mg` WRITE;
/*!40000 ALTER TABLE `evidencias_reunion_mg` DISABLE KEYS */;
INSERT INTO `evidencias_reunion_mg` VALUES (1,2,'documento','sdfsfsdf.PNG','8d867604d167ffc92d9491c2ae4b3cf5.png','image/png',46599,'767aecf0aacecf378aba03d3b360e9a3bf473fbc4d22cfcb73df9e3249e47eb5','dsff',2,'2026-09-26 16:47:19');
/*!40000 ALTER TABLE `evidencias_reunion_mg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expediente_etapas`
--

DROP TABLE IF EXISTS `expediente_etapas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expediente_etapas` (
  `id_expediente_etapa` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `etapa` enum('previa','mg1','mg2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `resultado` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_cierre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_expediente_etapa`),
  KEY `fk_etapas_registrador` (`registrado_por`),
  KEY `idx_etapas_expediente` (`id_expediente`),
  KEY `idx_etapas_etapa` (`etapa`),
  KEY `idx_etapas_abiertas` (`id_expediente`,`fecha_fin`),
  CONSTRAINT `fk_etapas_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_etapas_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_etapas_fechas` CHECK (((`fecha_fin` is null) or (`fecha_fin` >= `fecha_inicio`)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expediente_etapas`
--

LOCK TABLES `expediente_etapas` WRITE;
/*!40000 ALTER TABLE `expediente_etapas` DISABLE KEYS */;
INSERT INTO `expediente_etapas` VALUES (1,1,'previa','2026-02-20',NULL,NULL,NULL,1,'2026-09-25 21:55:23'),(2,2,'previa','2026-03-01','2026-09-26','completada','Tutor asignado al expediente.',1,'2026-09-26 14:43:13'),(3,2,'mg1','2026-09-26',NULL,NULL,NULL,1,'2026-09-26 15:00:35');
/*!40000 ALTER TABLE `expediente_etapas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expedientes_mg`
--

DROP TABLE IF EXISTS `expedientes_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expedientes_mg` (
  `id_expediente` int NOT NULL AUTO_INCREMENT,
  `id_estudiante` int NOT NULL,
  `id_modalidad` int NOT NULL,
  `id_cohorte` int NOT NULL,
  `etapa_actual` enum('previa','mg1','mg2','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'previa',
  `estado` enum('activo','aprobado','reprobado','abandono','retirado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `titulo_trabajo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `creado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_expediente`),
  UNIQUE KEY `uq_expediente_proceso` (`id_estudiante`,`id_modalidad`,`id_cohorte`),
  KEY `fk_expedientes_creador` (`creado_por`),
  KEY `idx_expedientes_modalidad` (`id_modalidad`),
  KEY `idx_expedientes_cohorte` (`id_cohorte`),
  KEY `idx_expedientes_etapa` (`etapa_actual`),
  KEY `idx_expedientes_estado` (`estado`),
  CONSTRAINT `fk_expedientes_cohorte` FOREIGN KEY (`id_cohorte`) REFERENCES `cohortes_mg` (`id_cohorte`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_expedientes_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_expedientes_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_expedientes_modalidad` FOREIGN KEY (`id_modalidad`) REFERENCES `modalidades_grado` (`id_modalidad`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_expediente_fechas` CHECK (((`fecha_cierre` is null) or (`fecha_cierre` >= `fecha_inicio`)))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expedientes_mg`
--

LOCK TABLES `expedientes_mg` WRITE;
/*!40000 ALTER TABLE `expedientes_mg` DISABLE KEYS */;
INSERT INTO `expedientes_mg` VALUES (1,3,4,1,'previa','activo','tarea 1','2026-02-20',NULL,'ponga mas documentaciondd',1,'2026-09-25 21:55:23'),(2,1,2,1,'mg1','activo','tarea 1','2026-03-01',NULL,'ff',1,'2026-09-26 14:43:13');
/*!40000 ALTER TABLE `expedientes_mg` ENABLE KEYS */;
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
-- Table structure for table `importaciones_mg`
--

DROP TABLE IF EXISTS `importaciones_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `importaciones_mg` (
  `id_importacion` int NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash_archivo` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('previsualizada','procesando','completada','fallida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'previsualizada',
  `total_filas` int unsigned NOT NULL DEFAULT '0',
  `filas_correctas` int unsigned NOT NULL DEFAULT '0',
  `filas_advertencia` int unsigned NOT NULL DEFAULT '0',
  `filas_error` int unsigned NOT NULL DEFAULT '0',
  `filas_creadas` int unsigned NOT NULL DEFAULT '0',
  `registrado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_procesamiento` datetime DEFAULT NULL,
  PRIMARY KEY (`id_importacion`),
  KEY `fk_importaciones_usuario` (`registrado_por`),
  KEY `idx_importaciones_hash` (`hash_archivo`),
  KEY `idx_importaciones_estado` (`estado`),
  KEY `idx_importaciones_fecha` (`fecha_registro`),
  CONSTRAINT `fk_importaciones_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `importaciones_mg`
--

LOCK TABLES `importaciones_mg` WRITE;
/*!40000 ALTER TABLE `importaciones_mg` DISABLE KEYS */;
/*!40000 ALTER TABLE `importaciones_mg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `importaciones_mg_detalle`
--

DROP TABLE IF EXISTS `importaciones_mg_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `importaciones_mg_detalle` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_importacion` int NOT NULL,
  `numero_fila` int unsigned NOT NULL,
  `registro_universitario` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrera` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `semestre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modalidad` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cohorte` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resultado` enum('correcta','advertencia','error','pendiente_cuenta','creada','omitida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datos_originales` json DEFAULT NULL,
  `id_expediente` int DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detalle`),
  UNIQUE KEY `uq_importacion_numero_fila` (`id_importacion`,`numero_fila`),
  KEY `fk_detalle_expediente` (`id_expediente`),
  KEY `idx_detalle_resultado` (`resultado`),
  KEY `idx_detalle_registro` (`registro_universitario`),
  CONSTRAINT `fk_detalle_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_importacion_detalle` FOREIGN KEY (`id_importacion`) REFERENCES `importaciones_mg` (`id_importacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `importaciones_mg_detalle`
--

LOCK TABLES `importaciones_mg_detalle` WRITE;
/*!40000 ALTER TABLE `importaciones_mg_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `importaciones_mg_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informes_avance_mg`
--

DROP TABLE IF EXISTS `informes_avance_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `informes_avance_mg` (
  `id_informe` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `id_hito` int DEFAULT NULL,
  `etapa` enum('mg1','mg2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_informe` tinyint unsigned NOT NULL,
  `fecha_informe` date NOT NULL,
  `porcentaje_avance` tinyint unsigned NOT NULL DEFAULT '0',
  `resumen_avance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logros` text COLLATE utf8mb4_unicode_ci,
  `dificultades` text COLLATE utf8mb4_unicode_ci,
  `recomendaciones` text COLLATE utf8mb4_unicode_ci,
  `proximas_actividades` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('borrador','presentado','aprobado','observado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `observacion_revision` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revisado_por` int DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_informe`),
  UNIQUE KEY `uq_informe_expediente_etapa` (`id_expediente`,`etapa`,`numero_informe`),
  KEY `fk_informes_hito` (`id_hito`),
  KEY `fk_informes_registrador` (`registrado_por`),
  KEY `fk_informes_revisor` (`revisado_por`),
  KEY `idx_informes_expediente` (`id_expediente`),
  KEY `idx_informes_etapa` (`etapa`),
  KEY `idx_informes_estado` (`estado`),
  KEY `idx_informes_fecha` (`fecha_informe`),
  CONSTRAINT `fk_informes_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_informes_hito` FOREIGN KEY (`id_hito`) REFERENCES `calendario_mg` (`id_hito`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_informes_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_informes_revisor` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_informe_avance` CHECK ((`porcentaje_avance` between 0 and 100)),
  CONSTRAINT `chk_informe_numero` CHECK ((`numero_informe` between 1 and 20))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informes_avance_mg`
--

LOCK TABLES `informes_avance_mg` WRITE;
/*!40000 ALTER TABLE `informes_avance_mg` DISABLE KEYS */;
INSERT INTO `informes_avance_mg` VALUES (1,2,NULL,'mg1',1,'2026-09-26',49,'sdrfsefsfsfsfsfs','sdfsf','sdfs','fsfsf','fsfs','aprobado',NULL,1,'2026-09-26 19:12:54',2,'2026-09-26 18:15:41','2026-09-26 19:12:54'),(2,2,NULL,'mg1',2,'2026-09-26',80,'ascvbcbcvbvcbcdadad','cvbcvb','cvbcv','cvbvcb','cvbcbc','aprobado',NULL,1,'2026-09-26 19:14:47',2,'2026-09-26 19:13:45','2026-09-26 19:14:47');
/*!40000 ALTER TABLE `informes_avance_mg` ENABLE KEYS */;
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
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `tipo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_destino` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave_evento` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_lectura` datetime DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  UNIQUE KEY `uq_notificacion_evento_usuario` (`id_usuario`,`clave_evento`),
  KEY `idx_notificacion_usuario_leida` (`id_usuario`,`leida`),
  KEY `idx_notificacion_fecha` (`fecha_registro`),
  KEY `idx_notificacion_tipo` (`tipo`),
  CONSTRAINT `fk_notificacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_notificacion_leida` CHECK ((`leida` in (0,1)))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
INSERT INTO `notificaciones` VALUES (1,3,'defensa_programada','Defensa programada','Tu defensa de MG1 fue programada para el 03/10/2026 a horas 09:00.','controllers/mi_proceso_listar.php','defensa_estudiante:1',0,'2026-09-26 23:12:23',NULL),(5,2,'tribunal_defensa','Participación en defensa','Fuiste asignado como Tribunal 1 para una defensa de MG1 programada el 03/10/2026.','controllers/documento_ver.php?id=0','defensa_tribunal:1:1',0,'2026-09-26 23:17:34',NULL),(6,8,'tribunal_defensa','Participación en defensa','Fuiste asignado como Tribunal 2 para una defensa de MG1 programada el 03/10/2026.','controllers/documento_ver.php?id=0','defensa_tribunal:1:2',0,'2026-09-26 23:17:34',NULL),(9,10,'tribunal_defensa','Participación en defensa','Fuiste asignado como Tribunal 1 para una defensa de MG1 programada el 03/10/2026.','controllers/documento_ver.php?id=0','defensa_tribunal:1:3',0,'2026-09-27 00:15:29',NULL);
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
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
INSERT INTO `parametros_mg` VALUES ('calificacion_maxima','100','Valor máximo permitido para una calificación.','PROPUESTA MVP','propuesta',NULL,'2026-09-26 22:16:46'),('calificacion_minima','0','Valor mínimo permitido para una calificación.','PROPUESTA MVP','propuesta',NULL,'2026-09-26 22:16:46'),('dias_alerta_sin_reunion','10','Días sin reuniones antes de mostrar una alerta.xcddc','Plan de implementación v1','propuesta',1,'2026-09-25 20:26:13'),('dias_anticipacion_tribunal','14','Anticipación aproximada para asignar tribunales.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('duracion_mg1_meses','2','Duración aproximada de MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('duracion_mg2_meses','4','Duración aproximada de MG2.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('min_interesados_examen','12','Cantidad mencionada para habilitar Examen de Grado.','Normativa pendiente','pendiente',NULL,'2026-09-25 19:37:34'),('plazo_registro_reunion_dias','7','Días permitidos para registrar una reunión anterior.','Plan de implementación v1','propuesta',NULL,'2026-09-25 19:37:34'),('promedio_excelencia','90','Promedio mencionado para Graduación por Excelencia.','Normativa pendiente','pendiente',NULL,'2026-09-25 19:37:34'),('reuniones_min_semana_perfil','2','Cantidad mínima recomendada de reuniones semanales durante MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tribunales_por_defensa_mg1','2','Cantidad de tribunales para la defensa de MG1.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tribunales_por_defensa_mg2','2','Cantidad provisional de tribunales para MG2.','C-02','pendiente',NULL,'2026-09-25 19:37:34'),('tutor_carga_recomendada','3','Carga recomendada de estudiantes vigentes por tutor.','ENT-03','confirmado',NULL,'2026-09-25 19:37:34'),('tutor_max_estudiantes',NULL,'Máximo formal de estudiantes por tutor. No genera bloqueo mientras permanezca pendiente.','C-01','pendiente',NULL,'2026-09-25 19:37:34');
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
-- Table structure for table `plantillas_documento`
--

DROP TABLE IF EXISTS `plantillas_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plantillas_documento` (
  `id_plantilla` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuerpo_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` smallint unsigned NOT NULL DEFAULT '1',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `actualizado_por` int DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plantilla`),
  UNIQUE KEY `uq_plantilla_codigo` (`codigo`),
  KEY `fk_plantilla_actualizador` (`actualizado_por`),
  KEY `idx_plantilla_activa` (`activa`),
  CONSTRAINT `fk_plantilla_actualizador` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_plantilla_activa` CHECK ((`activa` in (0,1))),
  CONSTRAINT `chk_plantilla_version` CHECK ((`version` >= 1))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plantillas_documento`
--

LOCK TABLES `plantillas_documento` WRITE;
/*!40000 ALTER TABLE `plantillas_documento` DISABLE KEYS */;
INSERT INTO `plantillas_documento` VALUES (1,'CITACION_TRIBUNAL','Citación para tribunal de defensa','<h1>Citación a defensa</h1>\n    <p><strong>[PLANTILLA PROVISIONAL]</strong></p>\n    <p>Se comunica a {{destinatario}} que ha sido designado como Tribunal {{orden_tribunal}} de la defensa correspondiente a:</p>\n    <p><strong>Estudiante:</strong> {{estudiante_nombre}}</p>\n    <p><strong>Modalidad:</strong> {{modalidad}}</p>\n    <p><strong>Trabajo:</strong> {{titulo_trabajo}}</p>\n    <p><strong>Etapa:</strong> {{etapa}}</p>\n    <p><strong>Fecha:</strong> {{fecha_defensa}}</p>\n    <p><strong>Horario:</strong> {{horario}}</p>\n    <p><strong>Ambiente:</strong> {{ambiente}}</p>\n    <p><strong>Número:</strong> {{numero_documento}}</p>',1,1,NULL,'2026-09-26 22:25:05','2026-09-26 22:25:05'),(2,'CITACION_ESTUDIANTE','Citación para estudiante','<h1>Citación a defensa</h1>\n    <p><strong>[PLANTILLA PROVISIONAL]</strong></p>\n    <p>Se comunica a {{destinatario}} la programación de su defensa académica.</p>\n    <p><strong>Modalidad:</strong> {{modalidad}}</p>\n    <p><strong>Trabajo:</strong> {{titulo_trabajo}}</p>\n    <p><strong>Etapa:</strong> {{etapa}}</p>\n    <p><strong>Fecha:</strong> {{fecha_defensa}}</p>\n    <p><strong>Horario:</strong> {{horario}}</p>\n    <p><strong>Ambiente:</strong> {{ambiente}}</p>\n    <p><strong>Número:</strong> {{numero_documento}}</p>',1,1,NULL,'2026-09-26 22:25:05','2026-09-26 22:25:05');
/*!40000 ALTER TABLE `plantillas_documento` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_accesos`
--

LOCK TABLES `registro_accesos` WRITE;
/*!40000 ALTER TABLE `registro_accesos` DISABLE KEYS */;
INSERT INTO `registro_accesos` VALUES (1,1,'2026-09-18 22:12:15','192.168.143.1','exitoso'),(2,1,'2026-09-18 23:55:58','192.168.178.1','exitoso'),(3,NULL,'2026-09-20 17:38:45','192.168.143.1','fallido'),(4,NULL,'2026-09-20 17:45:40','192.168.143.1','fallido'),(5,1,'2026-09-20 17:45:50','192.168.143.1','exitoso'),(6,1,'2026-09-20 19:17:27','192.168.143.1','exitoso'),(7,1,'2026-09-20 19:17:43','192.168.143.1','exitoso'),(8,7,'2026-09-20 20:23:57','192.168.143.1','exitoso'),(9,7,'2026-09-20 20:24:28','192.168.143.1','fallido'),(10,7,'2026-09-20 20:24:33','192.168.143.1','exitoso'),(11,8,'2026-09-20 20:34:51','192.168.143.1','fallido'),(12,1,'2026-09-20 20:35:08','192.168.143.1','exitoso'),(13,7,'2026-09-20 20:40:49','192.168.143.1','exitoso'),(14,1,'2026-09-20 20:41:32','192.168.143.1','exitoso'),(15,7,'2026-09-20 20:43:26','192.168.143.1','fallido'),(16,1,'2026-09-20 20:46:53','192.168.143.1','exitoso'),(17,7,'2026-09-20 20:47:46','192.168.143.1','exitoso'),(18,7,'2026-09-20 20:49:24','192.168.143.1','fallido'),(19,1,'2026-09-23 20:54:09','192.168.143.1','exitoso'),(20,2,'2026-09-23 21:25:19','192.168.143.1','fallido'),(21,1,'2026-09-23 21:25:27','192.168.143.1','exitoso'),(22,8,'2026-09-23 21:26:04','192.168.143.1','exitoso'),(23,1,'2026-09-23 21:26:49','192.168.143.1','exitoso'),(24,8,'2026-09-23 21:27:22','192.168.143.1','exitoso'),(25,1,'2026-09-24 23:47:38','192.168.178.1','exitoso'),(26,8,'2026-09-24 23:58:16','192.168.178.1','exitoso'),(27,1,'2026-09-25 00:02:18','192.168.178.1','exitoso'),(28,1,'2026-09-25 19:32:41','192.168.143.1','exitoso'),(29,1,'2026-09-25 20:06:40','192.168.143.1','exitoso'),(30,1,'2026-09-25 23:58:44','192.168.178.1','exitoso'),(31,1,'2026-09-26 00:39:46','192.168.178.1','exitoso'),(32,1,'2026-09-26 14:42:18','192.168.143.1','exitoso'),(33,2,'2026-09-26 15:26:20','192.168.143.1','exitoso'),(34,1,'2026-09-26 18:10:22','192.168.143.1','fallido'),(35,1,'2026-09-26 18:10:25','192.168.143.1','exitoso'),(36,2,'2026-09-26 18:10:35','192.168.143.1','exitoso'),(37,1,'2026-09-26 19:10:08','192.168.143.1','exitoso'),(38,2,'2026-09-26 19:11:45','192.168.143.1','fallido'),(39,2,'2026-09-26 19:11:49','192.168.143.1','exitoso'),(40,1,'2026-09-26 19:12:29','192.168.143.1','exitoso'),(41,5,'2026-09-26 19:48:17','192.168.143.1','fallido'),(42,5,'2026-09-26 19:48:29','192.168.143.1','fallido'),(43,5,'2026-09-26 19:48:35','192.168.143.1','fallido'),(44,5,'2026-09-26 19:48:39','192.168.143.1','fallido'),(45,5,'2026-09-26 19:49:10','192.168.143.1','exitoso'),(46,5,'2026-09-26 20:58:42','192.168.143.1','exitoso'),(47,1,'2026-09-26 21:22:16','192.168.143.1','fallido'),(48,1,'2026-09-26 21:22:19','192.168.143.1','exitoso'),(49,1,'2026-09-26 21:44:52','192.168.143.1','exitoso'),(50,5,'2026-09-26 22:45:50','192.168.143.1','exitoso'),(51,1,'2026-09-26 22:46:24','192.168.143.1','exitoso'),(52,1,'2026-09-27 00:01:32','192.168.143.1','exitoso'),(53,1,'2026-09-27 00:10:26','192.168.143.1','exitoso'),(54,1,'2026-09-27 00:10:32','192.168.143.1','fallido'),(55,1,'2026-09-27 00:15:29','192.168.143.1','exitoso');
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
-- Table structure for table `reuniones_mg`
--

DROP TABLE IF EXISTS `reuniones_mg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reuniones_mg` (
  `id_reunion` int NOT NULL AUTO_INCREMENT,
  `id_asignacion` int NOT NULL,
  `fecha_reunion` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `modalidad` enum('presencial','virtual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lugar_enlace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tema` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acuerdos` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('programada','realizada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `asistencia_tutor` enum('pendiente','presente','ausente','justificada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `asistencia_estudiante` enum('pendiente','presente','ausente','justificada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `estado_validacion` enum('pendiente','validada','observada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observacion_validacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validada_por` int DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reunion`),
  KEY `fk_reuniones_registrador` (`registrado_por`),
  KEY `fk_reuniones_validador` (`validada_por`),
  KEY `idx_reuniones_asignacion` (`id_asignacion`),
  KEY `idx_reuniones_fecha` (`fecha_reunion`),
  KEY `idx_reuniones_estado` (`estado`),
  KEY `idx_reuniones_validacion` (`estado_validacion`),
  CONSTRAINT `fk_reuniones_asignacion` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones_tutor` (`id_asignacion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_reuniones_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_reuniones_validador` FOREIGN KEY (`validada_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_reunion_horario` CHECK ((`hora_fin` > `hora_inicio`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reuniones_mg`
--

LOCK TABLES `reuniones_mg` WRITE;
/*!40000 ALTER TABLE `reuniones_mg` DISABLE KEYS */;
INSERT INTO `reuniones_mg` VALUES (1,1,'2026-09-30','08:00:00','10:00:00','presencial','aula a-204','Control de actividades','lista 10m despues de inicio','reunion importante','programada','pendiente','pendiente','pendiente',NULL,NULL,NULL,2,'2026-09-26 16:36:20','2026-09-26 16:36:20'),(2,1,'2026-09-26','12:00:00','13:00:00','presencial','aula a-204','Control de actividades','sdf','sadfs','realizada','presente','presente','pendiente',NULL,NULL,NULL,2,'2026-09-26 16:46:34','2026-09-26 16:46:50');
/*!40000 ALTER TABLE `reuniones_mg` ENABLE KEYS */;
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
-- Table structure for table `tribunales_defensa`
--

DROP TABLE IF EXISTS `tribunales_defensa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tribunales_defensa` (
  `id_tribunal` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `etapa` enum('mg1','mg2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_tutor` int NOT NULL,
  `orden` tinyint unsigned NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('vigente','reemplazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vigente',
  `motivo_cambio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` int NOT NULL,
  `asignacion_vigente` tinyint DEFAULT '1',
  PRIMARY KEY (`id_tribunal`),
  UNIQUE KEY `uq_tribunal_orden_vigente` (`id_expediente`,`etapa`,`orden`,`asignacion_vigente`),
  UNIQUE KEY `uq_tribunal_docente_vigente` (`id_expediente`,`etapa`,`id_tutor`,`asignacion_vigente`),
  KEY `fk_tribunal_registrador` (`registrado_por`),
  KEY `idx_tribunal_expediente` (`id_expediente`,`etapa`),
  KEY `idx_tribunal_tutor` (`id_tutor`),
  KEY `idx_tribunal_estado` (`estado`),
  CONSTRAINT `fk_tribunal_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes_mg` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tribunal_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_tribunal_tutor` FOREIGN KEY (`id_tutor`) REFERENCES `tutores` (`id_tutor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_tribunal_orden` CHECK ((`orden` between 1 and 10)),
  CONSTRAINT `chk_tribunal_vigente` CHECK (((`asignacion_vigente` is null) or (`asignacion_vigente` = 1)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tribunales_defensa`
--

LOCK TABLES `tribunales_defensa` WRITE;
/*!40000 ALTER TABLE `tribunales_defensa` DISABLE KEYS */;
INSERT INTO `tribunales_defensa` VALUES (1,2,'mg1',1,1,'2026-09-26 21:44:35','2026-09-26 23:45:58','reemplazado','disponibilidad',1,NULL),(2,2,'mg1',2,2,'2026-09-26 21:44:35',NULL,'vigente',NULL,1,1),(3,2,'mg1',3,1,'2026-09-26 23:45:58',NULL,'vigente',NULL,1,1);
/*!40000 ALTER TABLE `tribunales_defensa` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
INSERT INTO `tutores` VALUES (1,2,'Desarrollo Web y Bases de Datos','Docente tutor especializado en desarrollo backend y arquitecturas web.'),(2,8,'Desarrollo web','ddd'),(3,10,'Metodología de investigación','Tutora creada para comprobar asignaciones y reemplazos de tribunal.');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,'Admin','Sistema','admin@tutorias.local','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000001','activo','2026-09-18 22:11:05'),(2,2,'Carlos','Docente','tutor@tutorias.local','tutor1','$2y$12$nJ.irCksJIJdo1JjPMgkRe3QQ5BAAg2Oa/VTKqhygdHf2nhlBZPUS','70000002','activo','2026-09-18 22:11:05'),(3,3,'Maria','Estudiante','estudiante@tutorias.local','estudiante1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000003','activo','2026-09-18 22:11:05'),(5,3,'raul','Sandoval','paul@upds.edu.bo','angel123','$2y$12$Qe2BmlVnpWYOLalsC3CuFePCbr3ygb/AVOxoyXmwcfIn73JUVMdTW',NULL,'activo','2026-09-18 22:31:38'),(6,1,'juan','fernando','juan123@gmail.com','juan12','$2y$12$muFtZoyMm61UAHyfK094KevYiyZLkWy6ZSC/7J9RVgUUtkcNTqbzu','77175134','activo','2026-09-19 00:54:49'),(7,1,'manuel','morales','manuel@gmail.com','manuel123','$2y$12$s5G7HXE6U8QVpkYMz2Aepeb9maZWs8OeDzwlZQCL9PB77HK4aLMaa','77174567','activo','2026-09-20 20:04:45'),(8,2,'saul','ortega','saul@gmail.com','tutor2','$2y$12$UTeQUMMccHdQT6FKliqDk.GDUHl2.4ZRH4IqmO9Mjq2aTub6/rSve','81174569','activo','2026-09-20 20:34:27'),(9,3,'Ana','Pérez','ana@gmail.com','anaperez','$2y$12$.9Icgv8SMT1ZGOTb9AAsPOeu9W2QkIcpSckOs6uRl1BFwC50ZleNS',NULL,'activo','2026-09-20 21:47:11'),(10,2,'Laura','Mendoza','laura.mendoza.mg@prueba.local','tutor_prueba_mg','$2y$12$1fQ10B3m0hHzlhX61ecf6e/r7AwAQIsCtpk2FyleAKTF7jtYtIST6','70000001','activo','2026-09-26 23:44:41');
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

-- Dump completed on 2026-09-27  0:22:51
