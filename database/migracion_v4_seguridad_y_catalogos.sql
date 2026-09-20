-- ============================================================
-- MIGRACIÓN V4 - SEGURIDAD, CATÁLOGOS Y TRAZABILIDAD
-- Sistema de Tutorías UPDS
-- ============================================================
-- Esta migración es no destructiva. Antes de aplicarla sobre una
-- base con datos propios, mantén una copia de seguridad.

USE tutorias_db;

-- Carreras: baja lógica para no perder historial académico.
SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='carreras' AND COLUMN_NAME='estado'),
    'SELECT 1',
    "ALTER TABLE carreras ADD COLUMN estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo' AFTER nombre_carrera"
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='carreras' AND INDEX_NAME='idx_carreras_estado'),
    'SELECT 1',
    'ALTER TABLE carreras ADD INDEX idx_carreras_estado (estado)'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Materias: baja lógica para no perder tutorías/asignaciones históricas.
SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='materias' AND COLUMN_NAME='estado'),
    'SELECT 1',
    "ALTER TABLE materias ADD COLUMN estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo' AFTER id_carrera"
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='materias' AND INDEX_NAME='idx_materias_estado'),
    'SELECT 1',
    'ALTER TABLE materias ADD INDEX idx_materias_estado (estado)'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índice de auditoría para filtros por fecha.
SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='registro_accesos' AND INDEX_NAME='idx_auditoria_fecha'),
    'SELECT 1',
    'ALTER TABLE registro_accesos ADD INDEX idx_auditoria_fecha (fecha_hora)'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;



-- ------------------------------------------------------------
-- Cancelaciones: trazabilidad de quién canceló, cuándo y por qué.
-- ------------------------------------------------------------
SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND COLUMN_NAME='motivo_cancelacion'),
    'SELECT 1',
    "ALTER TABLE tutorias ADD COLUMN motivo_cancelacion VARCHAR(500) NULL AFTER observaciones"
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND COLUMN_NAME='cancelado_por_usuario'),
    'SELECT 1',
    "ALTER TABLE tutorias ADD COLUMN cancelado_por_usuario INT NULL AFTER motivo_cancelacion"
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND COLUMN_NAME='fecha_cancelacion'),
    'SELECT 1',
    "ALTER TABLE tutorias ADD COLUMN fecha_cancelacion DATETIME NULL AFTER cancelado_por_usuario"
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND INDEX_NAME='idx_tutorias_fecha_cancelacion'),
    'SELECT 1',
    'ALTER TABLE tutorias ADD INDEX idx_tutorias_fecha_cancelacion (fecha_cancelacion)'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND INDEX_NAME='idx_tutorias_cancelado_por'),
    'SELECT 1',
    'ALTER TABLE tutorias ADD INDEX idx_tutorias_cancelado_por (cancelado_por_usuario)'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tutorias' AND CONSTRAINT_NAME='fk_tutorias_cancelado_por'),
    'SELECT 1',
    'ALTER TABLE tutorias ADD CONSTRAINT fk_tutorias_cancelado_por FOREIGN KEY (cancelado_por_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT 'Migración V4 completada.' AS resultado;
