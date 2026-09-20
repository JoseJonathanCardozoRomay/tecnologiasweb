USE tutorias_db;

-- Ejecutar con una copia de seguridad si la base ya contiene información propia.
-- Estas restricciones refuerzan las reglas de unicidad y auditoría de la versión 2.
ALTER TABLE carreras ADD CONSTRAINT uq_carreras_nombre UNIQUE (nombre_carrera);
ALTER TABLE materias ADD CONSTRAINT uq_materias_nombre UNIQUE (nombre_materia);
ALTER TABLE disponibilidad_tutor ADD CONSTRAINT uq_disponibilidad_exacta UNIQUE (id_tutor,dia_semana,hora_inicio,hora_fin);
ALTER TABLE tutorias MODIFY estado ENUM('pendiente','confirmada','rechazada','realizada','cancelada') NOT NULL DEFAULT 'pendiente';
ALTER TABLE registro_accesos ADD COLUMN accion VARCHAR(40) NULL AFTER resultado;
ALTER TABLE registro_accesos ADD COLUMN modulo VARCHAR(80) NULL AFTER accion;
ALTER TABLE registro_accesos ADD COLUMN descripcion VARCHAR(255) NULL AFTER modulo;
ALTER TABLE registro_accesos ADD INDEX idx_auditoria_fecha (fecha_hora), ADD INDEX idx_auditoria_usuario (id_usuario);
ALTER TABLE tutorias ADD INDEX idx_tutoria_tutor_fecha (id_tutor,fecha,hora_inicio), ADD INDEX idx_tutoria_estudiante_fecha (id_estudiante,fecha,hora_inicio);
