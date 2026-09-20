-- ============================================
-- SEED: 100 tutorías con estados variados
-- Fecha: septiembre-octubre 2026
-- Todos los datos en español
-- ============================================

-- PENDIENTE (20 registros — estudiantes esperando confirmación)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-09-02', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Solicitud de apoyo en Base de Datos I, pendiente de confirmación.', NULL, '2026-09-01 08:30:00'),
(2, 1, 2, 2, '2026-09-04', 'I-2026', '10:00:00', '12:00:00', NULL, 'virtual', 'pendiente', 'Necesita ayuda con Programación I. Por confirmar.', NULL, '2026-09-03 10:15:00'),
(3, 3, 3, 3, '2026-09-05', 'I-2026', '14:00:00', '16:00:00', NULL, 'presencial', 'pendiente', 'Tutoría pendiente de aprobación por coordinación.', NULL, '2026-09-03 14:00:00'),
(4, 2, 5, 4, '2026-09-06', 'I-2026', '18:00:00', '20:00:00', NULL, 'presencial', 'pendiente', 'Solicitud enviada, esperando respuesta del tutor.', NULL, '2026-09-04 09:00:00'),
(5, 4, 6, 1, '2026-09-09', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Estudiante requiere apoyo en Derecho.', NULL, '2026-09-05 11:00:00'),
(6, 5, 1, 2, '2026-09-10', 'I-2026', '10:00:00', '12:00:00', NULL, 'virtual', 'pendiente', 'Solicitud de tutoría sin confirmar. A la espera.', NULL, '2026-09-06 08:00:00'),
(2, 1, 2, 3, '2026-09-11', 'I-2026', '14:00:00', '16:00:00', NULL, 'presencial', 'pendiente', 'Pendiente de confirmación. Estudiante solicitó por correo.', NULL, '2026-09-07 15:20:00'),
(3, 3, 3, 4, '2026-09-12', 'I-2026', '18:00:00', '20:00:00', NULL, 'presencial', 'pendiente', 'Tutoría solicitada, aún no se ha asignado tutor.', NULL, '2026-09-08 10:00:00'),
(4, 2, 5, 1, '2026-09-13', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Pendiente de asignación de tutor y aprobación.', NULL, '2026-09-09 08:00:00'),
(5, 4, 6, 2, '2026-09-16', 'I-2026', '10:00:00', '12:00:00', NULL, 'virtual', 'pendiente', 'Solicitud sin confirmar. Revisión pendiente por parte del tutor.', NULL, '2026-09-10 13:00:00'),
(6, 5, 1, 3, '2026-09-17', 'I-2026', '14:00:00', '16:00:00', NULL, 'presencial', 'pendiente', 'Tutoría de Base de Datos pendiente de confirmación.', NULL, '2026-09-11 09:30:00'),
(2, 1, 2, 4, '2026-09-18', 'I-2026', '18:00:00', '20:00:00', NULL, 'virtual', 'pendiente', 'Solicitud virtual pendiente de aprobación.', NULL, '2026-09-12 14:00:00'),
(3, 3, 3, 1, '2026-09-19', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Estudiante necesita repaso de desarrollo web.', NULL, '2026-09-13 10:00:00'),
(4, 2, 5, 2, '2026-09-20', 'I-2026', '10:00:00', '12:00:00', NULL, 'presencial', 'pendiente', 'Tutoría de derecho pendiente de coordinación.', NULL, '2026-09-14 11:00:00'),
(5, 4, 6, 3, '2026-09-23', 'I-2026', '14:00:00', '16:00:00', NULL, 'virtual', 'pendiente', 'Programación: estructuras de control pendientes.', NULL, '2026-09-15 15:00:00'),
(6, 5, 1, 4, '2026-09-24', 'I-2026', '18:00:00', '20:00:00', NULL, 'presencial', 'pendiente', 'Base de Datos: normalización pendiente.', NULL, '2026-09-16 09:00:00'),
(2, 1, 2, 1, '2026-09-25', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Programación: algoritmos pendiente.', NULL, '2026-09-17 10:30:00'),
(3, 3, 3, 2, '2026-09-26', 'I-2026', '10:00:00', '12:00:00', NULL, 'virtual', 'pendiente', 'Desarrollo web: HTML pendiente.', NULL, '2026-09-18 14:00:00'),
(4, 2, 5, 3, '2026-09-27', 'I-2026', '14:00:00', '16:00:00', NULL, 'presencial', 'pendiente', 'Derecho: teoría general pendiente.', NULL, '2026-09-19 11:00:00'),
(5, 4, 6, 4, '2026-09-30', 'I-2026', '18:00:00', '20:00:00', NULL, 'virtual', 'pendiente', 'Derecho penal: procedimientos pendientes.', NULL, '2026-09-20 15:30:00'),
(6, 5, 1, 1, '2026-10-01', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'pendiente', 'Base de Datos: consultas SQL pendientes.', NULL, '2026-09-21 09:00:00');

-- CONFIRMADA (25 registros — tutor aceptó, lista para ejecutarse)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-09-01', 'I-2026', '08:00:00', '10:00:00', 'Aula 101', 'presencial', 'confirmada', 'Tutoría confirmada por el tutor para Base de Datos I.', NULL, '2026-08-31 09:00:00'),
(4, 3, 3, 2, '2026-09-02', 'I-2026', '10:00:00', '12:00:00', 'Aula 102', 'presencial', 'confirmada', 'Sesión confirmada para Desarrollo Web.', NULL, '2026-09-01 10:00:00'),
(6, 2, 5, 3, '2026-09-03', 'I-2026', '14:00:00', '16:00:00', 'Aula 103', 'presencial', 'confirmada', 'Confirmada por coordinación académica.', NULL, '2026-09-02 14:00:00'),
(8, 4, 6, 4, '2026-09-04', 'I-2026', '18:00:00', '20:00:00', 'Aula 104', 'presencial', 'confirmada', 'Derecho confirmado para revisión de examen.', NULL, '2026-09-03 09:00:00'),
(10, 5, 1, 1, '2026-09-05', 'I-2026', '08:00:00', '10:00:00', 'Aula 105', 'presencial', 'confirmada', 'Base de Datos confirmada para repaso de consultas.', NULL, '2026-09-04 11:00:00'),
(2, 1, 2, 2, '2026-09-06', 'I-2026', '10:00:00', '12:00:00', 'Aula 106', 'presencial', 'confirmada', 'Programación confirmada para repaso de estructuras.', NULL, '2026-09-05 08:00:00'),
(4, 3, 3, 3, '2026-09-07', 'I-2026', '14:00:00', '16:00:00', 'Aula 107', 'presencial', 'confirmada', 'Desarrollo web: JavaScript confirmado.', NULL, '2026-09-06 15:00:00'),
(6, 2, 5, 4, '2026-09-08', 'I-2026', '18:00:00', '20:00:00', 'Aula 108', 'presencial', 'confirmada', 'Derecho: teoría general confirmado.', NULL, '2026-09-07 10:00:00'),
(8, 4, 6, 1, '2026-09-09', 'I-2026', '08:00:00', '10:00:00', 'Aula 109', 'presencial', 'confirmada', 'Derecho penal: introducción confirmada.', NULL, '2026-09-08 09:00:00'),
(10, 5, 1, 2, '2026-09-10', 'I-2026', '10:00:00', '12:00:00', 'Aula 110', 'presencial', 'confirmada', 'Base de Datos: algoritmos confirmados.', NULL, '2026-09-09 13:00:00'),
(2, 1, 2, 3, '2026-09-11', 'I-2026', '14:00:00', '16:00:00', 'Aula 111', 'presencial', 'confirmada', 'Programación: funciones confirmadas.', NULL, '2026-09-10 09:30:00'),
(4, 3, 3, 4, '2026-09-12', 'I-2026', '18:00:00', '20:00:00', 'Aula 112', 'presencial', 'confirmada', 'Desarrollo web: CSS confirmado.', NULL, '2026-09-11 14:00:00'),
(6, 2, 5, 1, '2026-09-13', 'I-2026', '08:00:00', '10:00:00', 'Aula 113', 'presencial', 'confirmada', 'Derecho: procedimientos confirmados.', NULL, '2026-09-12 10:00:00'),
(8, 4, 6, 2, '2026-09-14', 'I-2026', '10:00:00', '12:00:00', 'Aula 114', 'presencial', 'confirmada', 'Derecho penal: tipos de delitos confirmado.', NULL, '2026-09-13 11:00:00'),
(10, 5, 1, 3, '2026-09-15', 'I-2026', '14:00:00', '16:00:00', 'Aula 115', 'presencial', 'confirmada', 'Base de Datos: índices confirmados.', NULL, '2026-09-14 15:00:00'),
(2, 1, 2, 4, '2026-09-16', 'I-2026', '18:00:00', '20:00:00', 'Aula 116', 'presencial', 'confirmada', 'Programación: arrays confirmados.', NULL, '2026-09-15 09:00:00'),
(4, 3, 3, 1, '2026-09-17', 'I-2026', '08:00:00', '10:00:00', 'Aula 117', 'presencial', 'confirmada', 'Desarrollo web: PHP confirmado.', NULL, '2026-09-16 10:30:00'),
(6, 2, 5, 2, '2026-09-18', 'I-2026', '10:00:00', '12:00:00', 'Aula 118', 'presencial', 'confirmada', 'Derecho: jurisprudencia confirmada.', NULL, '2026-09-17 14:00:00'),
(8, 4, 6, 3, '2026-09-19', 'I-2026', '14:00:00', '16:00:00', 'Aula 119', 'presencial', 'confirmada', 'Derecho penal: sanciones confirmado.', NULL, '2026-09-18 11:00:00'),
(10, 5, 1, 4, '2026-09-20', 'I-2026', '18:00:00', '20:00:00', 'Aula 120', 'presencial', 'confirmada', 'Base de Datos: vistas confirmadas.', NULL, '2026-09-19 15:30:00'),
(2, 1, 1, 1, '2026-09-21', 'I-2026', '08:00:00', '10:00:00', 'Aula 121', 'presencial', 'confirmada', 'Base de Datos: procedimientos confirmados.', NULL, '2026-09-20 09:00:00'),
(4, 3, 3, 2, '2026-09-22', 'I-2026', '10:00:00', '12:00:00', 'Aula 122', 'presencial', 'confirmada', 'Desarrollo web: frameworks confirmado.', NULL, '2026-09-21 10:00:00'),
(6, 2, 5, 3, '2026-09-23', 'I-2026', '14:00:00', '16:00:00', 'Aula 123', 'presencial', 'confirmada', 'Derecho: constitución confirmada.', NULL, '2026-09-22 14:00:00'),
(8, 4, 6, 4, '2026-09-24', 'I-2026', '18:00:00', '20:00:00', 'Aula 124', 'presencial', 'confirmada', 'Derecho penal: proceso penal confirmado.', NULL, '2026-09-23 11:00:00'),
(10, 5, 1, 1, '2026-09-25', 'I-2026', '08:00:00', '10:00:00', 'Aula 125', 'presencial', 'confirmada', 'Base de Datos: seguridad confirmada.', NULL, '2026-09-24 13:00:00'),
(2, 1, 2, 2, '2026-09-26', 'I-2026', '10:00:00', '12:00:00', 'Aula 126', 'presencial', 'confirmada', 'Programación: recursividad confirmada.', NULL, '2026-09-25 09:30:00');

-- EN_PROCESO (15 registros — sesión iniciada, en desarrollo)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-09-20', 'I-2026', '08:00:00', '10:00:00', 'Aula 101', 'presencial', 'en_proceso', 'Sesión iniciada. Trabajando en consultas SQL.', NULL, '2026-09-19 09:00:00'),
(4, 3, 3, 2, '2026-09-20', 'I-2026', '10:00:00', '12:00:00', 'Aula 102', 'presencial', 'en_proceso', 'Sesión en curso. Revisando desarrollo web.', NULL, '2026-09-19 10:00:00'),
(6, 2, 5, 3, '2026-09-20', 'I-2026', '14:00:00', '16:00:00', 'Aula 103', 'presencial', 'en_proceso', 'Desarrollando teoría general de derecho.', NULL, '2026-09-19 14:00:00'),
(8, 4, 6, 4, '2026-09-20', 'I-2026', '18:00:00', '20:00:00', 'Aula 104', 'presencial', 'en_proceso', 'Explicando procedimientos de derecho penal.', NULL, '2026-09-19 18:00:00'),
(10, 5, 1, 1, '2026-09-19', 'I-2026', '08:00:00', '10:00:00', 'Aula 105', 'presencial', 'en_proceso', 'Programando consultas en Base de Datos.', NULL, '2026-09-18 09:00:00'),
(2, 1, 2, 2, '2026-09-19', 'I-2026', '10:00:00', '12:00:00', 'Aula 106', 'presencial', 'en_proceso', 'Practicando estructuras de control en programación.', NULL, '2026-09-18 10:00:00'),
(4, 3, 3, 3, '2026-09-19', 'I-2026', '14:00:00', '16:00:00', 'Aula 107', 'presencial', 'en_proceso', 'Tutorial de JavaScript en progreso.', NULL, '2026-09-18 14:00:00'),
(6, 2, 5, 4, '2026-09-19', 'I-2026', '18:00:00', '20:00:00', 'Aula 108', 'presencial', 'en_proceso', 'Estudiando jurisprudencia aplicada.', NULL, '2026-09-18 18:00:00'),
(8, 4, 6, 1, '2026-09-18', 'I-2026', '08:00:00', '10:00:00', 'Aula 109', 'presencial', 'en_proceso', 'Explicando tipos de delitos.', NULL, '2026-09-17 09:00:00'),
(10, 5, 1, 2, '2026-09-18', 'I-2026', '10:00:00', '12:00:00', 'Aula 110', 'presencial', 'en_proceso', 'Depurando algoritmos de programación.', NULL, '2026-09-17 10:00:00'),
(2, 1, 2, 3, '2026-09-18', 'I-2026', '14:00:00', '16:00:00', 'Aula 111', 'presencial', 'en_proceso', 'Escribiendo funciones en programación.', NULL, '2026-09-17 14:00:00'),
(4, 3, 3, 4, '2026-09-18', 'I-2026', '18:00:00', '20:00:00', 'Aula 112', 'presencial', 'en_proceso', 'Implementando CSS layout.', NULL, '2026-09-17 18:00:00'),
(6, 2, 5, 1, '2026-09-17', 'I-2026', '08:00:00', '10:00:00', 'Aula 113', 'presencial', 'en_proceso', 'Analizando constitución política.', NULL, '2026-09-16 09:00:00'),
(8, 4, 6, 2, '2026-09-17', 'I-2026', '10:00:00', '12:00:00', 'Aula 114', 'presencial', 'en_proceso', 'Estudiando sanciones penales.', NULL, '2026-09-16 10:00:00'),
(10, 5, 1, 3, '2026-09-17', 'I-2026', '14:00:00', '16:00:00', 'Aula 115', 'presencial', 'en_proceso', 'Resolviendo problemas con arrays.', NULL, '2026-09-16 14:00:00'),
(2, 1, 1, 4, '2026-09-17', 'I-2026', '18:00:00', '20:00:00', 'Aula 116', 'presencial', 'en_proceso', 'Creando vistas para reportes.', NULL, '2026-09-16 18:00:00');

-- REALIZADA (25 registros — sesión completada con éxito)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-08-25', 'I-2026', '08:00:00', '10:00:00', 'Aula 101', 'presencial', 'realizada', 'Sesión completada. Estudiante entendió consultas básicas.', NULL, '2026-08-24 09:00:00'),
(4, 3, 3, 2, '2026-08-26', 'I-2026', '10:00:00', '12:00:00', 'Aula 102', 'presencial', 'realizada', 'Desarrollo web completado con éxito.', NULL, '2026-08-25 10:00:00'),
(6, 2, 5, 3, '2026-08-27', 'I-2026', '14:00:00', '16:00:00', 'Aula 103', 'presencial', 'realizada', 'Teoría general de derecho completada.', NULL, '2026-08-26 14:00:00'),
(8, 4, 6, 4, '2026-08-28', 'I-2026', '18:00:00', '20:00:00', 'Aula 104', 'presencial', 'realizada', 'Derecho penal: procedimientos cubiertos.', NULL, '2026-08-27 18:00:00'),
(10, 5, 1, 1, '2026-08-29', 'I-2026', '08:00:00', '10:00:00', 'Aula 105', 'presencial', 'realizada', 'Base de Datos: consultas completadas.', NULL, '2026-08-28 09:00:00'),
(2, 1, 2, 2, '2026-08-30', 'I-2026', '10:00:00', '12:00:00', 'Aula 106', 'presencial', 'realizada', 'Programación: estructuras completadas.', NULL, '2026-08-29 10:00:00'),
(4, 3, 3, 3, '2026-08-31', 'I-2026', '14:00:00', '16:00:00', 'Aula 107', 'presencial', 'realizada', 'JavaScript tutorial completado.', NULL, '2026-08-30 14:00:00'),
(6, 2, 5, 4, '2026-09-01', 'I-2026', '18:00:00', '20:00:00', 'Aula 108', 'presencial', 'realizada', 'Jurisprudencia explicada con éxito.', NULL, '2026-08-31 18:00:00'),
(8, 4, 6, 1, '2026-09-02', 'I-2026', '08:00:00', '10:00:00', 'Aula 109', 'presencial', 'realizada', 'Tipos de delitos explicados.', NULL, '2026-09-01 09:00:00'),
(10, 5, 1, 2, '2026-09-03', 'I-2026', '10:00:00', '12:00:00', 'Aula 110', 'presencial', 'realizada', 'Algoritmos resueltos correctamente.', NULL, '2026-09-02 10:00:00'),
(2, 1, 2, 3, '2026-09-04', 'I-2026', '14:00:00', '16:00:00', 'Aula 111', 'presencial', 'realizada', 'Funciones creadas y probadas.', NULL, '2026-09-03 14:00:00'),
(4, 3, 3, 4, '2026-09-05', 'I-2026', '18:00:00', '20:00:00', 'Aula 112', 'presencial', 'realizada', 'CSS implementado con éxito.', NULL, '2026-09-04 18:00:00'),
(6, 2, 5, 1, '2026-09-06', 'I-2026', '08:00:00', '10:00:00', 'Aula 113', 'presencial', 'realizada', 'Constitución explicada y comprendida.', NULL, '2026-09-05 09:00:00'),
(8, 4, 6, 2, '2026-09-07', 'I-2026', '10:00:00', '12:00:00', 'Aula 114', 'presencial', 'realizada', 'Sanciones penales explicadas.', NULL, '2026-09-06 10:00:00'),
(10, 5, 1, 3, '2026-09-08', 'I-2026', '14:00:00', '16:00:00', 'Aula 115', 'presencial', 'realizada', 'Arrays bidimensionales comprendidos.', NULL, '2026-09-07 14:00:00'),
(2, 1, 1, 4, '2026-09-09', 'I-2026', '18:00:00', '20:00:00', 'Aula 116', 'presencial', 'realizada', 'Vistas creadas para reportes.', NULL, '2026-09-08 18:00:00'),
(4, 3, 3, 1, '2026-09-10', 'I-2026', '08:00:00', '10:00:00', 'Aula 117', 'presencial', 'realizada', 'PHP backend completado.', NULL, '2026-09-09 09:00:00'),
(6, 2, 5, 2, '2026-09-11', 'I-2026', '10:00:00', '12:00:00', 'Aula 118', 'presencial', 'realizada', 'Proceso penal estudiado.', NULL, '2026-09-10 10:00:00'),
(8, 4, 6, 3, '2026-09-12', 'I-2026', '14:00:00', '16:00:00', 'Aula 119', 'presencial', 'realizada', 'Sanciones explicadas y aplicadas.', NULL, '2026-09-11 14:00:00'),
(10, 5, 1, 4, '2026-09-13', 'I-2026', '18:00:00', '20:00:00', 'Aula 120', 'presencial', 'realizada', 'Procedimientos de Base de Datos dominados.', NULL, '2026-09-12 18:00:00'),
(2, 1, 2, 1, '2026-09-14', 'I-2026', '08:00:00', '10:00:00', 'Aula 121', 'presencial', 'realizada', 'Recursividad comprendida.', NULL, '2026-09-13 09:00:00'),
(4, 3, 3, 2, '2026-09-15', 'I-2026', '10:00:00', '12:00:00', 'Aula 122', 'presencial', 'realizada', 'Frameworks de desarrollo web completados.', NULL, '2026-09-14 10:00:00'),
(6, 2, 5, 3, '2026-09-16', 'I-2026', '14:00:00', '16:00:00', 'Aula 123', 'presencial', 'realizada', 'Constitución política dominada.', NULL, '2026-09-15 14:00:00'),
(8, 4, 6, 4, '2026-09-17', 'I-2026', '18:00:00', '20:00:00', 'Aula 124', 'presencial', 'realizada', 'Proceso penal completo.', NULL, '2026-09-16 18:00:00'),
(10, 5, 1, 1, '2026-09-18', 'I-2026', '08:00:00', '10:00:00', 'Aula 125', 'presencial', 'realizada', 'Seguridad de Base de Datos practicada.', NULL, '2026-09-17 09:00:00'),
(2, 1, 1, 2, '2026-09-19', 'I-2026', '10:00:00', '12:00:00', 'Aula 126', 'presencial', 'realizada', 'Backup y restore practicados.', NULL, '2026-09-18 10:00:00');

-- DETENIDO (5 registros — sesión interrumpida con observaciones en español)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-09-14', 'I-2026', '08:00:00', '10:00:00', 'Aula 101', 'presencial', 'detenido', 'Sesión interrumpida por falla de conexión. Reagendada para el 15/09.', NULL, '2026-09-13 09:00:00'),
(4, 3, 3, 2, '2026-09-15', 'I-2026', '10:00:00', '12:00:00', 'Aula 102', 'presencial', 'detenido', 'Sesión detenida por problemas técnicos del estudiante. Reagendada para el 17/09.', NULL, '2026-09-14 10:00:00'),
(6, 2, 5, 3, '2026-09-16', 'I-2026', '14:00:00', '16:00:00', 'Aula 103', 'presencial', 'detenido', 'Tutoría interrumpida por urgencia médica del estudiante. Por reprogramar.', NULL, '2026-09-15 14:00:00'),
(8, 4, 6, 4, '2026-09-17', 'I-2026', '18:00:00', '20:00:00', 'Aula 104', 'presencial', 'detenido', 'Sesión detenida por falla en el aula. Reagendada para el 18/09.', NULL, '2026-09-16 18:00:00'),
(10, 5, 1, 1, '2026-09-18', 'I-2026', '08:00:00', '10:00:00', 'Aula 105', 'presencial', 'detenido', 'Sesión interrumpida por falla eléctrica en el aula. Reagendada para el 19/09.', NULL, '2026-09-17 09:00:00');

-- CANCELADA (10 registros — con motivo de cancelación en español)
INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, id_bloque, fecha, periodo, hora_inicio, hora_fin, lugar_o_enlace, modalidad, estado, observaciones, motivo_cancelacion, fecha_solicitud) VALUES
(2, 1, 1, 1, '2026-09-03', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por falta de disponibilidad del tutor. Estudiante notificado.', 'El tutor no está disponible en este horario', '2026-08-30 08:00:00'),
(4, 3, 3, 2, '2026-09-05', 'I-2026', '10:00:00', '12:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por solicitud del estudiante. Se reprogramará.', 'El estudiante solicitó la cancelación por motivos personales', '2026-08-31 11:00:00'),
(6, 2, 5, 3, '2026-09-08', 'I-2026', '14:00:00', '16:00:00', 'Aula 103', 'presencial', 'cancelada', 'Cancelada por cambio de horario por decisión del coordinador.', 'Cambio de horario por decisión de coordinación', '2026-09-02 15:00:00'),
(8, 4, 6, 4, '2026-09-12', 'I-2026', '18:00:00', '20:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por falta de interés del estudiante.', 'El estudiante manifestó que no necesita más tutorías por ahora', '2026-09-07 10:00:00'),
(10, 5, 1, 1, '2026-09-15', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por disponibilidad del tutor. Nueva sesión agendada.', 'El tutor no puede asistir debido a compromiso previo', '2026-09-10 14:00:00'),
(2, 1, 2, 2, '2026-09-17', 'I-2026', '10:00:00', '12:00:00', 'Aula 106', 'presencial', 'cancelada', 'Cancelada por decisión de la coordinación académica.', 'Cambio de agenda académica por coordinación', '2026-09-14 09:00:00'),
(4, 3, 3, 3, '2026-09-20', 'I-2026', '14:00:00', '16:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por solicitud del estudiante. Discontinua la tutoría.', 'El estudiante decidió no continuar con la tutoría', '2026-09-15 16:00:00'),
(6, 2, 5, 4, '2026-09-23', 'I-2026', '18:00:00', '20:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por inconveniencia del tutor. Se reagendará.', 'El tutor canceló por enfermedad', '2026-09-18 10:00:00'),
(8, 4, 6, 1, '2026-09-25', 'I-2026', '08:00:00', '10:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por decisión del estudiante. Se le ofreció reprogramar.', 'El estudiante solicitó la cancelación', '2026-09-20 14:00:00'),
(10, 5, 1, 2, '2026-09-27', 'I-2026', '10:00:00', '12:00:00', NULL, 'presencial', 'cancelada', 'Cancelada por problemas técnicos de conexión. Reagendada para el 29/09.', 'Falla de conexión persistente. Se reagendará', '2026-09-22 11:00:00');
