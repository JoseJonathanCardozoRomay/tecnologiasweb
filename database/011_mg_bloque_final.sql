-- =========================================================
-- MIGRACIÓN 011 - BLOQUE FINAL MÓDULO MODALIDADES DE GRADO (SPRINT 7)
-- HU-023 (importación padrón CSV), HU-027/030 (cartas y citaciones A4),
-- HU-029 (programación de defensas con anti-cruces), HU-031 (evaluación),
-- HU-032 (reportes), HU-033/038 (alertas calculadas A1-A9).
-- Idempotente.
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. Parámetros nuevos
-- ---------------------------------------------------------
INSERT INTO parametros_mg (clave, valor, descripcion, tipo) VALUES
  ('nota_aprobacion_mg', '51', 'Nota minima de aprobacion de la defensa de Modalidad de Grado.', 'entero'),
  ('tribunales_min_mg', '2', 'Numero minimo de tribunales por defensa de MG.', 'entero'),
  ('tribunales_max_mg', '5', 'Numero maximo de tribunales por defensa de MG.', 'entero')
ON DUPLICATE KEY UPDATE
  valor = VALUES(valor),
  descripcion = VALUES(descripcion),
  tipo = VALUES(tipo);

-- ---------------------------------------------------------
-- 2. correlativos_documentos (correlativos dinámicos por documento/gestion)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS correlativos_documentos (
  id_correlativo INT AUTO_INCREMENT PRIMARY KEY,
  tipo_documento VARCHAR(40) NOT NULL,
  gestion CHAR(4) NOT NULL,
  ultimo_correlativo INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_correlativo (tipo_documento, gestion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 3. ambientes_mg (aulas/salas para defensas)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS ambientes_mg (
  id_ambiente_mg INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  ubicacion VARCHAR(100),
  capacidad SMALLINT NOT NULL DEFAULT 0,
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ambientes_mg (nombre, ubicacion, capacidad, activa) VALUES
  ('Aula Magna', 'Pabellon A - 2do piso', 80, 1),
  ('Aula 201', 'Pabellon A - 2do piso', 30, 1),
  ('Aula 305', 'Pabellon B - 3er piso', 25, 1),
  ('Sala de Sesiones', 'Direccion Academica', 15, 1),
  ('Laboratorio de Audiencias', 'Pabellon C - 1er piso', 40, 1)
ON DUPLICATE KEY UPDATE ubicacion = VALUES(ubicacion), capacidad = VALUES(capacidad);

-- ---------------------------------------------------------
-- 4. defensas_mg (agenda de defensas; una por expediente)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS defensas_mg (
  id_defensa_mg INT AUTO_INCREMENT PRIMARY KEY,
  id_expediente_mg INT NOT NULL,
  id_cohorte_mg INT NOT NULL,
  id_ambiente_mg INT,
  correlativo VARCHAR(30),
  fecha_defensa DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'programada',
  nota_final DECIMAL(5,2) NULL,
  resultado VARCHAR(20) NULL,
  observaciones VARCHAR(255),
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_defensa_expediente (id_expediente_mg),
  KEY idx_defensa_fecha (fecha_defensa, hora_inicio),
  KEY idx_defensa_ambiente (id_ambiente_mg, fecha_defensa, hora_inicio),
  KEY idx_defensa_cohorte (id_cohorte_mg),
  CONSTRAINT fk_defensa_expediente FOREIGN KEY (id_expediente_mg)
    REFERENCES expedientes_mg(id_expediente_mg) ON DELETE CASCADE,
  CONSTRAINT fk_defensa_cohorte FOREIGN KEY (id_cohorte_mg)
    REFERENCES cohortes_mg(id_cohorte_mg),
  CONSTRAINT fk_defensa_ambiente FOREIGN KEY (id_ambiente_mg)
    REFERENCES ambientes_mg(id_ambiente_mg) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 5. tribunales_defensa_mg (miembros del tribunal evaluador)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tribunales_defensa_mg (
  id_tribunal_defensa_mg INT AUTO_INCREMENT PRIMARY KEY,
  id_defensa_mg INT NOT NULL,
  id_tutor INT NOT NULL,
  rol VARCHAR(30) NOT NULL DEFAULT 'miembro',
  nota DECIMAL(5,2) NULL,
  UNIQUE KEY uq_tribunal_defensa (id_defensa_mg, id_tutor),
  KEY idx_tribunal_docente (id_tutor),
  CONSTRAINT fk_tribunal_defensa FOREIGN KEY (id_defensa_mg)
    REFERENCES defensas_mg(id_defensa_mg) ON DELETE CASCADE,
  CONSTRAINT fk_tribunal_docente FOREIGN KEY (id_tutor)
    REFERENCES tutores(id_tutor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 6. alertas_mg (historial de alertas atendidas por el coordinador)
-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- 7. Columna correlativo_carta en asignaciones_tutor (HU-027)
-- ---------------------------------------------------------
SET @existe_col := (SELECT COUNT(*) FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = 'tutorias_db'
                      AND TABLE_NAME = 'asignaciones_tutor'
                      AND COLUMN_NAME = 'correlativo_carta');
SET @sql_alter := IF(@existe_col = 0,
  'ALTER TABLE asignaciones_tutor ADD COLUMN correlativo_carta VARCHAR(30) NULL AFTER estado',
  'SELECT 1');
PREPARE stmt_alter FROM @sql_alter;
EXECUTE stmt_alter;
DEALLOCATE PREPARE stmt_alter;

CREATE TABLE IF NOT EXISTS alertas_mg (
  id_alerta_mg INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  mensaje VARCHAR(300) NOT NULL,
  id_referencia INT NULL,
  atendida TINYINT(1) NOT NULL DEFAULT 0,
  atendida_por INT NULL,
  nota VARCHAR(255),
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_atencion DATETIME NULL,
  KEY idx_alertas_codigo (codigo),
  KEY idx_alertas_atendida (atendida),
  CONSTRAINT fk_alerta_usuario FOREIGN KEY (atendida_por)
    REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;