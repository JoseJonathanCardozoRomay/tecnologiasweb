 <?php
/**
 * Listar Notificaciones — Ruta corregida
 */

require_once __DIR__ . '/../conexion.php';

$mensaje = $_GET['mensaje'] ?? '';

$consulta = "SELECT n.*,
                    CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
             FROM notificaciones n
             LEFT JOIN usuarios u ON n.id_usuario = u.id_usuario
             ORDER BY n.fecha_creacion DESC";
$notificaciones = $conexion->prepare($consulta);
$notificaciones->execute();

require_once __DIR__ . '/../views/notificaciones/listar.php';