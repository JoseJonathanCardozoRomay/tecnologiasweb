 <?php
/**
 * Eliminar Rol
 * Sistema de Gestión de Tutorías
 */

require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();

$id_rol = intval($_GET['id'] ?? 0);

if ($id_rol > 0) {
    $modelo->eliminar($id_rol);
}

header("Location: index.php?accion=listar&mensaje=eliminado");
exit;