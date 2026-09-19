-- Revise y corrija las filas listadas antes de ejecutar los ALTER TABLE.
SELECT 'tutorias' AS tabla, id_tutoria AS id, hora_inicio, hora_fin FROM tutorias WHERE hora_fin <= hora_inicio
UNION ALL
SELECT 'disponibilidad_tutor' AS tabla, id_disponibilidad AS id, hora_inicio, hora_fin FROM disponibilidad_tutor WHERE hora_fin <= hora_inicio;

ALTER TABLE tutorias
  ADD INDEX idx_tutoria_tutor_fecha (id_tutor, fecha),
  ADD INDEX idx_tutoria_estudiante_fecha (id_estudiante, fecha),
  ADD CONSTRAINT chk_tutoria_horas CHECK (hora_fin > hora_inicio);

ALTER TABLE disponibilidad_tutor
  ADD CONSTRAINT chk_disp_horas CHECK (hora_fin > hora_inicio);
