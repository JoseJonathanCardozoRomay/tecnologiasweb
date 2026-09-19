-- Resolver estos duplicados antes de aplicar los índices únicos.
SELECT nombre_carrera, COUNT(*) AS total
FROM carreras
GROUP BY nombre_carrera
HAVING COUNT(*) > 1;

SELECT id_carrera, nombre_materia, COUNT(*) AS total
FROM materias
GROUP BY id_carrera, nombre_materia
HAVING COUNT(*) > 1;

ALTER TABLE carreras
    ADD UNIQUE KEY uq_carrera_nombre (nombre_carrera);

ALTER TABLE materias
    ADD UNIQUE KEY uq_materia_carrera (id_carrera, nombre_materia);
