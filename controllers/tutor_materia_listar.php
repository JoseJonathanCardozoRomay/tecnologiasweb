 <?php
/**
 * Listar Asignaciones Tutor-Materia — Columnas exactas
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT 
                    CONCAT(tm.id_tutor, '-', tm.id_materia) AS id_asignacion,
                    CONCAT(u.nombre, ' ', u.apellido) AS tutor_nombre,
                    m.nombre_materia AS materia_nombre,
                    tm.id_tutor,
                    tm.id_materia
             FROM tutor_materia tm
             LEFT JOIN tutores t ON tm.id_tutor = t.id_tutor
             LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
             LEFT JOIN materias m ON tm.id_materia = m.id_materia
             ORDER BY tm.id_tutor, tm.id_materia";
$asignaciones = $conexion->prepare($consulta);
$asignaciones->execute();

require_once __DIR__ . '/../views/tutor_materia/listar.php';