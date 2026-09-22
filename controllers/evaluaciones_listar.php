 <?php
/**
 * Listar Evaluaciones de Tutorías — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

// Variable para mensajes
$mensaje = $_GET['mensaje'] ?? '';

// Consulta con todos los campos que necesita la vista
$consulta = "SELECT ev.*,
                    CONCAT(ue.nombre, ' ', ue.apellido) AS estudiante,
                    m.nombre_materia AS materia,
                    ev.calificacion,
                    ev.fecha_evaluacion AS fecha
             FROM evaluaciones_tutoria ev
             LEFT JOIN tutorias t ON ev.id_tutoria = t.id_tutoria
             LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
             LEFT JOIN usuarios ue ON e.id_usuario = ue.id_usuario
             LEFT JOIN materias m ON t.id_materia = m.id_materia
             ORDER BY ev.fecha_evaluacion DESC";
$evaluaciones = $conexion->prepare($consulta);
$evaluaciones->execute();

require_once __DIR__ . '/../views/evaluaciones/listar.php';