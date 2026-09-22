 <?php
/**
 * Listar Estudiantes — Campos exactos para tu vista
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT e.*, 
                    u.nombre,
                    u.apellido,
                    c.nombre_carrera
             FROM estudiantes e
             LEFT JOIN usuarios u ON e.id_usuario = u.id_usuario
             LEFT JOIN carreras c ON e.id_carrera = c.id_carrera
             ORDER BY e.id_estudiante DESC";
$estudiantes = $conexion->prepare($consulta);
$estudiantes->execute();

require_once __DIR__ . '/../views/estudiantes/listar.php';