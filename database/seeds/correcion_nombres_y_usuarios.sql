-- ============================================
-- CORRECCIÓN Y ESTANDARIZACIÓN DE NOMBRES
-- Generar nombres en español claro, sin inglés
-- ============================================

SET NAMES utf8mb4;

-- Primero: intentar corrección directa de los nombres existentes
UPDATE usuarios SET nombre = 'Valentina', apellido = 'Díaz' WHERE id_usuario = 17;
UPDATE usuarios SET nombre = 'Sofía', apellido = 'González' WHERE id_usuario = 15;
UPDATE usuarios SET nombre = 'Luis', apellido = 'Fernández' WHERE id_usuario = 14;
UPDATE usuarios SET nombre = 'Pedro', apellido = 'Rodríguez' WHERE id_usuario = 13;
UPDATE usuarios SET nombre = 'Ana', apellido = 'Martínez' WHERE id_usuario = 12;
UPDATE usuarios SET nombre = 'Juan', apellido = 'Pérez' WHERE id_usuario = 11;
UPDATE usuarios SET nombre = 'María', apellido = 'López' WHERE id_usuario = 10;
UPDATE usuarios SET nombre = 'Carlos', apellido = 'García' WHERE id_usuario = 9;
UPDATE usuarios SET nombre = 'Diego', apellido = 'Moreno' WHERE id_usuario = 18;

-- Verificar después con:
-- SELECT id_usuario, nombre, apellido FROM usuarios ORDER BY id_usuario;
-- Si los nombres siguen sin corregirse, usar la siguiente sección

-- ============================================
-- RE-INSERCIÓN COMPLETA (solo si el UPDATE no funcionó)
-- ⚠️ El usuario debe verificar las relaciones antes de ejecutar
-- ============================================

-- 1. Verificar relaciones existentes:
-- SELECT * FROM tutor_materia WHERE id_tutor IN (SELECT id_tutor FROM tutores WHERE id_usuario IN (9,10,11,12,13,14,15,17,18));
-- SELECT * FROM tutores WHERE id_usuario IN (9,10,11,12,13,14,15,17,18);
-- SELECT * FROM estudiantes WHERE id_usuario IN (10,14,15,17,18);

-- 2. Si es necesario, eliminar registros existentes:
-- DELETE FROM tutor_materia WHERE id_tutor IN (SELECT id_tutor FROM tutores WHERE id_usuario IN (9,10,11,12,13));
-- DELETE FROM tutores WHERE id_usuario IN (9,10,11,12,13);
-- DELETE FROM estudiantes WHERE id_usuario IN (10,14,15,17,18);
-- DELETE FROM usuarios WHERE id_usuario IN (9,10,11,12,13,14,15,17,18);

-- 3. Re-inserción con nombres en español claro:

-- USUARIOS
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(9, 2, 'Carlos', 'García', 'carlos.garcia@upds.edu.bo', 'tutor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(10, 3, 'María', 'López', 'maria.lopez@upds.edu.bo', 'estudiante1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(11, 2, 'Juan', 'Pérez', 'juan.perez@upds.edu.bo', 'tutor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(12, 2, 'Ana', 'Martínez', 'ana.martinez@upds.edu.bo', 'tutor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(13, 2, 'Pedro', 'Rodríguez', 'pedro.rodriguez@upds.edu.bo', 'tutor4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(14, 3, 'Luis', 'Fernández', 'luis.fernandez@upds.edu.bo', 'estudiante2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(15, 3, 'Sofía', 'González', 'sofia.gonzalez@upds.edu.bo', 'estudiante3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(17, 3, 'Valentina', 'Díaz', 'valentina.diaz@upds.edu.bo', 'estudiante4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(18, 3, 'Diego', 'Moreno', 'diego.moreno@upds.edu.bo', 'estudiante5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido);

-- TUTORES (con especialidad en español)
INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia, foto_perfil, perfil_linkedin, certificaciones, areas_expertise) VALUES
(1, 9, 'Matemáticas y Estadística', 'Profesor con 15 años de experiencia en matemática aplicada y estadística.', NULL, NULL, NULL, 'Matemáticas, Estadística, Álgebra Lineal, Cálculo'),
(2, 11, 'Física General', 'Físico titulado con especialización en mecánica clásica y electromagnetismo.', NULL, NULL, NULL, 'Física, Mecánica, Electromagnetismo, Termodinámica'),
(3, 12, 'Programación y Desarrollo Web', 'Desarrollador con experiencia en PHP, JavaScript y bases de datos.', NULL, NULL, NULL, 'Programación, Desarrollo Web, PHP, JavaScript, Bases de Datos'),
(4, 13, 'Inglés Técnico', 'Profesor de inglés con especialización en vocabulario técnico-científico.', NULL, NULL, NULL, 'Inglés, Inglés Técnico, Redacción Científica'),
(5, 14, 'Matemáticas Avanzadas', 'Matemático con experiencia en álgebra lineal, cálculo diferencial e integral.', NULL, NULL, NULL, 'Matemáticas, Álgebra, Cálculo, Geometría')
ON DUPLICATE KEY UPDATE especialidad = VALUES(especialidad);

-- RELACIÓN TUTORES-MATERIAS
INSERT INTO tutor_materia (id_tutor, id_materia) VALUES
(1, 1), (1, 2),
(2, 5),
(3, 3), (3, 4),
(4, 6),
(5, 1), (5, 2)
ON DUPLICATE KEY UPDATE id_tutor = VALUES(id_tutor);

-- ESTUDIANTES (con datos coherentes)
INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, registro_universitario) VALUES
(2, 10, 1, 3, 'RU-2026-0002'),
(4, 14, 1, 5, 'RU-2026-0004'),
(6, 15, 2, 2, 'RU-2026-0006'),
(8, 17, 1, 4, 'RU-2026-0008'),
(10, 18, 1, 6, 'RU-2026-0010')
ON DUPLICATE KEY UPDATE semestre = VALUES(semestre);
