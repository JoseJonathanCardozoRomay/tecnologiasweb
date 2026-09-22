 <?php
/**
 * Listar Usuarios — Solución exacta
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT u.*, r.nombre_rol 
             FROM usuarios u 
             LEFT JOIN roles r ON u.id_rol = r.id_rol 
             ORDER BY u.id_usuario";
$usuarios = $conexion->prepare($consulta);
$usuarios->execute();

require_once __DIR__ . '/../views/usuarios/listar.php';