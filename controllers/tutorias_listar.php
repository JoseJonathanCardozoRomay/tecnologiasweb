 <?php
/**
 * Listar Tutorías Programadas — Alias exactos para la vista
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$mensaje = $_GET['mensaje'] ?? '';

$consulta = "SELECT t.*,
                    CONCAT(ut.nombre, ' ', ut.apellido) AS tutor_nombre,
                    CONCAT(ue.nombre, ' ', ue.apellido) AS estudiante_nombre,
                    m.nombre_materia AS materia_nombre,
                    t.fecha,
                    CONCAT(t.hora_inicio, ' - ', t.hora_fin) AS hora,
                    t.estado
             FROM tutorias t
             LEFT JOIN tutores tr ON t.id_tutor = tr.id_tutor
             LEFT JOIN usuarios ut ON tr.id_usuario = ut.id_usuario
             LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
             LEFT JOIN usuarios ue ON e.id_usuario = ue.id_usuario
             LEFT JOIN materias m ON t.id_materia = m.id_materia
             ORDER BY t.fecha DESC, t.hora_inicio DESC";
$tutorias = $conexion->prepare($consulta);
$tutorias->execute();

require_once __DIR__ . '/../views/tutorias/listar.php';