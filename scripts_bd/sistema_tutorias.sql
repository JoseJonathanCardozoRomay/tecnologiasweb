-- =========================================================
-- INSTALACIÓN / REINSTALACIÓN COMPLETA DESDE CERO (DROP & RECREATE)
--
-- El esquema autoritativo y los datos semilla viven en:
--     database/init.sql   (es el que monta Docker)
-- Este script solo garantiza una base limpia y aplica init.sql.
--
-- USO (desde la carpeta raíz del proyecto):
--     mysql -h localhost -u tutorias_user -p < scripts_bd/sistema_tutorias.sql
-- =========================================================

DROP DATABASE IF EXISTS tutorias_db;
CREATE DATABASE tutorias_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tutorias_db;

SOURCE database/init.sql;