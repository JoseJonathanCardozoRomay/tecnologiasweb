USE tutorias_db;

START TRANSACTION;

-- =====================================================
-- 1. CARRERAS: 20 nuevas
-- =====================================================

SET @carrera_inicio = (SELECT COALESCE(MAX(id_carrera), 0) FROM carreras) + 1;

INSERT INTO carreras (nombre_carrera)
WITH RECURSIVE n AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 20
)
SELECT CONCAT('Carrera de Prueba ', num)
FROM n;


-- =====================================================
-- 2. MATERIAS: 100 nuevas
-- =====================================================

SET @materia_inicio = (SELECT COALESCE(MAX(id_materia), 0) FROM materias) + 1;

INSERT INTO materias (nombre_materia, id_carrera)
WITH RECURSIVE n AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 100
)
SELECT
    CONCAT('Materia de Prueba ', num),
    @carrera_inicio + MOD(num - 1, 20)
FROM n;


-- =====================================================
-- 3. USUARIOS: 80 nuevos
--    60 estudiantes + 20 tutores
-- =====================================================

SET @usuario_inicio = (SELECT COALESCE(MAX(id_usuario), 0) FROM usuarios) + 1;

INSERT INTO usuarios
(
    id_rol,
    nombre,
    apellido,
    correo,
    usuario,
    contrasena_hash,
    telefono,
    estado
)
WITH RECURSIVE n AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 80
)
SELECT
    CASE
        WHEN num <= 60 THEN 3
        ELSE 2
    END,
    CONCAT('Usuario', num),
    CONCAT('Prueba', num),
    CONCAT('usuario', num, '@prueba.local'),
    CONCAT('usuario_prueba_', num),
    '$2y$12$zHRBnDrpU7MM6ICk10AU0uzpl.Lc.9v0siV6UgcctUhRgnyoA4Xoe',
    CONCAT('7000', LPAD(num, 4, '0')),
    'activo'
FROM n;


-- =====================================================
-- 4. ESTUDIANTES: 60
-- =====================================================

SET @estudiante_inicio =
    (SELECT COALESCE(MAX(id_estudiante), 0) FROM estudiantes) + 1;

INSERT INTO estudiantes
(
    id_usuario,
    id_carrera,
    semestre,
    registro_universitario
)
WITH RECURSIVE n AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 60
)
SELECT
    @usuario_inicio + num - 1,
    @carrera_inicio + MOD(num - 1, 20),
    MOD(num - 1, 10) + 1,
    CONCAT('RU-', LPAD(num, 6, '0'))
FROM n;


-- =====================================================
-- 5. TUTORES: 20
-- =====================================================

SET @tutor_inicio =
    (SELECT COALESCE(MAX(id_tutor), 0) FROM tutores) + 1;

INSERT INTO tutores
(
    id_usuario,
    especialidad,
    biografia
)
WITH RECURSIVE n AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 20
)
SELECT
    @usuario_inicio + 59 + num,
    CONCAT('Especialidad de Prueba ', num),
    CONCAT('Tutor de prueba numero ', num, ' para pruebas del sistema.')
FROM n;


-- =====================================================
-- 6. TUTOR - MATERIA: 100 relaciones
-- =====================================================

INSERT INTO tutor_materia
(
    id_tutor,
    id_materia
)
WITH RECURSIVE n AS (
    SELECT 0 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 99
)
SELECT
    @tutor_inicio + MOD(num, 20),
    @materia_inicio + FLOOR(num / 20)
FROM n;


-- =====================================================
-- 7. DISPONIBILIDAD: 40 registros
-- =====================================================

INSERT INTO disponibilidad_tutor
(
    id_tutor,
    dia_semana,
    hora_inicio,
    hora_fin
)
WITH RECURSIVE n AS (
    SELECT 0 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 39
)
SELECT
    @tutor_inicio + MOD(num, 20),
    CASE MOD(num, 6)
        WHEN 0 THEN 'Lunes'
        WHEN 1 THEN 'Martes'
        WHEN 2 THEN 'Miercoles'
        WHEN 3 THEN 'Jueves'
        WHEN 4 THEN 'Viernes'
        ELSE 'Sabado'
    END,
    CASE MOD(num, 3)
        WHEN 0 THEN '08:00:00'
        WHEN 1 THEN '10:00:00'
        ELSE '14:00:00'
    END,
    CASE MOD(num, 3)
        WHEN 0 THEN '10:00:00'
        WHEN 1 THEN '12:00:00'
        ELSE '16:00:00'
    END
FROM n;


-- =====================================================
-- 8. TUTORIAS: 200
-- =====================================================

SET @tutoria_inicio =
    (SELECT COALESCE(MAX(id_tutoria), 0) FROM tutorias) + 1;

INSERT INTO tutorias
(
    id_estudiante,
    id_tutor,
    id_materia,
    fecha,
    hora_inicio,
    hora_fin,
    modalidad,
    lugar_o_enlace,
    estado,
    observaciones
)
WITH RECURSIVE n AS (
    SELECT 0 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 199
)
SELECT
    @estudiante_inicio + MOD(num, 60),
    @tutor_inicio + MOD(num, 20),
    @materia_inicio + MOD(num, 100),
    DATE_ADD('2026-01-01', INTERVAL MOD(num, 250) DAY),
    CASE MOD(num, 4)
        WHEN 0 THEN '08:00:00'
        WHEN 1 THEN '10:00:00'
        WHEN 2 THEN '14:00:00'
        ELSE '16:00:00'
    END,
    CASE MOD(num, 4)
        WHEN 0 THEN '10:00:00'
        WHEN 1 THEN '12:00:00'
        WHEN 2 THEN '16:00:00'
        ELSE '18:00:00'
    END,
    CASE MOD(num, 2)
        WHEN 0 THEN 'presencial'
        ELSE 'virtual'
    END,
    CASE MOD(num, 2)
        WHEN 0 THEN CONCAT('Aula ', MOD(num, 20) + 1)
        ELSE CONCAT('https://meet.prueba.local/sesion/', num + 1)
    END,
    CASE MOD(num, 4)
        WHEN 0 THEN 'pendiente'
        WHEN 1 THEN 'confirmada'
        WHEN 2 THEN 'realizada'
        ELSE 'cancelada'
    END,
    CONCAT('Tutoria de prueba numero ', num + 1)
FROM n;


-- =====================================================
-- 9. EVALUACIONES: 100
-- =====================================================

INSERT INTO evaluaciones_tutoria
(
    id_tutoria,
    calificacion,
    comentario
)
WITH RECURSIVE n AS (
    SELECT 0 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 99
)
SELECT
    @tutoria_inicio + num,
    MOD(num, 5) + 1,
    CONCAT('Evaluacion de prueba numero ', num + 1)
FROM n;


-- =====================================================
-- 10. REGISTRO DE ACCESOS: 300
-- =====================================================

INSERT INTO registro_accesos
(
    id_usuario,
    ip_origen,
    resultado
)
WITH RECURSIVE n AS (
    SELECT 0 AS num
    UNION ALL
    SELECT num + 1 FROM n WHERE num < 299
)
SELECT
    @usuario_inicio + MOD(num, 80),
    CONCAT('192.168.75.', MOD(num, 50) + 1),
    CASE
        WHEN MOD(num, 5) = 0 THEN 'fallido'
        ELSE 'exitoso'
    END
FROM n;


COMMIT;


-- =====================================================
-- RESUMEN DE DATOS
-- =====================================================

SELECT 'carreras' AS tabla, COUNT(*) AS cantidad FROM carreras
UNION ALL
SELECT 'materias', COUNT(*) FROM materias
UNION ALL
SELECT 'usuarios', COUNT(*) FROM usuarios
UNION ALL
SELECT 'estudiantes', COUNT(*) FROM estudiantes
UNION ALL
SELECT 'tutores', COUNT(*) FROM tutores
UNION ALL
SELECT 'tutor_materia', COUNT(*) FROM tutor_materia
UNION ALL
SELECT 'disponibilidad_tutor', COUNT(*) FROM disponibilidad_tutor
UNION ALL
SELECT 'tutorias', COUNT(*) FROM tutorias
UNION ALL
SELECT 'evaluaciones_tutoria', COUNT(*) FROM evaluaciones_tutoria
UNION ALL
SELECT 'registro_accesos', COUNT(*) FROM registro_accesos;
