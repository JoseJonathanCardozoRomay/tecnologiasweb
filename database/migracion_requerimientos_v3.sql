-- =========================================================
-- MIGRACIÓN REQUERIMIENTOS V3 - SISTEMA DE TUTORÍAS UPDS
-- 1. Motor de notificaciones en base de datos
--    (notificaciones: campana del header, tiempo real vía polling)
-- 2. Intercambio de documentos en el expediente de la tutoría
--    (documentos_expediente: .doc / .docx / .pdf)
-- Idempotente (se puede ejecutar más de una vez).
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. Notificaciones (destinatario, origen, tipo, mensaje)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificaciones (
  id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
  id_destinatario INT NOT NULL,
  id_origen INT NULL,
  tipo VARCHAR(60) NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  enlace VARCHAR(255) NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_destinatario FOREIGN KEY (id_destinatario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_notif_origen FOREIGN KEY (id_origen) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_notif_destinatario (id_destinatario, leida),
  INDEX idx_notif_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 2. Documentos enviados en el expediente de la tutoría
--    (admin / auxiliar -> estudiante o tutor)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS documentos_expediente (
  id_documento INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  id_origen INT NOT NULL,
  id_destinatario INT NOT NULL,
  nombre_original VARCHAR(255) NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  descripcion VARCHAR(500) NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_doc_exp_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_doc_exp_origen FOREIGN KEY (id_origen) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_doc_exp_destinatario FOREIGN KEY (id_destinatario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  INDEX idx_doc_exp_tutoria (id_tutoria),
  INDEX idx_doc_exp_destinatario (id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;