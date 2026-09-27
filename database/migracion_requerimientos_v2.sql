-- =========================================================
-- MIGRACIÓN REQUERIMIENTOS V2 - SISTEMA DE TUTORÍAS UPDS
-- 1. Rol 'auxiliar' + bitácora de auditoría de usuarios
-- 2. Acceso condicional a Modalidad de Grado (MG)
--    (comprobantes_pago_mg + desbloqueo acceso_mg_desbloqueado)
-- 3. Filtro estricto de tutores por carrera (docente_carreras)
-- 4. Gestión de cupos por tutor
--    (config cupo_minimo_apoyo por tutoría, cupo_maximo_mg = 5)
-- 5. Reuniones/informes dinámicos + tardanzas/evidencias
-- 6. Estados de cierre de expediente de MG (3FN)
--    (estados_conclusion_mg + tutorias.id_estado_conclusion)
-- Mantiene 3FN. Idempotente (se puede ejecutar más de una vez).
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 0. Estados de cierre del expediente de Modalidad de Grado
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS estados_conclusion_mg (
  id_estado_conclusion INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO estados_conclusion_mg (id_estado_conclusion, nombre, descripcion) VALUES
(1, 'Aprobada', 'El estudiante aprobó la defensa y concluyó la Modalidad de Grado.'),
(2, 'Reprobada', 'El estudiante no aprobó la defensa de la Modalidad de Grado.'),
(3, 'Abandono', 'El estudiante abandonó la Modalidad de Grado sin concluir.'),
(4, 'Otros', 'Otro resultado de cierre del expediente de grado.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- ---------------------------------------------------------
-- 1. Rol auxiliar (apoyo a la administración)
-- ---------------------------------------------------------
INSERT INTO roles (id_rol, nombre_rol) VALUES
(4, 'auxiliar')
ON DUPLICATE KEY UPDATE nombre_rol = VALUES(nombre_rol);

-- ---------------------------------------------------------
-- 2. Configuración del sistema (parámetros de negocio)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS configuracion_sistema (
  clave VARCHAR(50) PRIMARY KEY,
  valor VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO configuracion_sistema (clave, valor, descripcion) VALUES
('cupo_minimo_apoyo', '3', 'Número mínimo de estudiantes por tutoría de apoyo para habilitarla.'),
('cupo_maximo_mg', '5', 'Límite de estudiantes por tutor en Modalidad de Grado.'),
('materias_requeridas_mg', '54', 'Materias culminadas requeridas para acceder a Modalidad de Grado.'),
('semestres_requeridos_mg', '9', 'Semestres requeridos para acceder a Modalidad de Grado.')
ON DUPLICATE KEY UPDATE
  valor = VALUES(valor),
  descripcion = VALUES(descripcion);

-- ---------------------------------------------------------
-- 3. Tutorías: distingue Apoyo vs Modalidad de Grado
-- ---------------------------------------------------------
DROP PROCEDURE IF EXISTS migrar_requerimientos_v2;
DELIMITER //
CREATE PROCEDURE migrar_requerimientos_v2()
BEGIN
  -- 3.1 tutorias.tipo
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'tutorias'
                   AND COLUMN_NAME = 'tipo') THEN
    ALTER TABLE tutorias
      ADD COLUMN tipo ENUM('apoyo','grado') NOT NULL DEFAULT 'apoyo' AFTER id_modalidad;
  END IF;

  -- 3.1.b tutorias.estado (agrega 'confirmada' usado por el flujo de Apoyo)
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'tutorias'
                   AND COLUMN_NAME = 'estado'
                   AND COLUMN_TYPE LIKE '%confirmada%') THEN
    ALTER TABLE tutorias
      MODIFY COLUMN estado ENUM('pendiente','confirmada','asignada','aceptada','en_proceso','en_reasignacion','realizada','cancelada','finalizada') NOT NULL DEFAULT 'pendiente';
  END IF;

  -- 3.2 Estudiantes: datos de MG
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'estudiantes'
                   AND COLUMN_NAME = 'materias_completadas') THEN
    ALTER TABLE estudiantes
      ADD COLUMN materias_completadas INT NOT NULL DEFAULT 0 AFTER semestre;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'estudiantes'
                   AND COLUMN_NAME = 'acceso_mg_desbloqueado') THEN
    ALTER TABLE estudiantes
      ADD COLUMN acceso_mg_desbloqueado TINYINT(1) NOT NULL DEFAULT 0 AFTER materias_completadas;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'estudiantes'
                   AND COLUMN_NAME = 'mg_desbloqueado_por') THEN
    ALTER TABLE estudiantes
      ADD COLUMN mg_desbloqueado_por INT NULL AFTER acceso_mg_desbloqueado;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'estudiantes'
                   AND COLUMN_NAME = 'mg_desbloqueado_fecha') THEN
    ALTER TABLE estudiantes
      ADD COLUMN mg_desbloqueado_fecha DATETIME NULL AFTER mg_desbloqueado_por;
  END IF;

  -- 3.3 tutorias.id_estado_conclusion (resultado de cierre de MG, 3FN)
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'tutorias'
                   AND COLUMN_NAME = 'id_estado_conclusion') THEN
    ALTER TABLE tutorias
      ADD COLUMN id_estado_conclusion INT NULL AFTER id_modalidad;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS rc
                 INNER JOIN information_schema.TABLE_CONSTRAINTS tc
                    ON rc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
                   AND rc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
                 WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
                   AND tc.TABLE_NAME = 'tutorias'
                   AND rc.CONSTRAINT_NAME = 'fk_tutorias_estado_conclusion') THEN
    ALTER TABLE tutorias
      ADD CONSTRAINT fk_tutorias_estado_conclusion FOREIGN KEY (id_estado_conclusion)
      REFERENCES estados_conclusion_mg(id_estado_conclusion) ON UPDATE CASCADE;
  END IF;
END//
DELIMITER ;
CALL migrar_requerimientos_v2();
DROP PROCEDURE IF EXISTS migrar_requerimientos_v2;

-- ---------------------------------------------------------
-- 4. Estudiante en tutoría (permite varios estudiantes en
--    una tutoría de apoyo: mínimo de estudiantes para habilitar)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutoria_estudiantes (
  id_tutoria INT NOT NULL,
  id_estudiante INT NOT NULL,
  fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tutoria, id_estudiante),
  CONSTRAINT fk_te_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_te_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 5. Comprobantes de pago para Modalidad de Grado
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS comprobantes_pago_mg (
  id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  fecha_pago DATE NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  estado ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  motivo_rechazo TEXT NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  validado_por INT NULL,
  fecha_validacion DATETIME NULL,
  CONSTRAINT fk_comprobante_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE,
  CONSTRAINT fk_comprobante_operador FOREIGN KEY (validado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_comprobante_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 6. Carreras de los docentes (filtro estricto por carrera)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS docente_carreras (
  id_docente_carrera INT AUTO_INCREMENT PRIMARY KEY,
  id_tutor INT NOT NULL,
  id_carrera INT NOT NULL,
  UNIQUE KEY uq_docente_carrera (id_tutor, id_carrera),
  CONSTRAINT fk_dc_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  CONSTRAINT fk_dc_carrera FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 7. Bitácora de auditoría de usuarios (admin / auxiliar)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS bitacora_auditoria_usuarios (
  id_bitacora INT AUTO_INCREMENT PRIMARY KEY,
  id_operador INT NOT NULL,
  id_afectado INT NOT NULL,
  accion ENUM('crear','editar','eliminar') NOT NULL,
  detalles TEXT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45),
  CONSTRAINT fk_bitacora_operador FOREIGN KEY (id_operador) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_bitacora_afectado FOREIGN KEY (id_afectado) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  INDEX idx_bitacora_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- Datos de referencia
-- =========================================================

-- Usuario auxiliar de prueba (auxiliar / password)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) VALUES
(14, 4, 'Auxiliar', 'Coordinación', 'auxiliar@tutorias.local', 'auxiliar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '70000014', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario), id_rol = VALUES(id_rol);

-- Carreras de los docentes de prueba (todos en Ingeniería de Sistemas)
INSERT INTO docente_carreras (id_tutor, id_carrera) VALUES
(1, 1), (7, 1), (8, 1)
ON DUPLICATE KEY UPDATE id_carrera = VALUES(id_carrera);

-- Inscripciones iniciales de estudiantes en las tutorías de prueba
INSERT INTO tutoria_estudiantes (id_tutoria, id_estudiante) VALUES
(101, 1), (102, 2), (103, 1)
ON DUPLICATE KEY UPDATE id_estudiante = VALUES(id_estudiante);

-- Actualiza materias completadas del estudiante de prueba para poder
-- demostrar el flujo de Modalidad de Grado (54 materias / 9 semestres culminados).
UPDATE estudiantes SET materias_completadas = 54
WHERE id_estudiante = 1 AND materias_completadas = 0;

UPDATE estudiantes SET semestre = 9
WHERE id_estudiante = 1 AND semestre < 9;