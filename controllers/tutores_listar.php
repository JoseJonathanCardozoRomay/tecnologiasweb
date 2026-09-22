 <?php
/**
 * Listar Tutores — Solución
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT t.*, u.nombre, u.apellido 
             FROM tutores t 
             LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario 
             ORDER BY t.id_tutor";
$tutores = $conexion->prepare($consulta);
$tutores->execute();

require_once __DIR__ . '/../views/tutores/listar.php';