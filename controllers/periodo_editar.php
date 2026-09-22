 <?php
/**
 * Editar Periodo de Tutoría — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Obtener datos del periodo
$consulta = "SELECT * FROM periodos_tutoria WHERE id_periodo = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$id]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $activo = $_POST['activo'] ?? 0;

    if ($codigo && $nombre && $fecha_inicio && $fecha_fin) {
        $actualizar = "UPDATE periodos_tutoria 
                      SET codigo = ?, nombre = ?, fecha_inicio = ?, fecha_fin = ?, activo = ?
                      WHERE id_periodo = ?";
        $stmt_act = $conexion->prepare($actualizar);
        $stmt_act->execute([$codigo, $nombre, $fecha_inicio, $fecha_fin, $activo, $id]);
        
        header("Location: index.php?accion=periodos_listar&mensaje=actualizado");
        exit;
    } else {
        $error = "Todos los campos son obligatorios";
    }
}

require_once __DIR__ . '/../views/periodos/editar.php';