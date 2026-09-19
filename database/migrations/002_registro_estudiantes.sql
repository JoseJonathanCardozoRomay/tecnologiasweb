-- Migración para bases de datos existentes. init.sql solo se ejecuta con un volumen MySQL vacío.
ALTER TABLE usuarios
    MODIFY estado ENUM('activo', 'inactivo', 'pendiente') NOT NULL DEFAULT 'activo';

CREATE TABLE IF NOT EXISTS registro_intentos (
  id_registro INT AUTO_INCREMENT PRIMARY KEY,
  ip_origen VARCHAR(45) NOT NULL,
  fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_registro_intentos_ip_fecha (ip_origen, fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
