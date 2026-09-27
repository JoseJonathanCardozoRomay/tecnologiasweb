-- =========================================================
-- MIGRACIÓN 012 - ACTUALIZACIÓN DE CREDENCIAL DEL ADMINISTRADOR
-- Cierre y endurecimiento de seguridad del SPRINT 7.
-- El hash se genero con PHP 8.2 nativo:
--     password_hash('@g4t.a56', PASSWORD_BCRYPT)
-- (coste por defecto 10, prefijo $2y$, 60 caracteres).
-- NUNCA se almacena la contrasena en texto plano.
-- Idempotente: puede aplicarse N veces sin efectos acumulativos.
-- =========================================================
USE tutorias_db;

-- ---------------------------------------------------------
-- 1. Credencial del usuario administrador
-- ---------------------------------------------------------
UPDATE usuarios
   SET contrasena_hash = '$2y$10$NZyKnHtEFdzBeiIrF0eFY.tKEc59NmwTKlSoF5hs5eWvZzoyJDhDu'
 WHERE usuario = 'admin'
    OR correo = 'admin@sistema-tutorias.edu.bo';

-- ---------------------------------------------------------
-- 2. Verificacion (solo lectura): el hash debe ser bcrypt
-- ---------------------------------------------------------
SELECT id_usuario,
       usuario,
       correo,
       estado,
       LEFT(contrasena_hash, 4)  AS algoritmo,
       LENGTH(contrasena_hash)    AS longitud
  FROM usuarios
 WHERE usuario = 'admin';
