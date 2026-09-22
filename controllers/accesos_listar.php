 <?php
require_once '/var/www/html/conexion.php';

$mensaje = $_GET['mensaje'] ?? '';

$consulta = "SELECT ra.*,
                    CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
             FROM registro_accesos ra
             LEFT JOIN usuarios u ON ra.id_usuario = u.id_usuario
             ORDER BY ra.fecha_hora DESC";
$registros = $conexion->prepare($consulta);
$registros->execute();

require_once '/var/www/html/views/accesos/listar.php';