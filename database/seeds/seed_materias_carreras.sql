-- ============================================
-- SEED: Materias para Carreras Fundamentales
-- Agrega materias para Ingeniería Civil, Administración de Empresas, Medicina, Contabilidad, Psicología
-- ============================================

SET NAMES utf8mb4;

-- Materias para Ingeniería Civil (id_carrera 4)
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(10, 'Física Aplicada', 4),
(11, 'Mecánica de Suelos', 4),
(12, 'Hidráulica', 4),
(13, 'Estructuras', 4)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Materias para Administración de Empresas (id_carrera 5)
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(14, 'Contabilidad General', 5),
(15, 'Economía', 5),
(16, 'Marketing', 5),
(17, 'Gestión de Recursos Humanos', 5)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Materias para Medicina (id_carrera 6)
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(18, 'Anatomía', 6),
(19, 'Fisiología', 6),
(20, 'Bioquímica', 6),
(21, 'Farmacología', 6)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Materias para Contabilidad (id_carrera 7)
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(22, 'Contabilidad I', 7),
(23, 'Contabilidad II', 7),
(24, 'Auditoría', 7),
(25, 'Costos', 7)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Materias para Psicología (id_carrera 8)
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(26, 'Psicología General', 8),
(27, 'Psicología Social', 8),
(28, 'Psicología Clínica', 8),
(29, 'Psicología Organizacional', 8)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Verificar después con:
-- SELECT id_materia, nombre_materia, id_carrera FROM materias ORDER BY id_materia;
