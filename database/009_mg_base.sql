-- =========================================================
-- MIGRACIÓN 009 - MÓDULO MODALIDADES DE GRADO (MVP) - SPRINT 7
-- Crea las tablas:
--   1. parametros_mg        (HU-020: parámetros de negocio del módulo MG)
--   2. modalidades_grado    (HU-022: 5 modalidades oficiales, RN-MG-01)
--   3. cohortes_mg          (HU-022: cohortes/periodos de gestión)
--   4. expedientes_mg       (HU-024: expedientes de modalidad de grado)
--   5. asignaciones_tutor   (HU-025: asignación e historial de tutores)
-- Incluye la semilla inicial de parámetros, modalidades y cohortes.
-- Idempotente (se puede ejecutar más de una vez).
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. parametros_mg (parámetros de negocio del módulo MG)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS parametros_mg (
  clave VARCHAR(50) PRIMARY KEY,
  valor VARCHAR(255) NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  tipo ENUM('entero','decimal','texto','booleano') NOT NULL DEFAULT 'texto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO parametros_mg (clave, valor, descripcion, tipo) VALUES
  ('reuniones_min_semana_perfil', '1', 'Minimo de reuniones semanales del perfil de proyecto de grado.', 'entero'),
  ('reuniones_min_semana_tesis', '2', 'Minimo de reuniones semanales para tesis.', 'entero'),
  ('reuniones_min_semana_trabajo_dirigido', '1', 'Minimo de reuniones semanales para trabajo dirigido.', 'entero'),
  ('tutor_carga_recomendada', '8', 'Cantidad recomendada de expedientes activos por tutor.', 'entero'),
  ('tutor_carga_maxima', '10', 'Cantidad maxima de expedientes activos por tutor.', 'entero'),
  ('dias_limite_confirmacion_tutor', '5', 'Dias para que el estudiante confirme al tutor asignado.', 'entero'),
  ('requiere_acta_aceptacion_tutor', '1', 'Indica si se genera acta de aceptacion al asignar tutor.', 'booleano')
ON DUPLICATE KEY UPDATE
  valor = VALUES(valor),
  descripcion = VALUES(descripcion),
  tipo = VALUES(tipo);

-- ---------------------------------------------------------
-- 2. modalidades_grado (catálogo oficial de modalidades, RN-MG-01)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS modalidades_grado (
  id_modalidad_grado INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  requiere_tutor TINYINT(1) NOT NULL DEFAULT 1,
  flujo VARCHAR(30) NOT NULL DEFAULT 'perfil_mg',
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO modalidades_grado (nombre, requiere_tutor, flujo) VALUES
  ('Graduacion por Excelencia', 0, 'excelencia'),
  ('Proyecto de Grado', 1, 'perfil_mg'),
  ('Tesis', 1, 'perfil_mg'),
  ('Examen de Grado', 0, 'examen_areas'),
  ('Trabajo Dirigido', 1, 'perfil_mg')
ON DUPLICATE KEY UPDATE
  requiere_tutor = VALUES(requiere_tutor),
  flujo = VALUES(flujo),
  activa = 1;

-- ---------------------------------------------------------
-- 3. cohortes_mg (cohortes/gestiones de modalidad de grado)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cohortes_mg (
  id_cohorte_mg INT AUTO_INCREMENT PRIMARY KEY,
  nombre_periodo VARCHAR(20) NOT NULL UNIQUE,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'planificada',
  observaciones VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO cohortes_mg (nombre_periodo, fecha_inicio, fecha_fin, estado, observaciones) VALUES
  ('2025-II', '2025-08-04', '2026-02-27', 'cerrada', 'Cohorte gestion II - 2025.'),
  ('2026-I', '2026-03-02', '2026-08-28', 'abierta', 'Cohorte en curso gestion I - 2026.'),
  ('2027-I', '2027-03-01', NULL, 'planificada', 'Cohorte planificada gestion I - 2027.')
ON DUPLICATE KEY UPDATE
  estado = IF(estado = 'cerrada', estado, VALUES(estado));

-- ---------------------------------------------------------
-- 4. expedientes_mg (expedientes de modalidad de grado)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS expedientes_mg (
  id_expediente_mg INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT NOT NULL,
  id_modalidad_grado INT NOT NULL,
  id_cohorte_mg INT NOT NULL,
  estado VARCHAR(30) NOT NULL DEFAULT 'solicitado',
  fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  solicitud_detalle VARCHAR(255),
  resolucion_admin VARCHAR(255),
  validado_por INT NULL,
  fecha_validacion DATETIME NULL,
  CONSTRAINT fk_exp_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON UPDATE CASCADE,
  CONSTRAINT fk_exp_modalidad FOREIGN KEY (id_modalidad_grado) REFERENCES modalidades_grado(id_modalidad_grado) ON UPDATE CASCADE,
  CONSTRAINT fk_exp_cohorte FOREIGN KEY (id_cohorte_mg) REFERENCES cohortes_mg(id_cohorte_mg) ON UPDATE CASCADE,
  CONSTRAINT fk_exp_validado FOREIGN KEY (validado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_exp_estudiante (id_estudiante),
  INDEX idx_exp_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 5. asignaciones_tutor (asignación e historial de tutores MG)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS asignaciones_tutor (
  id_asignacion_tutor INT AUTO_INCREMENT PRIMARY KEY,
  id_expediente_mg INT NOT NULL,
  id_tutor INT NOT NULL,
  fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  asignado_por INT NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'activa',
  observaciones VARCHAR(255),
  CONSTRAINT fk_ast_expediente FOREIGN KEY (id_expediente_mg) REFERENCES expedientes_mg(id_expediente_mg) ON DELETE CASCADE,
  CONSTRAINT fk_ast_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON UPDATE CASCADE,
  CONSTRAINT fk_ast_asignado FOREIGN KEY (asignado_por) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
  INDEX idx_ast_expediente (id_expediente_mg),
  INDEX idx_ast_tutor_estado (id_tutor, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;