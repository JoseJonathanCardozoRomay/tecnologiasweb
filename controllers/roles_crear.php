 <?php
/**
 * Crear nuevo Rol
 * Sistema de Gestión de Tutorías
 */

require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = trim($_POST['nombre_rol'] ?? '');
    
    if (!empty($nombre_rol)) {
        $resultado = $modelo->crear($nombre_rol);
        
        if (is_array($resultado) && isset($resultado['error'])) {
            $error = $resultado['error'];
        } else {
            header("Location: index.php?accion=listar&mensaje=creado");
            exit;
        }
    } else {
        $error = "El nombre del rol es obligatorio";
    }
}

require_once __DIR__ . '/../views/roles/crear.php';