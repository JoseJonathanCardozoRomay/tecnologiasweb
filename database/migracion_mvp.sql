-- =========================================================
-- MIGRACIÓN MVP - SISTEMA DE TUTORÍAS UPDS
-- Crea tablas: modalidades_graduacion, cartas_designacion,
--             reuniones, informes_avance, tribunales,
--             historial_auditoria
-- Modifica 'tutorias': agrega id_modalidad y amplía estado.
-- Idempotente (se puede ejecutar más de una vez).
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. Modalidades de graduación (entes oficiales)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS modalidades_graduacion (
  id_modalidad INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  descripcion TEXT,
  minimo_reuniones_semana TINYINT NOT NULL DEFAULT 1,
  cantidad_informes TINYINT NOT NULL DEFAULT 3,
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 2. Cartas de designación
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cartas_designacion (
  id_carta INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  id_tutor INT NOT NULL,
  id_estudiante INT NOT NULL,
  fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_firma DATETIME NULL,
  estado ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  motivo_rechazo TEXT NULL,
  CONSTRAINT fk_carta_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_carta_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON UPDATE CASCADE,
  CONSTRAINT fk_carta_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 3. Reuniones
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS reuniones (
  id_reunion INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  lugar_o_enlace VARCHAR(200),
  asistio_estudiante ENUM('si','no','tardanza') NOT NULL DEFAULT 'si',
  evidencia_url VARCHAR(255),
  observaciones TEXT,
  CONSTRAINT fk_reunion_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 4. Informes de avance
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS informes_avance (
  id_informe INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  numero_informe TINYINT NOT NULL,
  fecha_limite DATE NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  porcentaje_avance TINYINT NOT NULL DEFAULT 0 CHECK (porcentaje_avance BETWEEN 0 AND 100),
  descripcion_avance TEXT,
  CONSTRAINT fk_informe_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  UNIQUE KEY uq_informe_tutoria (id_tutoria, numero_informe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 5. Tribunales (2 por tutoría; docentes, NO el tutor asignado)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tribunales (
  id_tribunal INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_tutoria INT NOT NULL,
  CONSTRAINT fk_tribunal_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_tribunal_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  UNIQUE KEY uq_tribunal_tutoria (id_usuario, id_tutoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 6. Historial y auditoría (eventos críticos)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS historial_auditoria (
  id_historial INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NULL,
  tipo_evento VARCHAR(60) NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45),
  CONSTRAINT fk_historial_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 7. Altera tabla tutorias
--    a) Agrega id_modalidad (si no existe)
--    b) Amplía el ENUM de estado
-- ---------------------------------------------------------
DROP PROCEDURE IF EXISTS migrar_mvp;
DELIMITER //
CREATE PROCEDURE migrar_mvp()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'tutorias'
                   AND COLUMN_NAME = 'id_modalidad') THEN
    ALTER TABLE tutorias ADD COLUMN id_modalidad INT NULL AFTER id_materia;
  END IF;

  ALTER TABLE tutorias
    MODIFY COLUMN estado ENUM(
      'pendiente','asignada','aceptada','en_proceso',
      'en_reasignacion','realizada','cancelada','finalizada'
    ) NOT NULL DEFAULT 'pendiente';

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'tutorias'
                   AND INDEX_NAME = 'fk_tutorias_modalidad') THEN
    ALTER TABLE tutorias
      ADD CONSTRAINT fk_tutorias_modalidad
      FOREIGN KEY (id_modalidad) REFERENCES modalidades_graduacion(id_modalidad) ON UPDATE CASCADE;
  END IF;
END//
DELIMITER ;
CALL migrar_mvp();
DROP PROCEDURE IF EXISTS migrar_mvp;

-- =========================================================
-- Datos de prueba
-- =========================================================

-- Modalidades oficiales
INSERT INTO modalidades_graduacion (id_modalidad, nombre, descripcion, minimo_reuniones_semana, cantidad_informes, activa) VALUES
(1, 'Proyecto', 'Trabajo práctico orientado a construir un producto software. Se exige 1 reunión semanal y 3 informes de avance.', 1, 3, 1),
(2, 'Tesis', 'Investigación formal con sustento teórico y metodológico. Se exige 2 reuniones semanales y 4 informes de avance.', 2, 4, 1),
(3, 'Trabajo Dirigido', 'Solución a un problema real de la institución o empresa. Se exige 1 reunión semanal y 4 informes de avance.', 1, 4, 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Usuarios docentes adicionales para tutores y tribunales
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(11, 2, 'Laura', 'Quispe', 'laura.quispe@upds.edu.bo', 'laura', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(12, 2, 'Rodrigo', 'Vargas', 'rodrigo.vargas@upds.edu.bo', 'rodrigo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(13, 3, 'Ana', 'Rios', 'ana.rios@upds.edu.bo', 'ana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE estado = VALUES(estado);

INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia) VALUES
(7, 11, 'Ingeniería de Software', 'Docente con experiencia en metodologías ágiles y gestión de proyectos.'),
(8, 12, 'Bases de Datos', 'Especialista en diseño y administración de bases de datos relacionales.')
ON DUPLICATE KEY UPDATE especialidad = VALUES(especialidad);

INSERT INTO tutor_materia (id_tutor, id_materia) VALUES
(7, 3), (8, 3), (7, 1), (8, 1)
ON DUPLICATE KEY UPDATE id_tutor = VALUES(id_tutor);

-- Estudiante adicional
INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, registro_universitario) VALUES
(2, 13, 1, 6, 'RU-2026-11337')
ON DUPLICATE KEY UPDATE semestre = VALUES(semestre);

-- Tutorías de prueba con los nuevos estados (asignadas / en proceso)
INSERT INTO tutorias (id_tutoria, id_estudiante, id_tutor, id_materia, id_modalidad, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado, observaciones, fecha_solicitud) VALUES
(101, 1, 1, 3, 1, '2026-10-05', '14:00:00', '16:00:00', 'presencial', 'Lab 3 - Bloque A', 'en_proceso', 'Tutoría MVP: Proyecto de Tecnología Web.', NOW()),
(102, 2, 7, 3, 2, '2026-10-06', '09:00:00', '11:00:00', 'virtual', 'https://meet.upds.edu.bo/tesis-ana', 'asignada', 'Tutoría MVP: Tesis de grado.', NOW()),
(103, 1, 8, 1, 3, '2026-10-07', '15:00:00', '17:00:00', 'presencial', 'Aula 12', 'asignada', 'Tutoría MVP: Trabajo dirigido.', NOW())
ON DUPLICATE KEY UPDATE estado = VALUES(estado);

-- Cartas de designación de las tutorías de prueba
INSERT INTO cartas_designacion (id_carta, id_tutoria, id_tutor, id_estudiante, fecha_generacion, fecha_firma, estado, motivo_rechazo) VALUES
(1, 101, 1, 1, NOW(), NOW(), 'aceptada', NULL),
(2, 102, 7, 2, NOW(), NULL, 'pendiente', NULL),
(3, 103, 8, 1, NOW(), NULL, 'pendiente', NULL)
ON DUPLICATE KEY UPDATE estado = VALUES(estado);

-- Reuniones de prueba
INSERT INTO reuniones (id_reunion, id_tutoria, fecha, hora_inicio, hora_fin, lugar_o_enlace, asistio_estudiante, observaciones) VALUES
(1, 101, '2026-10-05', '14:00:00', '15:00:00', 'Lab 3', 'si', 'Definición del alcance del proyecto.'),
(2, 101, '2026-10-12', '14:00:00', '15:00:00', 'Lab 3', 'tardanza', 'Revisión del cronograma.')
ON DUPLICATE KEY UPDATE observaciones = VALUES(observaciones);

-- Informes de avance de prueba
INSERT INTO informes_avance (id_informe, id_tutoria, numero_informe, fecha_limite, fecha_registro, porcentaje_avance, descripcion_avance) VALUES
(1, 101, 1, '2026-10-20', NOW(), 25, 'Requisitos y diseño preliminar aprobados.'),
(2, 101, 2, '2026-11-10', NULL, 50, 'Avance en implementación del módulo de login.')
ON DUPLICATE KEY UPDATE descripcion_avance = VALUES(descripcion_avance);

-- Tribunales de prueba (docentes distintos al tutor de la tutoría 101)
INSERT INTO tribunales (id_tribunal, id_usuario, id_tutoria) VALUES
(1, 11, 101),
(2, 12, 101)
ON DUPLICATE KEY UPDATE id_tutoria = VALUES(id_tutoria);

-- Historial de auditoría de prueba
INSERT INTO historial_auditoria (id_historial, id_usuario, tipo_evento, descripcion, fecha_hora, ip_origen) VALUES
(1, 1, 'LOGIN', 'Inicio de sesión de admin.', NOW() - INTERVAL 2 DAY, '127.0.0.1'),
(2, 2, 'LOGOUT', 'Cierre de sesión de Carlos Docente.', NOW() - INTERVAL 1 DAY, '127.0.0.1'),
(3, 1, 'TUTORIA_CANCELADA', 'Cancelación de tutoría #101 (motivo: cambio de fecha).', NOW() - INTERVAL 1 HOUR, '127.0.0.1'),
(4, 2, 'CARTA_RECHAZADA', 'Carta de designación rechazada: no dispongo de tiempo.', NOW() - INTERVAL 30 MINUTE, '127.0.0.1'),
(5, 1, 'TRIBUNAL_ASIGNADO', 'Asignación de tribunales a tutoría #101.', NOW() - INTERVAL 10 MINUTE, '127.0.0.1'),
(6, 2, 'REUNION_REGISTRADA', 'Registro de reunión en tutoría #101.', NOW(), '127.0.0.1')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);