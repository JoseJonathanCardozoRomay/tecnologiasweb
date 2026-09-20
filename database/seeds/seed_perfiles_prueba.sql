-- Seed de perfiles de prueba para Sistema de Tutorías UPDS
-- Crea 5 tutores nuevos y 5 estudiantes nuevos con perfiles completos
-- Generado: 2026-09-20

-- === TUTORES DE PRUEBA (5 tutores nuevos) ===
-- Usuario: tutor2, tutor3, tutor4, tutor5, tutor6
-- Todos con password = 'password' (hash bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)

INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado)
VALUES
(9, 2, 'Carlos', 'García', 'carlos.garcia@upds.edu.bo', 'tutor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(10, 2, 'María', 'López', 'maria.lopez@upds.edu.bo', 'tutor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(11, 2, 'Juan', 'Pérez', 'juan.perez@upds.edu.bo', 'tutor4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(12, 2, 'Ana', 'Martínez', 'ana.martinez@upds.edu.bo', 'tutor5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(13, 2, 'Pedro', 'Rodríguez', 'pedro.rodriguez@upds.edu.bo', 'tutor6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia, foto_perfil, perfil_linkedin, certificaciones, areas_expertise)
VALUES
(3, 9, 'Bases de Datos y Estadística', 'Profesor con 15 años de experiencia en bases de datos y estadística. Especialista en MySQL, PostgreSQL y análisis de datos.', NULL, NULL, NULL, 'Base de Datos, SQL, Estadística, Análisis de Datos'),
(4, 10, 'Desarrollo Web Full Stack', 'Desarrollador full stack con React, Node.js y PHP. 8 años de experiencia en proyectos web empresariales.', NULL, NULL, NULL, 'Desarrollo Web, JavaScript, React, Node.js, PHP, HTML/CSS'),
(5, 11, 'Tecnología Web', 'Ingeniero especializado en tecnologías web modernas. Experiencia en desarrollo frontend y backend.', NULL, NULL, NULL, 'Tecnología Web, Frontend, Backend, APIs'),
(6, 12, 'Bases de Datos Avanzadas', 'Ingeniero de bases de datos. Experiencia en MySQL, PostgreSQL, modelado entidad-relación y optimización de consultas.', NULL, NULL, NULL, 'Base de Datos, SQL, MySQL, PostgreSQL, Modelado de Datos'),
(7, 13, 'Estadística Aplicada', 'Profesor de estadística con especialización en análisis estadístico aplicado a ingeniería.', NULL, NULL, NULL, 'Estadística, Análisis de Datos, Probabilidad')
ON DUPLICATE KEY UPDATE especialidad = VALUES(especialidad);

-- Asignar materias a cada tutor (usando materias existentes)
INSERT INTO tutor_materia (id_tutor, id_materia) VALUES
(3, 1), (3, 8),  -- Tutor 3: Base de Datos I, Estadística 1
(4, 2), (4, 3),  -- Tutor 4: Programación I, Tecnología Web I
(5, 3),           -- Tutor 5: Tecnología Web I
(6, 1), (6, 2), (6, 8), -- Tutor 6: Base de Datos I, Programación I, Estadística 1
(7, 8)            -- Tutor 7: Estadística 1
ON DUPLICATE KEY UPDATE id_tutor = VALUES(id_tutor);

-- === ESTUDIANTES DE PRUEBA (5 estudiantes nuevos) ===
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado)
VALUES
(14, 3, 'Luis', 'Fernández', 'luis.fernandez@upds.edu.bo', 'estudiante2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(15, 3, 'Sofía', 'González', 'sofia.gonzalez@upds.edu.bo', 'estudiante3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(16, 3, 'Miguel', 'Ruiz', 'miguel.ruiz@upds.edu.bo', 'estudiante4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(17, 3, 'Valentina', 'Díaz', 'valentina.diaz@upds.edu.bo', 'estudiante5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(18, 3, 'Diego', 'Moreno', 'diego.moreno@upds.edu.bo', 'estudiante6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, registro_universitario)
VALUES
(6, 14, 1, 4, 'RU-2026-0006'),
(7, 15, 1, 3, 'RU-2026-0007'),
(8, 16, 1, 5, 'RU-2026-0008'),
(9, 17, 2, 2, 'RU-2026-0009'),
(10, 18, 1, 6, 'RU-2026-0010')
ON DUPLICATE KEY UPDATE semestre = VALUES(semestre);

-- === ACTUALIZAR BLOQUES HORARIOS (si es necesario) ===
INSERT INTO bloques_horarios (id_bloque, nombre_bloque, hora_inicio, hora_fin, descripcion)
VALUES
(1, 'Mañana', '08:00:00', '10:00:00', '8:00 AM - 10:00 AM'),
(2, 'Mañana Tardío', '10:00:00', '12:00:00', '10:00 AM - 12:00 PM'),
(3, 'Tarde', '14:00:00', '16:00:00', '2:00 PM - 4:00 PM'),
(4, 'Noche', '18:00:00', '20:00:00', '6:00 PM - 8:00 PM')
ON DUPLICATE KEY UPDATE nombre_bloque = VALUES(nombre_bloque);
