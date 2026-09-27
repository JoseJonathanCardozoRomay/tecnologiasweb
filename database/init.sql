-- =========================================================
-- SISTEMA WEB DE APOYO ACADÉMICO PARA TUTORÍAS
-- Script de creación de base de datos y tablas (MySQL 8.0 / MariaDB)
-- =========================================================

CREATE DATABASE IF NOT EXISTS tutorias_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tutorias_db;

-- ---------------------------------------------------------
-- Roles del sistema
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(30) NOT NULL UNIQUE  -- administrador, tutor, estudiante
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Usuarios (tabla base para el login)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  id_rol INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasena_hash VARCHAR(255) NOT NULL,     -- Generado con password_hash() en PHP
  telefono VARCHAR(20),
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Carreras (para clasificar estudiantes y materias)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS carreras (
  id_carrera INT AUTO_INCREMENT PRIMARY KEY,
  nombre_carrera VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Estudiantes (extiende usuarios)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS estudiantes (
  id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  id_carrera INT NOT NULL,
  semestre TINYINT NOT NULL,
  materias_completadas INT NOT NULL DEFAULT 0,
  acceso_mg_desbloqueado TINYINT(1) NOT NULL DEFAULT 0,
  mg_desbloqueado_por INT NULL,
  mg_desbloqueado_fecha DATETIME NULL,
  registro_universitario VARCHAR(30) UNIQUE,
  CONSTRAINT fk_estudiantes_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_estudiantes_carreras FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tutores (extiende usuarios)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutores (
  id_tutor INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL UNIQUE,
  especialidad VARCHAR(150),
  biografia TEXT,
  CONSTRAINT fk_tutores_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Materias que pueden ser tutoradas
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS materias (
  id_materia INT AUTO_INCREMENT PRIMARY KEY,
  nombre_materia VARCHAR(150) NOT NULL,
  id_carrera INT,
  CONSTRAINT fk_materias_carreras FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Relación N:M: materias que domina cada tutor
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutor_materia (
  id_tutor INT NOT NULL,
  id_materia INT NOT NULL,
  PRIMARY KEY (id_tutor, id_materia),
  CONSTRAINT fk_tm_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  CONSTRAINT fk_tm_materia FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Disponibilidad horaria de cada tutor
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS disponibilidad_tutor (
  id_disponibilidad INT AUTO_INCREMENT PRIMARY KEY,
  id_tutor INT NOT NULL,
  dia_semana ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado') NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  CONSTRAINT fk_disp_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Modalidades de graduación (entes oficiales)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS modalidades_graduacion (
  id_modalidad INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  descripcion TEXT,
  minimo_reuniones_semana TINYINT NOT NULL DEFAULT 1,
  cantidad_informes TINYINT NOT NULL DEFAULT 3,
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Estados de cierre de un expediente de Modalidad de Grado (catálogo 3FN)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS estados_conclusion_mg (
  id_estado_conclusion INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tutorías (sesiones agendadas)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutorias (
  id_tutoria INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT NOT NULL,
  id_tutor INT NOT NULL,
  id_materia INT NOT NULL,
  id_modalidad INT NULL,
  id_estado_conclusion INT NULL,
  tipo ENUM('apoyo','grado') NOT NULL DEFAULT 'apoyo',
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  modalidad ENUM('presencial','virtual') NOT NULL DEFAULT 'presencial',
  lugar_o_enlace VARCHAR(200),
  estado ENUM('pendiente','confirmada','asignada','aceptada','en_proceso','en_reasignacion','realizada','cancelada','finalizada') NOT NULL DEFAULT 'pendiente',
  observaciones TEXT,
  fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tutorias_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_materia FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_modalidad FOREIGN KEY (id_modalidad) REFERENCES modalidades_graduacion(id_modalidad) ON UPDATE CASCADE,
  CONSTRAINT fk_tutorias_estado_conclusion FOREIGN KEY (id_estado_conclusion) REFERENCES estados_conclusion_mg(id_estado_conclusion) ON UPDATE CASCADE,
  INDEX idx_tutoria_fecha (fecha),
  INDEX idx_tutoria_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Evaluación/retroalimentación de la tutoría (insumo para reportes)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS evaluaciones_tutoria (
  id_evaluacion INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL UNIQUE,
  calificacion TINYINT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
  comentario TEXT,
  fecha_evaluacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_evaluaciones_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Registro de accesos (auditoría de login)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS registro_accesos (
  id_acceso INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45),
  resultado ENUM('exitoso','fallido') NOT NULL,
  CONSTRAINT fk_accesos_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Cartas de designación (flujo coordinación -> tutor)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cartas_designacion (
  id_carta INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  id_tutor INT NOT NULL,
  id_estudiante INT NOT NULL,
  fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_firma DATETIME NULL,
  estado ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  motivo_rechazo TEXT NULL,
  CONSTRAINT fk_carta_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_carta_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON UPDATE CASCADE,
  CONSTRAINT fk_carta_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Reuniones (sesiones con asistencia y evidencia)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS reuniones (
  id_reunion INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  lugar_o_enlace VARCHAR(200),
  asistio_estudiante ENUM('si','no','tardanza') NOT NULL DEFAULT 'si',
  evidencia_url VARCHAR(255),
  observaciones TEXT,
  CONSTRAINT fk_reunion_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Informes de avance (evidencia de progreso 0-100%)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS informes_avance (
  id_informe INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  numero_informe TINYINT NOT NULL,
  fecha_limite DATE NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  porcentaje_avance TINYINT NOT NULL DEFAULT 0 CHECK (porcentaje_avance BETWEEN 0 AND 100),
  descripcion_avance TEXT,
  CONSTRAINT fk_informe_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  UNIQUE KEY uq_informe_tutoria (id_tutoria, numero_informe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tribunales (2 docentes por tutoría; nunca el tutor asignado)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tribunales (
  id_tribunal INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_tutoria INT NOT NULL,
  CONSTRAINT fk_tribunal_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_tribunal_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  UNIQUE KEY uq_tribunal_tutoria (id_usuario, id_tutoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Historial y auditoría (logins, logouts y eventos críticos)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS historial_auditoria (
  id_historial INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NULL,
  tipo_evento VARCHAR(60) NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45),
  CONSTRAINT fk_historial_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Configuración del sistema (parámetros de negocio)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS configuracion_sistema (
  clave VARCHAR(50) PRIMARY KEY,
  valor VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Estudiante en tutoría (varios estudiantes por tutoría de apoyo)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS tutoria_estudiantes (
  id_tutoria INT NOT NULL,
  id_estudiante INT NOT NULL,
  fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tutoria, id_estudiante),
  CONSTRAINT fk_te_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_te_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Comprobantes de pago para Modalidad de Grado
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS comprobantes_pago_mg (
  id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante INT NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  fecha_pago DATE NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  estado ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  motivo_rechazo TEXT NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  validado_por INT NULL,
  fecha_validacion DATETIME NULL,
  CONSTRAINT fk_comprobante_estudiante FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE,
  CONSTRAINT fk_comprobante_operador FOREIGN KEY (validado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_comprobante_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Carreras de los docentes (filtro estricto por carrera)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS docente_carreras (
  id_docente_carrera INT AUTO_INCREMENT PRIMARY KEY,
  id_tutor INT NOT NULL,
  id_carrera INT NOT NULL,
  UNIQUE KEY uq_docente_carrera (id_tutor, id_carrera),
  CONSTRAINT fk_dc_tutor FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
  CONSTRAINT fk_dc_carrera FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Bitácora de auditoría de usuarios (admin / auxiliar)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS bitacora_auditoria_usuarios (
  id_bitacora INT AUTO_INCREMENT PRIMARY KEY,
  id_operador INT NOT NULL,
  id_afectado INT NOT NULL,
  accion ENUM('crear','editar','eliminar') NOT NULL,
  detalles TEXT NULL,
  fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_origen VARCHAR(45),
  CONSTRAINT fk_bitacora_operador FOREIGN KEY (id_operador) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_bitacora_afectado FOREIGN KEY (id_afectado) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  INDEX idx_bitacora_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Notificaciones (campana del header; tiempo real vía polling)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificaciones (
  id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
  id_destinatario INT NOT NULL,
  id_origen INT NULL,
  tipo VARCHAR(60) NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  enlace VARCHAR(255) NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_destinatario FOREIGN KEY (id_destinatario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_notif_origen FOREIGN KEY (id_origen) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
  INDEX idx_notif_destinatario (id_destinatario, leida),
  INDEX idx_notif_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Documentos enviados en el expediente de la tutoría
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS documentos_expediente (
  id_documento INT AUTO_INCREMENT PRIMARY KEY,
  id_tutoria INT NOT NULL,
  id_origen INT NOT NULL,
  id_destinatario INT NOT NULL,
  nombre_original VARCHAR(255) NOT NULL,
  ruta_archivo VARCHAR(255) NOT NULL,
  descripcion VARCHAR(500) NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_doc_exp_tutoria FOREIGN KEY (id_tutoria) REFERENCES tutorias(id_tutoria) ON DELETE CASCADE,
  CONSTRAINT fk_doc_exp_origen FOREIGN KEY (id_origen) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_doc_exp_destinatario FOREIGN KEY (id_destinatario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  INDEX idx_doc_exp_tutoria (id_tutoria),
  INDEX idx_doc_exp_destinatario (id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- Datos semilla iniciales
-- =========================================================

-- Roles del sistema
INSERT INTO roles (id_rol, nombre_rol) VALUES 
(1, 'administrador'), 
(2, 'tutor'), 
(3, 'estudiante'),
(4, 'auxiliar')
ON DUPLICATE KEY UPDATE nombre_rol = VALUES(nombre_rol);

-- Carreras
INSERT INTO carreras (id_carrera, nombre_carrera) VALUES 
(1, 'Ingeniería de Sistemas')
ON DUPLICATE KEY UPDATE nombre_carrera = VALUES(nombre_carrera);

-- Materias
INSERT INTO materias (id_materia, nombre_materia, id_carrera) VALUES
(1, 'Base de Datos I', 1),
(2, 'Programación I', 1),
(3, 'Tecnología Web I', 1)
ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia);

-- Usuarios iniciales con contraseña: password (hash bcrypt real)
-- 1. Administrador (admin / password)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) VALUES
(1, 1, 'Admin', 'Sistema', 'admin@tutorias.local', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '70000001', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

-- 2. Tutor de prueba (tutor1 / password)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) VALUES
(2, 2, 'Carlos', 'Docente', 'tutor@tutorias.local', 'tutor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '70000002', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

INSERT INTO tutores (id_tutor, id_usuario, especialidad, biografia) VALUES
(1, 2, 'Desarrollo Web y Bases de Datos', 'Docente tutor especializado en desarrollo backend y arquitecturas web.')
ON DUPLICATE KEY UPDATE especialidad = VALUES(especialidad);

-- Materias asignadas al tutor
INSERT INTO tutor_materia (id_tutor, id_materia) VALUES
(1, 1),
(1, 3)
ON DUPLICATE KEY UPDATE id_tutor = VALUES(id_tutor);

-- Disponibilidad horaria del tutor
INSERT INTO disponibilidad_tutor (id_disponibilidad, id_tutor, dia_semana, hora_inicio, hora_fin) VALUES
(1, 1, 'Lunes', '14:00:00', '18:00:00'),
(2, 1, 'Miercoles', '14:00:00', '18:00:00'),
(3, 1, 'Viernes', '09:00:00', '12:00:00')
ON DUPLICATE KEY UPDATE dia_semana = VALUES(dia_semana);

-- 3. Estudiante de prueba (estudiante1 / password)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) VALUES
(3, 3, 'Maria', 'Estudiante', 'estudiante@tutorias.local', 'estudiante1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '70000003', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

INSERT INTO estudiantes (id_estudiante, id_usuario, id_carrera, semestre, materias_completadas, registro_universitario) VALUES
(1, 3, 1, 9, 54, 'RU-2026-98765')
ON DUPLICATE KEY UPDATE semestre = VALUES(semestre);

-- Modalidades de graduación (entes oficiales)
INSERT INTO modalidades_graduacion (id_modalidad, nombre, descripcion, minimo_reuniones_semana, cantidad_informes, activa) VALUES
(1, 'Proyecto', 'Trabajo práctico orientado a construir un producto software. Se exige 1 reunión semanal y 3 informes de avance.', 1, 3, 1),
(2, 'Tesis', 'Investigación formal con sustento teórico y metodológico. Se exige 2 reuniones semanales y 4 informes de avance.', 2, 4, 1),
(3, 'Trabajo Dirigido', 'Solución a un problema real de la institución o empresa. Se exige 1 reunión semanal y 4 informes de avance.', 1, 4, 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Estados de cierre del expediente de Modalidad de Grado
INSERT INTO estados_conclusion_mg (id_estado_conclusion, nombre, descripcion) VALUES
(1, 'Aprobada', 'El estudiante aprobó la defensa y concluyó la Modalidad de Grado.'),
(2, 'Reprobada', 'El estudiante no aprobó la defensa de la Modalidad de Grado.'),
(3, 'Abandono', 'El estudiante abandonó la Modalidad de Grado sin concluir.'),
(4, 'Otros', 'Otro resultado de cierre del expediente de grado.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- Configuración del sistema (parámetros de negocio)
INSERT INTO configuracion_sistema (clave, valor, descripcion) VALUES
('cupo_minimo_apoyo', '3', 'Número mínimo de estudiantes por tutoría de apoyo para habilitarla.'),
('cupo_maximo_mg', '5', 'Límite de estudiantes por tutor en Modalidad de Grado.'),
('materias_requeridas_mg', '54', 'Materias culminadas requeridas para acceder a Modalidad de Grado.'),
('semestres_requeridos_mg', '9', 'Semestres requeridos para acceder a Modalidad de Grado.')
ON DUPLICATE KEY UPDATE valor = VALUES(valor), descripcion = VALUES(descripcion);

-- 4. Auxiliar de prueba (auxiliar / password)
INSERT INTO usuarios (id_usuario, id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) VALUES
(14, 4, 'Auxiliar', 'Coordinación', 'auxiliar@tutorias.local', 'auxiliar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '70000014', 'activo')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

-- Carreras de los docentes (filtro estricto por carrera)
INSERT INTO docente_carreras (id_tutor, id_carrera) VALUES
(1, 1)
ON DUPLICATE KEY UPDATE id_carrera = VALUES(id_carrera);
