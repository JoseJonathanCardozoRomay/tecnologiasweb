-- =========================================================
-- MIGRACIÓN 008: Campos adicionales para perfil profesional del tutor
-- Sistema de Tutorías UPDS
-- Ejecutar: docker exec -i tutorias_db mysql -u tutores_user -p'12345' tutorias_db < database/migrations/008_perfil_tutor_completo.sql
-- =========================================================

-- Ampliar foto_perfil para soportar rutas de archivos (NO data URIs) - idempotente
SET @col_foto_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'foto_perfil'
);
SET @sql_foto = IF(@col_foto_existe = 0,
  'ALTER TABLE tutores ADD COLUMN foto_perfil VARCHAR(500) NULL DEFAULT NULL',
  'ALTER TABLE tutores MODIFY COLUMN foto_perfil VARCHAR(500) NULL DEFAULT NULL');
PREPARE stmt_foto FROM @sql_foto;
EXECUTE stmt_foto;
DEALLOCATE PREPARE stmt_foto;

-- Agregar campos de perfil profesional - idempotente
SET @col_linkedin_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'perfil_linkedin'
);
SET @sql_linkedin = IF(@col_linkedin_existe = 0,
  'ALTER TABLE tutores ADD COLUMN perfil_linkedin VARCHAR(255) NULL DEFAULT NULL AFTER foto_perfil',
  'SELECT "Columna perfil_linkedin ya existe" AS mensaje');
PREPARE stmt_linkedin FROM @sql_linkedin;
EXECUTE stmt_linkedin;
DEALLOCATE PREPARE stmt_linkedin;

SET @col_cert_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'certificaciones'
);
SET @sql_cert = IF(@col_cert_existe = 0,
  'ALTER TABLE tutores ADD COLUMN certificaciones TEXT NULL AFTER perfil_linkedin',
  'SELECT "Columna certificaciones ya existe" AS mensaje');
PREPARE stmt_cert FROM @sql_cert;
EXECUTE stmt_cert;
DEALLOCATE PREPARE stmt_cert;

SET @col_areas_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND COLUMN_NAME = 'areas_expertise'
);
SET @sql_areas = IF(@col_areas_existe = 0,
  'ALTER TABLE tutores ADD COLUMN areas_expertise VARCHAR(500) NULL AFTER certificaciones',
  'SELECT "Columna areas_expertise ya existe" AS mensaje');
PREPARE stmt_areas FROM @sql_areas;
EXECUTE stmt_areas;
DEALLOCATE PREPARE stmt_areas;

-- Índice para búsqueda por área de expertise - idempotente
SET @idx_existe = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutores'
    AND INDEX_NAME = 'idx_tutores_expertise'
);
SET @sql_idx = IF(@idx_existe = 0,
  'CREATE INDEX idx_tutores_expertise ON tutores(areas_expertise(100))',
  'SELECT "Índice idx_tutores_expertise ya existe" AS mensaje');
PREPARE stmt_idx FROM @sql_idx;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;