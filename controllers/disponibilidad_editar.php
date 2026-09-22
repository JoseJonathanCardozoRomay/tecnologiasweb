 <?php
/**
 * Editar Disponibilidad — Solución completa
 * Sin tocar modelo ni vistas
 */

require_once __DIR__ . '/../config/conexion.php';

$error = '';
$id = $_GET['id'] ?? 0;

// Obtener datos de la disponibilidad
$consulta = "SELECT d.*, t.id_usuario
             FROM disponibilidad_tutor d
             LEFT JOIN tutores t ON d.id_tutor = t.id_tutor
             WHERE d.id_disponibilidad = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$id]);
$disponibilidad = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener lista de tutores para el selector
$consulta_tutores = "SELECT t.id_tutor, CONCAT(u.nombre, ' ', u.apellido) AS nombre_tutor
                     FROM tutores t
                     LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                     ORDER BY u.nombre, u.apellido";
$stmt_tutores = $conexion->prepare($consulta_tutores);
$stmt_tutores->execute();
$tutores = $stmt_tutores->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tutor = $_POST['id_tutor'] ?? 0;
    $dia_semana = $_POST['dia_semana'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';

    if ($id_tutor && $dia_semana && $hora_inicio && $hora_fin) {
        $actualizar = "UPDATE disponibilidad_tutor 
                      SET id_tutor = ?, dia_semana = ?, hora_inicio = ?, hora_fin = ?
                      WHERE id_disponibilidad = ?";
        $stmt_act = $conexion->prepare($actualizar);
        $stmt_act->execute([$id_tutor, $dia_semana, $hora_inicio, $hora_fin, $id]);
        
        header("Location: index.php?accion=disponibilidad_listar&mensaje=actualizado");
        exit;
    } else {
        $error = "Todos los campos son obligatorios";
    }
}

require_once __DIR__ . '/../views/disponibilidad/editar.php';