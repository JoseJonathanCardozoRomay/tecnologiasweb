 <?php
/**
 * Listar Materias — Solución
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT m.*, c.nombre_carrera 
             FROM materias m 
             LEFT JOIN carreras c ON m.id_carrera = c.id_carrera 
             ORDER BY m.id_materia";
$materias = $conexion->prepare($consulta);
$materias->execute();

require_once __DIR__ . '/../views/materias/listar.php';