 <?php
/**
 * Editar Bloque Horario — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Obtener datos del bloque
$consulta = "SELECT * FROM bloques_horarios WHERE id_bloque = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$id]);
$bloque = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_bloque = $_POST['nombre_bloque'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if ($nombre_bloque && $hora_inicio && $hora_fin) {
        $actualizar = "UPDATE bloques_horarios 
                      SET nombre_bloque = ?, hora_inicio = ?, hora_fin = ?, descripcion = ?
                      WHERE id_bloque = ?";
        $stmt_act = $conexion->prepare($actualizar);
        $stmt_act->execute([$nombre_bloque, $hora_inicio, $hora_fin, $descripcion, $id]);
        
        header("Location: index.php?accion=bloques_listar&mensaje=actualizado");
        exit;
    } else {
        $error = "Todos los campos obligatorios deben completarse";
    }
}

require_once __DIR__ . '/../views/bloques/editar.php';