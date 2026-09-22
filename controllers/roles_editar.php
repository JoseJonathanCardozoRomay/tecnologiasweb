 <?php
/**
 * Editar Rol — Corregido
 * Sistema de Gestión de Tutorías
 */

require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();

// Obtener el ID desde la URL
$id_rol = intval($_GET['id'] ?? 0);

if ($id_rol <= 0) {
    header("Location: index.php?accion=listar");
    exit;
}

// Obtener los datos del rol
$rol = $modelo->obtenerPorId($id_rol);

if (!$rol) {
    header("Location: index.php?accion=listar");
    exit;
}

$error = '';

// Procesar el formulario al guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = trim($_POST['nombre_rol'] ?? '');
    
    if (!empty($nombre_rol)) {
        $resultado = $modelo->actualizar($id_rol, $nombre_rol);
        
        if (is_array($resultado) && isset($resultado['error'])) {
            $error = $resultado['error'];
        } else {
            header("Location: index.php?accion=listar&mensaje=actualizado");
            exit;
        }
    } else {
        $error = "El nombre del rol es obligatorio";
    }
}

// Cargar la vista de edición
require_once __DIR__ . '/../views/roles/editar.php';