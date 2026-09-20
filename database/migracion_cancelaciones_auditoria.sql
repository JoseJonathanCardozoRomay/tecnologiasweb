-- ============================================================
-- MIGRACIÓN SEGURA - AUDITORÍA DE CANCELACIONES
-- Sistema de Tutorías UPDS
-- ============================================================
-- Ejecutar sobre una base existente después de realizar un backup.
-- No modifica config/conexion.php ni Docker.
-- ============================================================

USE tutorias_db;

-- Agrega los campos solo si todavía no existen.
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND COLUMN_NAME='motivo_cancelacion'
        ),
        'SELECT 1',
        "ALTER TABLE tutorias ADD COLUMN motivo_cancelacion VARCHAR(500) NULL AFTER observaciones"
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND COLUMN_NAME='cancelado_por_usuario'
        ),
        'SELECT 1',
        "ALTER TABLE tutorias ADD COLUMN cancelado_por_usuario INT NULL AFTER motivo_cancelacion"
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND COLUMN_NAME='fecha_cancelacion'
        ),
        'SELECT 1',
        "ALTER TABLE tutorias ADD COLUMN fecha_cancelacion DATETIME NULL AFTER cancelado_por_usuario"
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índices.
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND INDEX_NAME='idx_tutorias_fecha_cancelacion'
        ),
        'SELECT 1',
        'ALTER TABLE tutorias ADD INDEX idx_tutorias_fecha_cancelacion (fecha_cancelacion)'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND INDEX_NAME='idx_tutorias_cancelado_por'
        ),
        'SELECT 1',
        'ALTER TABLE tutorias ADD INDEX idx_tutorias_cancelado_por (cancelado_por_usuario)'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK solo si todavía no existe.
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA=DATABASE()
              AND TABLE_NAME='tutorias'
              AND CONSTRAINT_NAME='fk_tutorias_cancelado_por'
              AND CONSTRAINT_TYPE='FOREIGN KEY'
        ),
        'SELECT 1',
        'ALTER TABLE tutorias ADD CONSTRAINT fk_tutorias_cancelado_por FOREIGN KEY (cancelado_por_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT
    'OK' AS resultado,
    COUNT(*) AS tutorias_canceladas
FROM tutorias
WHERE estado='cancelada';
