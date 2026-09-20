-- ============================================
-- CORRECCIÓN NOMBRES UTF-8
-- Ejecutar con: docker exec -i tutorias_db mysql -u tutores_user -p'PASSWORD' tutorias_db < database/seeds/correcion_nombres.sql
-- ============================================

SET NAMES utf8mb4;

-- Intentar corrección directa primero
UPDATE usuarios SET nombre = 'Valentina', apellido = 'Díaz' WHERE id_usuario = 17;
UPDATE usuarios SET nombre = 'Sofía', apellido = 'González' WHERE id_usuario = 15;
UPDATE usuarios SET nombre = 'Luis', apellido = 'Fernández' WHERE id_usuario = 14;
UPDATE usuarios SET nombre = 'Pedro', apellido = 'Rodríguez' WHERE id_usuario = 13;
UPDATE usuarios SET nombre = 'Ana', apellido = 'Martínez' WHERE id_usuario = 12;
UPDATE usuarios SET nombre = 'Juan', apellido = 'Pérez' WHERE id_usuario = 11;
UPDATE usuarios SET nombre = 'María', apellido = 'López' WHERE id_usuario = 10;
UPDATE usuarios SET nombre = 'Carlos', apellido = 'García' WHERE id_usuario = 9;
UPDATE usuarios SET nombre = 'Diego', apellido = 'Moreno' WHERE id_usuario = 18;

-- Verificar después:
-- SELECT id_usuario, nombre, apellido FROM usuarios ORDER BY id_usuario;
-- Si los nombres siguen sin corregirse, usar la siguiente sección (re-inserción)

-- ============================================
-- RE-INSERCIÓN COMPLETA (solo si el UPDATE no funcionó)
-- ⚠️ Antes de ejecutar: revisar las relaciones existentes
-- ============================================

-- Verificar relaciones antes de borrar:
-- SELECT * FROM tutor_materia WHERE id_tutor IN (SELECT id_tutor FROM tutores WHERE id_usuario IN (9,10,11,12,13,14,15,17,18));
-- SELECT * FROM tutores WHERE id_usuario IN (9,10,11,12,13,14,15,17,18);
-- SELECT * FROM estudiantes WHERE id_usuario IN (9,10,11,12,13,14,15,17,18);

-- Eliminar relaciones y registros (SOLO si es necesario):
-- DELETE FROM tutor_materia WHERE id_tutor IN (SELECT id_tutor FROM tutores WHERE id_usuario IN (9,10,11,12,13));
-- DELETE FROM tutores WHERE id_usuario IN (9,10,11,12,13);
-- DELETE FROM estudiantes WHERE id_usuario IN (10,14,15,17,18);
-- DELETE FROM usuarios WHERE id_usuario IN (9,10,11,12,13,14,15,17,18);

-- Re-insertar con caracteres correctos (utf8mb4):
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(9, 2, 'Carlos', 'García', 'carlos.garcia@upds.edu.bo', 'tutor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(10, 3, 'María', 'López', 'maria.lopez@upds.edu.bo', 'estudiante1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(11, 2, 'Juan', 'Pérez', 'juan.perez@upds.edu.bo', 'tutor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(12, 2, 'Ana', 'Martínez', 'ana.martinez@upds.edu.bo', 'tutor4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(13, 2, 'Pedro', 'Rodríguez', 'pedro.rodriguez@upds.edu.bo', 'tutor5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(14, 3, 'Luis', 'Fernández', 'luis.fernandez@upds.edu.bo', 'estudiante2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(15, 3, 'Sofía', 'González', 'sofia.gonzalez@upds.edu.bo', 'estudiante3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(17, 3, 'Valentina', 'Díaz', 'valentina.diaz@upds.edu.bo', 'estudiante4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
(18, 3, 'Diego', 'Moreno', 'diego.moreno@upds.edu.bo', 'estudiante5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido);
