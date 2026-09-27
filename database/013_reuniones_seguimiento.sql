-- ============================================================================
-- SPRINT 8 - Migracion 013
-- Reunion: seguimiento posterior + informe final + IDs autoincrementales
-- Tutor: solicitudes de actualizacion de materias/horarios
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) Seguimiento post-reunion.
--    La asistencia y el cumplimiento dejan de capturarse al agendar y se
--    registran unicamente despues de que la reunion se realizo.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reunion_seguimientos (
    id_seguimiento     INT NOT NULL AUTO_INCREMENT,
    id_reunion         INT NOT NULL,
    id_usuario_registro INT NOT NULL,
    asistencia         ENUM('si','no','tardanza') NOT NULL DEFAULT 'si',
    cumplimiento       ENUM('completo','parcial','pendiente') NOT NULL DEFAULT 'pendiente',
    observaciones      TEXT NULL,
    compromisos        TEXT NULL,
    fecha_registro     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NULL,
    PRIMARY KEY (id_seguimiento),
    UNIQUE KEY uq_reunion_seguimiento (id_reunion),
    KEY fk_seguimiento_reunion (id_reunion),
    KEY fk_seguimiento_usuario (id_usuario_registro),
    CONSTRAINT fk_seguimiento_reunion
        FOREIGN KEY (id_reunion) REFERENCES reuniones (id_reunion) ON DELETE CASCADE,
    CONSTRAINT fk_seguimiento_usuario
        FOREIGN KEY (id_usuario_registro) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 2) Informe final generado por el tutor tras el seguimiento.
-- ----------------------------------------------------------------------------
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'reuniones'
        AND COLUMN_NAME = 'informe_url') = 0,
    'ALTER TABLE reuniones ADD COLUMN informe_url VARCHAR(255) NULL AFTER observaciones',
    'DO 0');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 3) Solicitudes de actualizacion de materias / horarios / carreras.
--    Solo el Administrador (o Auxiliar) resuelve; el Tutor solo solicita.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS solicitudes_actualizacion_tutor (
    id_solicitud        INT NOT NULL AUTO_INCREMENT,
    id_tutor            INT NOT NULL,
    id_usuario_solicitante INT NOT NULL,
    tipo                ENUM('materias','horarios','carreras') NOT NULL DEFAULT 'materias',
    detalle             TEXT NULL,
    estado              ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
    respuesta           TEXT NULL,
    id_usuario_responde INT NULL,
    fecha_solicitud     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta     DATETIME NULL,
    PRIMARY KEY (id_solicitud),
    KEY fk_sat_tutor (id_tutor),
    KEY fk_sat_solicitante (id_usuario_solicitante),
    KEY fk_sat_responde (id_usuario_responde),
    CONSTRAINT fk_sat_tutor
        FOREIGN KEY (id_tutor) REFERENCES tutores (id_tutor) ON DELETE CASCADE,
    CONSTRAINT fk_sat_solicitante
        FOREIGN KEY (id_usuario_solicitante) REFERENCES usuarios (id_usuario),
    CONSTRAINT fk_sat_responde
        FOREIGN KEY (id_usuario_responde) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 4) Garantia de AUTO_INCREMENT gestionado por el motor.
--    Reejecutar la migracion no debe fallar si el atributo ya existe.
-- ----------------------------------------------------------------------------
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'reuniones'
        AND COLUMN_NAME = 'id_reunion'
        AND EXTRA LIKE '%auto_increment%') = 0,
    'ALTER TABLE reuniones MODIFY id_reunion INT NOT NULL AUTO_INCREMENT',
    'DO 0');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'reunion_seguimientos'
        AND COLUMN_NAME = 'id_seguimiento'
        AND EXTRA LIKE '%auto_increment%') = 0,
    'ALTER TABLE reunion_seguimientos MODIFY id_seguimiento INT NOT NULL AUTO_INCREMENT',
    'DO 0');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'solicitudes_actualizacion_tutor'
        AND COLUMN_NAME = 'id_solicitud'
        AND EXTRA LIKE '%auto_increment%') = 0,
    'ALTER TABLE solicitudes_actualizacion_tutor MODIFY id_solicitud INT NOT NULL AUTO_INCREMENT',
    'DO 0');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
