-- =========================================================
-- MIGRACIÓN 007: Corrección de campo foto_perfil para rutas de archivos
-- Sistema de Tutorías UPDS
-- ---------------------------------------------------------
-- Problema: El campo foto_perfil VARCHAR(255) no alcanza para data URIs
-- Solución: Aumentar a VARCHAR(500) para almacenar rutas relativas de archivos
-- =========================================================

-- Limpiar datos corruptos existentes (data URIs inválidos y NULL)
UPDATE tutores SET foto_perfil = '' WHERE foto_perfil LIKE 'data:image%' OR foto_perfil IS NULL;

-- Aumentar el tamaño del campo para almacenar rutas de archivos
ALTER TABLE tutores 
    MODIFY COLUMN foto_perfil VARCHAR(500) NOT NULL DEFAULT '';

-- Nota: Después de esta migración, el sistema debe usar archivos físicos
-- en assets/img/tutores/ y guardar solo la ruta relativa en la BD
-- =========================================================
