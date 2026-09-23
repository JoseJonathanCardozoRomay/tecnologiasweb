<?php
/**
 * Controlador — Listado de Tutorías
 * Con nombres completos: Estudiante, Tutor y Materia
 */
require_once __DIR__ . '/../config/conexion.php';

$consulta = "
    SELECT 
        t.id_tutoria,
        t.fecha,
        t.hora_inicio,
        t.hora_fin,
        t.estado,
        t.modalidad,
        t.lugar_o_enlace,
        e.nombre AS nombre_estudiante,
        e.apellido AS apellido_estudiante,
        tut.nombre AS nombre_tutor,
        tut.apellido AS apellido_tutor,
        m.nombre_materia
    FROM tutorias t
    LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
    LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
    LEFT JOIN tutores tu ON t.id_tutor = tu.id_tutor
    LEFT JOIN usuarios tut ON tu.id_usuario = tut.id_usuario
    LEFT JOIN materias m ON t.id_materia = m.id_materia
    ORDER BY t.fecha DESC, t.hora_inicio DESC
";

$stmt = $conexion->prepare($consulta);
$stmt->execute();
$tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/tutorias/listar.php';