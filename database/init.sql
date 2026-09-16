-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS tutorias_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tutorias_db;

-- 1. Tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de registro de accesos (auditoría de login)
CREATE TABLE IF NOT EXISTS registro_accesos (
    id_acceso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    resultado ENUM('exitoso', 'fallido') NOT NULL,
    fecha_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_accesos_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar roles iniciales
INSERT INTO roles (id_rol, nombre_rol) VALUES
(1, 'administrador'),
(2, 'tutor'),
(3, 'estudiante')
ON DUPLICATE KEY UPDATE nombre_rol = VALUES(nombre_rol);

-- Insertar usuario administrador por defecto
-- Usuario: admin
-- Contraseña: password
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES
(1, 1, 'Administrador', 'Principal', 'admin@tutorias.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);
