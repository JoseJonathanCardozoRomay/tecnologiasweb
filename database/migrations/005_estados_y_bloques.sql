-- =========================================================
-- MIGRACIÓN 005: Nuevos estados de tutoría, periodos y bloques horarios
-- Sistema de Tutorías UPDS
-- ---------------------------------------------------------
-- Aplica sobre una base ya existente. No volver a ejecutar init.sql.
-- No incluye USE: la base se selecciona al invocar el cliente
-- (mysql -uroot -p... tutorias_db < archivo) o la conexión PDO.
--
-- Objetivo: que el ESTUDIANTE nunca defina carrera, fechas base del periodo,
-- bloques horarios ni modalidad. El coordinador/admin define el rango de fechas
-- (periodos_tutoria) y los bloques (bloques_horarios); el estudiante solo elige
-- materia (de su carrera), tutor, bloque y una fecha dentro del rango permitido.
-- =========================================================

-- ---------------------------------------------------------
-- A) Nuevos estados de tutoría: en_proceso y detenido
--    Se amplía el ENUM existente sin perder los valores actuales.
-- ---------------------------------------------------------
ALTER TABLE tutorias MODIFY COLUMN estado
  ENUM('pendiente','confirmada','realizada','cancelada','en_proceso','detenido')
  NOT NULL DEFAULT 'pendiente';

-- ---------------------------------------------------------
-- B) Tabla periodos_tutoria (rango de fechas definido por coordinador/admin)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS periodos_tutoria (
  id_periodo INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) NOT NULL UNIQUE,         -- ej: 'I-2026', 'II-2026', 'Verano-2026'
  nombre VARCHAR(100) NOT NULL,               -- ej: '2026-1 Primer Semestre'
  fecha_inicio DATE NOT NULL,                 -- primera fecha disponible para tutorías
  fecha_fin DATE NOT NULL,                    -- última fecha disponible
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_por INT NULL,                        -- id_usuario del coordinador/admin
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_periodo_fechas CHECK (fecha_fin >= fecha_inicio),
  INDEX idx_periodo_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- C) Tabla bloques_horarios (Morning / Noon / Afternoon / Night)
--    Definidos por el administrador; el estudiante solo los selecciona.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS bloques_horarios (
  id_bloque INT AUTO_INCREMENT PRIMARY KEY,
  nombre_bloque VARCHAR(30) NOT NULL UNIQUE,  -- 'Morning', 'Noon', 'Afternoon', 'Night'
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  descripcion VARCHAR(200) NULL,
  CONSTRAINT chk_bloque_horas CHECK (hora_fin > hora_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos semilla para bloques_horarios (idempotente)
INSERT INTO bloques_horarios (nombre_bloque, hora_inicio, hora_fin, descripcion) VALUES
('Morning',    '08:00:00', '11:00:00', 'Mañana: 8:00 AM – 11:00 AM'),
('Noon',       '11:00:00', '14:00:00', 'Mediodía: 11:00 AM – 2:00 PM'),
('Afternoon',  '14:00:00', '18:00:00', 'Tarde: 2:00 PM – 6:00 PM'),
('Night',      '18:00:00', '21:00:00', 'Noche: 6:00 PM – 9:00 PM')
ON DUPLICATE KEY UPDATE nombre_bloque = VALUES(nombre_bloque);

-- ---------------------------------------------------------
-- D) Asociación de la tutoría con un bloque horario
--    Se conserva hora_inicio/hora_fin para retrocompatibilidad; el flujo nuevo
--    los calcula automáticamente desde el bloque seleccionado.
--    El ADD COLUMN/INDEX se hace condicional para poder re-ejecutar la migración.
-- ---------------------------------------------------------
SET @col_bloque_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutorias'
    AND COLUMN_NAME = 'id_bloque'
);
SET @sql_add_bloque = IF(@col_bloque_existe = 0,
  'ALTER TABLE tutorias ADD COLUMN id_bloque INT NULL AFTER id_materia, ADD INDEX idx_tutoria_bloque (id_bloque), ADD CONSTRAINT fk_tutorias_bloque FOREIGN KEY (id_bloque) REFERENCES bloques_horarios(id_bloque) ON UPDATE CASCADE',
  'SELECT "La columna tutorias.id_bloque ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_add_bloque FROM @sql_add_bloque;
EXECUTE stmt_add_bloque;
DEALLOCATE PREPARE stmt_add_bloque;

-- ---------------------------------------------------------
-- E) Seeds iniciales para periodos_tutoria (ejemplo de periodos)
-- ---------------------------------------------------------
INSERT INTO periodos_tutoria (codigo, nombre, fecha_inicio, fecha_fin, activo) VALUES
('I-2026', '2026-1 Primer Semestre', '2026-03-01', '2026-07-31', 1),
('II-2026', '2026-2 Segundo Semestre', '2026-08-01', '2026-12-31', 1)
ON DUPLICATE KEY UPDATE fecha_inicio = VALUES(fecha_inicio), fecha_fin = VALUES(fecha_fin);
