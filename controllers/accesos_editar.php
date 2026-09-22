 <?php
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$id = $_GET['id'] ?? 0;
$acceso = $modelo->obtenerPorId($id);
$usuarios = $modelo->listarUsuarios();

if (!$acceso) {
    header('Location: index.php?accion=accesos_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->editar($id, $_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=accesos_editar&id=$id&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: index.php?accion=accesos_listar&mensaje=actualizado");
    exit;
}

require_once __DIR__ . '/../views/accesos/editar.php';