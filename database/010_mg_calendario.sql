-- =========================================================
-- MIGRACIÓN 010 - MÓDULO MODALIDADES DE GRADO (MVP) - SPRINT 7 (addenda)
-- Agrega:
--   1. Parametro dias_anticipacion_tribunales (HU-020: confirmacion del
--      tribunal evaluador con antelacion).
--   2. Tabla calendario_mg (HU-022: hitos/calendario por cohorte).
-- Incluye la semilla de los hitos por defecto para las cohortes existentes.
-- Idempotente (se puede ejecutar mas de una vez).
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. Nuevo parametro: dias de anticipacion para tribunales
-- ---------------------------------------------------------
INSERT INTO parametros_mg (clave, valor, descripcion, tipo) VALUES
  ('dias_anticipacion_tribunales', '7', 'Dias minimos de anticipacion para conformar el tribunal evaluador.', 'entero')
ON DUPLICATE KEY UPDATE
  valor = VALUES(valor),
  descripcion = VALUES(descripcion),
  tipo = VALUES(tipo);

-- ---------------------------------------------------------
-- 2. calendario_mg (hitos del calendario por cohorte)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS calendario_mg (
  id_hito INT AUTO_INCREMENT PRIMARY KEY,
  id_cohorte_mg INT NOT NULL,
  titulo VARCHAR(120) NOT NULL,
  descripcion VARCHAR(255),
  tipo_hito VARCHAR(30) NOT NULL DEFAULT 'gestion',
  fecha_hito DATE NOT NULL,
  orden INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_cal_cohorte FOREIGN KEY (id_cohorte_mg) REFERENCES cohortes_mg(id_cohorte_mg) ON DELETE CASCADE,
  INDEX idx_cal_cohorte (id_cohorte_mg),
  INDEX idx_cal_tipo (tipo_hito)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 3. Semilla de hitos por defecto para las cohortes existentes
--    (idempotente: no duplica si el hito ya existe por cohorte+titulo)
-- ---------------------------------------------------------
INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Inscripcion de solicitudes', 'Apertura del periodo de inscripciones.', 'inscripcion', '2026-03-02', 1
FROM cohortes_mg c
WHERE c.nombre_periodo = '2026-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Inscripcion de solicitudes');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Asignacion de tutores', 'Cierre del plazo para asignar tutores afines.', 'tutor', '2026-04-06', 2
FROM cohortes_mg c
WHERE c.nombre_periodo = '2026-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Asignacion de tutores');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Entrega de perfil de proyecto', 'Entrega del perfil para proyectos de grado y tesis.', 'perfil', '2026-05-25', 3
FROM cohortes_mg c
WHERE c.nombre_periodo = '2026-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Entrega de perfil de proyecto');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Defensas', 'Periodo de defensas ante tribunal.', 'defensa', '2026-08-10', 4
FROM cohortes_mg c
WHERE c.nombre_periodo = '2026-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Defensas');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Cierre de cohorte', 'Fin oficial de la gestion.', 'conclusion', '2026-08-28', 5
FROM cohortes_mg c
WHERE c.nombre_periodo = '2026-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Cierre de cohorte');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Inscripcion de solicitudes', 'Apertura del periodo de inscripciones.', 'inscripcion', '2025-08-04', 1
FROM cohortes_mg c
WHERE c.nombre_periodo = '2025-II'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Inscripcion de solicitudes');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Cierre de cohorte', 'Fin oficial de la gestion.', 'conclusion', '2026-02-27', 5
FROM cohortes_mg c
WHERE c.nombre_periodo = '2025-II'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Cierre de cohorte');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Inscripcion de solicitudes', 'Apertura del periodo de inscripciones (prevision).', 'inscripcion', '2027-03-01', 1
FROM cohortes_mg c
WHERE c.nombre_periodo = '2027-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Inscripcion de solicitudes');

INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
SELECT c.id_cohorte_mg, 'Asignacion de tutores', 'Cierre del plazo para asignar tutores afines.', 'tutor', '2027-04-05', 2
FROM cohortes_mg c
WHERE c.nombre_periodo = '2027-I'
  AND NOT EXISTS (SELECT 1 FROM calendario_mg cm WHERE cm.id_cohorte_mg = c.id_cohorte_mg AND cm.titulo = 'Asignacion de tutores');