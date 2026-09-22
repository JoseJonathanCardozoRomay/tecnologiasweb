 <?php
/**
 * Listar Carreras — Solución
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$consulta = "SELECT * FROM carreras ORDER BY id_carrera";
$carreras = $conexion->prepare($consulta);
$carreras->execute();

require_once __DIR__ . '/../views/carreras/listar.php';