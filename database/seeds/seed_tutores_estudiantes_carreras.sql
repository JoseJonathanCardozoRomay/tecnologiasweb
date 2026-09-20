-- ============================================
-- SEED: Tutores y Estudiantes para Carreras Fundamentales
-- Agrega tutores y estudiantes para Ingeniería Civil, Administración de Empresas, Medicina, Contabilidad, Psicología
-- ============================================

SET NAMES utf8mb4;

-- NUEVOS TUTORES (para las nuevas carreras)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(19, 2, 'Roberto', 'Mendoza', 'roberto.mendoza@upds.edu.bo', 'tutor11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(20, 2, 'Laura', 'Sánchez', 'laura.sanchez@upds.edu.bo', 'tutor12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(21, 2, 'Miguel', 'Torres', 'miguel.torres@upds.edu.bo', 'tutor13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(22, 2, 'Carmen', 'Vargas', 'carmen.vargas@upds.edu.bo', 'tutor14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(23, 2, 'Jorge', 'Luna', 'jorge.luna@upds.edu.bo', 'tutor15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido);

INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia, foto_perfil, perfil_linkedin, certificaciones, areas_expertise) VALUES
(8, 19, 'Ingeniería Civil', 'Ingeniero civil con 12 años de experiencia en construcción y diseño estructural.', NULL, NULL, NULL, 'Ingeniería Civil, Estructuras, Mecánica de Suelos, Hidráulica'),
(9, 20, 'Administración de Empresas', 'Licenciada en administración con experiencia en gestión empresarial y marketing.', NULL, NULL, NULL, 'Administración, Economía, Marketing, Recursos Humanos'),
(10, 21, 'Medicina', 'Médico general con especialización en medicina interna y urgencias.', NULL, NULL, NULL, 'Medicina, Anatomía, Fisiología, Farmacología'),
(11, 22, 'Contabilidad', 'Contadora pública con experiencia en auditoría fiscal y contabilidad financiera.', NULL, NULL, NULL, 'Contabilidad, Auditoría, Costos, Finanzas'),
(12, 23, 'Psicología', 'Psicólogo clínico con experiencia en psicología organizacional y clínica.', NULL, NULL, NULL, 'Psicología, Psicología Clínica, Psicología Social, Psicología Organizacional')
ON DUPLICATE KEY UPDATE especialidad = VALUES(especialidad);

-- Relación tutores-materias
INSERT INTO tutor_materia (id_tutor, id_materia) VALUES
(8, 10), (8, 11), (8, 12), (8, 13),
(9, 14), (9, 15), (9, 16), (9, 17),
(10, 18), (10, 19), (10, 20), (10, 21),
(11, 22), (11, 23), (11, 24), (11, 25),
(12, 26), (12, 27), (12, 28), (12, 29)
ON DUPLICATE KEY UPDATE id_tutor = VALUES(id_tutor);

-- NUEVOS ESTUDIANTES (para las nuevas carreras)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(24, 3, 'Gabriel', 'Ramírez', 'gabriel.ramirez@upds.edu.bo', 'estudiante7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(25, 3, 'Elena', 'Castro', 'elena.castro@upds.edu.bo', 'estudiante8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(26, 3, 'Fernando', 'Ortiz', 'fernando.ortiz@upds.edu.bo', 'estudiante9', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(27, 3, 'Andrea', 'Morales', 'andrea.morales@upds.edu.bo', 'estudiante10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(28, 3, 'Ricardo', 'Flores', 'ricardo.flores@upds.edu.bo', 'estudiante11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido);

INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, registro_universitario) VALUES
(12, 24, 4, 4, 'RU-2026-0012'),
(13, 25, 5, 3, 'RU-2026-0013'),
(14, 26, 6, 2, 'RU-2026-0014'),
(15, 27, 7, 5, 'RU-2026-0015'),
(16, 28, 8, 4, 'RU-2026-0016')
ON DUPLICATE KEY UPDATE semestre = VALUES(semestre);

-- Verificar después con:
-- SELECT id_usuario, nombre, apellido FROM usuarios ORDER BY id_usuario;
-- SELECT id_estudiante, e.id_usuario, u.nombre, u.apellido, c.nombre_carrera FROM estudiantes e INNER JOIN usuarios u ON e.id_usuario = u.id_usuario INNER JOIN carreras c ON e.id_carrera = c.id_carrera ORDER BY e.id_estudiante;
