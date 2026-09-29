CREATE DATABASE IF NOT EXISTS sistema_tutorias;
USE sistema_tutorias;

CREATE TABLE IF NOT EXISTS roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_rol INT NOT NULL DEFAULT 1,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) DEFAULT '',
  correo VARCHAR(150) NOT NULL UNIQUE,
  email VARCHAR(150) DEFAULT NULL,
  usuario VARCHAR(100) DEFAULT NULL UNIQUE,
  password VARCHAR(255) DEFAULT NULL,
  contrasena_hash VARCHAR(255) DEFAULT NULL,
  rol VARCHAR(50) DEFAULT 'administrador',
  estado VARCHAR(20) DEFAULT 'activo',
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS carreras (
  id_carrera INT AUTO_INCREMENT PRIMARY KEY,
  nombre_carrera VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estudiantes (
  id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  id_carrera INT NOT NULL,
  semestre TINYINT NOT NULL,
  registro_universitario VARCHAR(30) UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutores (
  id_tutor INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  especialidad VARCHAR(150) DEFAULT NULL,
  biografia TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS materias (
  id_materia INT AUTO_INCREMENT PRIMARY KEY,
  nombre_materia VARCHAR(150) NOT NULL,
  id_carrera INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutor_materia (
  id_tutor INT NOT NULL,
  id_materia INT NOT NULL,
  PRIMARY KEY (id_tutor, id_materia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS disponibilidad_tutor (
  id_disponibilidad INT AUTO_INCREMENT PRIMARY KEY,
  id_tutor INT NOT NULL,
  dia_semana ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado') NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutorias (
  id_tutoria INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT DEFAULT NULL,
  id_tutor INT DEFAULT NULL,
  id_materia INT DEFAULT NULL,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  modalidad ENUM('presencial','virtual') NOT NULL DEFAULT 'presencial',
  lugar_o_enlace VARCHAR(200) DEFAULT NULL,
  estado ENUM('pendiente','confirmada','realizada','cancelada') NOT NULL DEFAULT 'pendiente',
  observaciones TEXT DEFAULT NULL,
  fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS evaluaciones_tutoria (
  id_evaluacion INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL UNIQUE,
  calificacion TINYINT NOT NULL,
  comentario TEXT DEFAULT NULL,
  fecha_evaluacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registro_accesos (
  id_acceso INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT DEFAULT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45) DEFAULT NULL,
  resultado ENUM('exitoso','fallido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos semilla
INSERT INTO roles (id_rol, nombre_rol) VALUES (1, 'administrador'), (2, 'tutor'), (3, 'estudiante')
ON DUPLICATE KEY UPDATE nombre_rol=nombre_rol;

INSERT INTO carreras (id_carrera, nombre_carrera) VALUES (1, 'Ingeniería de Sistemas')
ON DUPLICATE KEY UPDATE nombre_carrera=nombre_carrera;

INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(1, 'Base de Datos I', 1),
(2, 'Programación I', 1),
(3, 'Tecnología Web I', 1)
ON DUPLICATE KEY UPDATE nombre_materia=nombre_materia;

-- Admin (admin / admin)
INSERT INTO usuarios (id, id_rol, nombre, apellido, correo, email, usuario, password, contrasena_hash, rol, estado)
VALUES (1, 1, 'Admin', 'Sistema', 'admin@upds.edu.bo', 'admin@upds.edu.bo', 'admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- Tutor (tutor1 / password)
INSERT INTO usuarios (id, id_rol, nombre, apellido, correo, email, usuario, password, contrasena_hash, rol, estado)
VALUES (2, 2, 'Carlos', 'Docente', 'tutor@tutorias.local', 'tutor@tutorias.local', 'tutor1', 'password', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor', 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia) VALUES
(1, 2, 'Desarrollo Web y Bases de Datos', 'Docente tutor especializado en desarrollo backend y arquitecturas web.')
ON DUPLICATE KEY UPDATE especialidad=especialidad;

INSERT INTO tutor_materia (id_tutor, id_materia) VALUES (1, 1), (1, 3)
ON DUPLICATE KEY UPDATE id_tutor=id_tutor;

INSERT INTO disponibilidad_tutor (id_disponibilidad, id_tutor, dia_semana, hora_inicio, hora_fin) VALUES
(1, 1, 'Lunes', '14:00:00', '18:00:00'),
(2, 1, 'Miercoles', '14:00:00', '18:00:00'),
(3, 1, 'Viernes', '09:00:00', '12:00:00')
ON DUPLICATE KEY UPDATE id_tutor=id_tutor;

-- Estudiante (estudiante1 / password)
INSERT INTO usuarios (id, id_rol, nombre, apellido, correo, email, usuario, password, contrasena_hash, rol, estado)
VALUES (3, 3, 'Maria', 'Estudiante', 'estudiante@tutorias.local', 'estudiante@tutorias.local', 'estudiante1', 'password', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, registro_universitario) VALUES
(1, 3, 1, 4, 'RU-2026-98765')
ON DUPLICATE KEY UPDATE id_usuario=id_usuario;
