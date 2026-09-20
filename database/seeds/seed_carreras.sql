-- ============================================
-- SEED: Carreras Fundamentales UPDS
-- Agrega carreras: Ingeniería Civil, Administración de Empresas, Medicina, Contabilidad, Psicología
-- ============================================

SET NAMES utf8mb4;

-- Insertar nuevas carreras
INSERT INTO carreras (id_carrera, nombre_carrera) VALUES
(4, 'Ingeniería Civil'),
(5, 'Administración de Empresas'),
(6, 'Medicina'),
(7, 'Contabilidad'),
(8, 'Psicología')
ON DUPLICATE KEY UPDATE nombre_carrera = VALUES(nombre_carrera);

-- Verificar después con:
-- SELECT id_carrera, nombre_carrera FROM carreras ORDER BY id_carrera;
