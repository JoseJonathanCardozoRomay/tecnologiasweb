-- =========================================================
-- MIGRACIÓN 006: Perfil profesional del tutor y bloques horarios predefinidos
-- Sistema de Tutorías UPDS
-- ---------------------------------------------------------
-- Aplica sobre una base ya existente. No volver a ejecutar init.sql.
-- No incluye USE: la base se selecciona al invocar el cliente
-- (mysql -uroot -p... tutorias_db < archivo) o la conexión PDO.
--
-- Objetivo: 
-- 1. Enriquecer el perfil profesional del tutor con campos adicionales
-- 2. Crear tabla intermedia para asociar tutores con bloques horarios predefinidos
-- 3. El tutor solo selecciona bloques definidos por coordinación, no los crea
-- =========================================================

-- ---------------------------------------------------------
-- A) Campos adicionales para el perfil profesional del tutor
--    Se agregan de forma condicional para permitir re-ejecución
-- ---------------------------------------------------------

SET @col_foto_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'foto_perfil'
);
SET @sql_add_foto = IF(@col_foto_existe = 0,
  'ALTER TABLE tutores ADD COLUMN foto_perfil VARCHAR(255) NULL AFTER biografia',
  'SELECT "La columna tutores.foto_perfil ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_add_foto FROM @sql_add_foto;
EXECUTE stmt_add_foto;
DEALLOCATE PREPARE stmt_add_foto;

SET @col_linkedin_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'perfil_linkedin'
);
SET @sql_add_linkedin = IF(@col_linkedin_existe = 0,
  'ALTER TABLE tutores ADD COLUMN perfil_linkedin VARCHAR(255) NULL AFTER foto_perfil',
  'SELECT "La columna tutores.perfil_linkedin ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_add_linkedin FROM @sql_add_linkedin;
EXECUTE stmt_add_linkedin;
DEALLOCATE PREPARE stmt_add_linkedin;

SET @col_certificaciones_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'certificaciones'
);
SET @sql_add_certificaciones = IF(@col_certificaciones_existe = 0,
  'ALTER TABLE tutores ADD COLUMN certificaciones TEXT NULL AFTER perfil_linkedin',
  'SELECT "La columna tutores.certificaciones ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_add_certificaciones FROM @sql_add_certificaciones;
EXECUTE stmt_add_certificaciones;
DEALLOCATE PREPARE stmt_add_certificaciones;

SET @col_areas_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'areas_expertise'
);
SET @sql_add_areas = IF(@col_areas_existe = 0,
  'ALTER TABLE tutores ADD COLUMN areas_expertise VARCHAR(500) NULL AFTER certificaciones',
  'SELECT "La columna tutores.areas_expertise ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_add_areas FROM @sql_add_areas;
EXECUTE stmt_add_areas;
DEALLOCATE PREPARE stmt_add_areas;

-- ---------------------------------------------------------
-- B) Tabla intermedia tutor_bloque_seleccionado
--    Asocia tutores con bloques horarios predefinidos por coordinación
--    El tutor selecciona qué bloques le corresponden como disponibles
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutor_bloque_seleccionado (
  id_tutor INT NOT NULL,
  id_bloque INT NOT NULL,
  fecha_seleccion DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tutor, id_bloque),
  CONSTRAINT fk_tutor_bloque_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  CONSTRAINT fk_tutor_bloque_bloque FOREIGN KEY (id_bloque) REFERENCES bloques_horarios(id_bloque) ON DELETE CASCADE,
  INDEX idx_tutor_bloque_tutor (id_tutor),
  INDEX idx_tutor_bloque_bloque (id_bloque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- C) Vista para obtener bloques seleccionados por un tutor con sus detalles
--    Facilita las consultas en los controladores y modelos
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vista_tutor_bloques AS
SELECT 
  tbs.id_tutor,
  tbs.id_bloque,
  bh.nombre_bloque,
  bh.hora_inicio,
  bh.hora_fin,
  bh.descripcion,
  tbs.fecha_seleccion
FROM tutor_bloque_seleccionado tbs
INNER JOIN bloques_horarios bh ON tbs.id_bloque = bh.id_bloque
ORDER BY bh.hora_inicio ASC;

-- ---------------------------------------------------------
-- Notas de uso:
-- 
-- 1. El tutor ya NO crea horarios personalizados (disponibilidad_tutor)
--    en su panel. Solo selecciona de los bloques predefinidos por coordinación.
-- 
-- 2. Para marcar un bloque como disponible para un tutor:
--    INSERT INTO tutor_bloque_seleccionado (id_tutor, id_bloque) VALUES (1, 2);
-- 
-- 3. Para desmarcar un bloque:
--    DELETE FROM tutor_bloque_seleccionado WHERE id_tutor = 1 AND id_bloque = 2;
-- 
-- 4. Para obtener los bloques seleccionados por un tutor:
--    SELECT * FROM vista_tutor_bloques WHERE id_tutor = 1;
-- 
-- 5. Los campos del perfil profesional son opcionales (NULL por defecto)
--    y pueden ser actualizados directamente por el tutor en su panel.
-- =========================================================
