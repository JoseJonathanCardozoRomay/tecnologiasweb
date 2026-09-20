CREATE DATABASE IF NOT EXISTS tutorias_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tutorias_db;

CREATE TABLE IF NOT EXISTS roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  id_rol INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasena_hash VARCHAR(255) NOT NULL,
  telefono VARCHAR(20) NULL,
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON UPDATE CASCADE,
  INDEX idx_usuarios_rol_estado (id_rol,estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS carreras (
  id_carrera INT AUTO_INCREMENT PRIMARY KEY,
  nombre_carrera VARCHAR(150) NOT NULL UNIQUE,
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  INDEX idx_carreras_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estudiantes (
  id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  id_carrera INT NOT NULL,
  semestre TINYINT NOT NULL,
  registro_universitario VARCHAR(30) UNIQUE,
  CONSTRAINT chk_estudiante_semestre CHECK (semestre BETWEEN 1 AND 12),
  CONSTRAINT fk_estudiantes_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_estudiantes_carreras FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutores (
  id_tutor INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  especialidad VARCHAR(150),
  biografia TEXT,
  CONSTRAINT fk_tutores_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS materias (
  id_materia INT AUTO_INCREMENT PRIMARY KEY,
  nombre_materia VARCHAR(150) NOT NULL UNIQUE,
  id_carrera INT NOT NULL,
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  CONSTRAINT fk_materias_carreras FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON UPDATE CASCADE,
  INDEX idx_materias_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutor_materia (
  id_tutor INT NOT NULL,
  id_materia INT NOT NULL,
  PRIMARY KEY (id_tutor,id_materia),
  CONSTRAINT fk_tm_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  CONSTRAINT fk_tm_materia FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS disponibilidad_tutor (
  id_disponibilidad INT AUTO_INCREMENT PRIMARY KEY,
  id_tutor INT NOT NULL,
  dia_semana ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado') NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  CONSTRAINT chk_disponibilidad_horas CHECK (hora_inicio < hora_fin),
  CONSTRAINT fk_disp_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  UNIQUE KEY uq_disponibilidad_exacta (id_tutor,dia_semana,hora_inicio,hora_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tutorias (
  id_tutoria INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT NOT NULL,
  id_tutor INT NOT NULL,
  id_materia INT NOT NULL,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  modalidad ENUM('presencial','virtual') NOT NULL DEFAULT 'presencial',
  lugar_o_enlace VARCHAR(200) NULL,
  estado ENUM('pendiente','confirmada','rechazada','realizada','cancelada') NOT NULL DEFAULT 'pendiente',
  observaciones TEXT NULL,
  motivo_cancelacion VARCHAR(500) NULL,
  cancelado_por_usuario INT NULL,
  fecha_cancelacion DATETIME NULL,
  fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_tutoria_horas CHECK (hora_inicio < hora_fin),
  CONSTRAINT fk_tutorias_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_materia FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_cancelado_por FOREIGN KEY (cancelado_por_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX idx_tutoria_tutor_fecha (id_tutor,fecha,hora_inicio),
  INDEX idx_tutoria_estudiante_fecha (id_estudiante,fecha,hora_inicio),
  INDEX idx_tutoria_estado (estado),
  INDEX idx_tutorias_fecha_cancelacion (fecha_cancelacion),
  INDEX idx_tutorias_cancelado_por (cancelado_por_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS evaluaciones_tutoria (
  id_evaluacion INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL UNIQUE,
  calificacion TINYINT NOT NULL,
  comentario TEXT NULL,
  fecha_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_evaluacion_calificacion CHECK (calificacion BETWEEN 1 AND 5),
  CONSTRAINT fk_evaluaciones_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registro_accesos (
  id_acceso INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NULL,
  fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45) NULL,
  resultado ENUM('exitoso','fallido') NOT NULL,
  accion VARCHAR(40) NULL,
  modulo VARCHAR(80) NULL,
  descripcion VARCHAR(255) NULL,
  CONSTRAINT fk_accesos_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_auditoria_fecha (fecha_hora),
  INDEX idx_auditoria_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (id_rol,nombre_rol) VALUES (1,'administrador'),(2,'tutor'),(3,'estudiante') ON DUPLICATE KEY UPDATE nombre_rol=VALUES(nombre_rol);
INSERT INTO carreras (id_carrera,nombre_carrera) VALUES (1,'Ingeniería de Sistemas') ON DUPLICATE KEY UPDATE nombre_carrera=VALUES(nombre_carrera);
INSERT INTO materias (id_materia,nombre_materia,id_carrera) VALUES (1,'Base de Datos I',1),(2,'Programación I',1),(3,'Tecnología Web I',1) ON DUPLICATE KEY UPDATE nombre_materia=VALUES(nombre_materia),id_carrera=VALUES(id_carrera);
INSERT INTO usuarios (id_usuario,id_rol,nombre,apellido,correo,usuario,contrasena_hash,telefono,estado) VALUES (1,1,'Admin','Sistema','admin@tutorias.local','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000001','activo') ON DUPLICATE KEY UPDATE id_rol=VALUES(id_rol),estado='activo';
INSERT INTO usuarios (id_usuario,id_rol,nombre,apellido,correo,usuario,contrasena_hash,telefono,estado) VALUES (2,2,'Carlos','Docente','tutor@tutorias.local','tutor1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000002','activo') ON DUPLICATE KEY UPDATE id_rol=VALUES(id_rol),estado='activo';
INSERT INTO usuarios (id_usuario,id_rol,nombre,apellido,correo,usuario,contrasena_hash,telefono,estado) VALUES (3,3,'Maria','Estudiante','estudiante@tutorias.local','estudiante1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','70000003','activo') ON DUPLICATE KEY UPDATE id_rol=VALUES(id_rol),estado='activo';
INSERT INTO tutores (id_tutor,id_usuario,especialidad,biografia) VALUES (1,2,'Desarrollo Web y Bases de Datos','Docente tutor especializado en desarrollo backend y arquitecturas web.') ON DUPLICATE KEY UPDATE especialidad=VALUES(especialidad),biografia=VALUES(biografia);
INSERT INTO tutor_materia (id_tutor,id_materia) VALUES (1,1),(1,3) ON DUPLICATE KEY UPDATE id_tutor=VALUES(id_tutor);
INSERT INTO disponibilidad_tutor (id_disponibilidad,id_tutor,dia_semana,hora_inicio,hora_fin) VALUES (1,1,'Lunes','14:00:00','18:00:00'),(2,1,'Miercoles','14:00:00','18:00:00'),(3,1,'Viernes','09:00:00','12:00:00') ON DUPLICATE KEY UPDATE hora_inicio=VALUES(hora_inicio),hora_fin=VALUES(hora_fin);
INSERT INTO estudiantes (id_estudiante,id_usuario,id_carrera,semestre,registro_universitario) VALUES (1,3,1,4,'RU-2026-98765') ON DUPLICATE KEY UPDATE id_carrera=VALUES(id_carrera),semestre=VALUES(semestre),registro_universitario=VALUES(registro_universitario);
