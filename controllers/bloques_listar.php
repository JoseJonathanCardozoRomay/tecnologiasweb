 <?php
/**
 * Listar Bloques Horarios — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

// Variable para mensajes
$mensaje = $_GET['mensaje'] ?? '';

// Consulta directa
$consulta = "SELECT * FROM bloques_horarios ORDER BY id_bloque DESC";
$bloques = $conexion->prepare($consulta);
$bloques->execute();

require_once __DIR__ . '/../views/bloques/listar.php';