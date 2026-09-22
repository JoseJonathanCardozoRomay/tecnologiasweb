 <?php
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$id = $_GET['id'] ?? 0;
$resultado = $modelo->eliminar($id);

if (is_array($resultado) && isset($resultado['error'])) {
    header("Location: index.php?accion=accesos_listar&mensaje=error&detalle=" . urlencode($resultado['error']));
    exit;
}

header("Location: index.php?accion=accesos_listar&mensaje=eliminado");
exit;