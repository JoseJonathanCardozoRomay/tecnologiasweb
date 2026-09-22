 <?php
/**
 * Listar Periodos de Tutoría — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

// Variable para mensajes
$mensaje = $_GET['mensaje'] ?? '';

// Consulta directa
$consulta = "SELECT * FROM periodos_tutoria ORDER BY id_periodo DESC";
$periodos = $conexion->prepare($consulta);
$periodos->execute();

require_once __DIR__ . '/../views/periodos/listar.php';