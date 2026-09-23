<?php
/**
 * Controlador — Listado de Disponibilidad Horaria
 * Con nombres completos del Tutor
 */
require_once __DIR__ . '/../config/conexion.php';

$consulta = "
    SELECT 
        d.id_disponibilidad,
        d.dia_semana,
        d.hora_inicio,
        d.hora_fin,
        u.nombre AS nombre_tutor,
        u.apellido AS apellido_tutor
    FROM disponibilidad_tutor d
    LEFT JOIN tutores t ON d.id_tutor = t.id_tutor
    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
    ORDER BY d.dia_semana, d.hora_inicio
";

$stmt = $conexion->prepare($consulta);
$stmt->execute();
$disponibilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/disponibilidad/listar.php';