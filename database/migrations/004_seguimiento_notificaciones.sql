-- =========================================================
-- MIGRACIÓN 004: Seguimiento de sesión y notificaciones
-- Sistema de Tutorías UPDS
-- ---------------------------------------------------------
-- Aplica sobre una base ya existente. No volver a ejecutar init.sql.
-- No incluye USE: la base se selecciona al invocar el cliente
-- (mysql -uroot -p... tutorias_db < archivo) o la conexión PDO.
-- =========================================================

-- ---------------------------------------------------------
-- Motivo de cancelación de una tutoría (idempotente)
-- MySQL 8 no soporta ADD COLUMN IF NOT EXISTS en versiones previas a 8.0.29,
-- por eso se consulta information_schema y se prepara el ALTER solo si falta.
-- ---------------------------------------------------------
SET @col_existe = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tutorias'
    AND COLUMN_NAME = 'motivo_cancelacion'
);
SET @sql_alter = IF(@col_existe = 0,
  'ALTER TABLE tutorias ADD COLUMN motivo_cancelacion VARCHAR(255) NULL AFTER observaciones',
  'SELECT "Columna tutorias.motivo_cancelacion ya existe, se omite el ALTER" AS aviso');
PREPARE stmt_alter FROM @sql_alter;
EXECUTE stmt_alter;
DEALLOCATE PREPARE stmt_alter;

-- ---------------------------------------------------------
-- Seguimiento de la sesión (1:1 con tutorias)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS seguimiento_sesion (
  id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL UNIQUE,
  asistio ENUM('si','no') NOT NULL,
  temas_tratados TEXT NULL,
  avance ENUM('sin_avance','parcial','logrado') NULL,
  recomendaciones TEXT NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_seguimiento_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Notificaciones internas por usuario
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificaciones (
  id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  tipo VARCHAR(30) NOT NULL,
  mensaje VARCHAR(255) NOT NULL,
  url VARCHAR(200) NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  INDEX idx_notif_usuario_leida (id_usuario, leida, fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
