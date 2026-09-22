 <?php
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$usuarios = $modelo->listarUsuarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->crear($_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=accesos_crear&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: index.php?accion=accesos_listar&mensaje=creado");
    exit;
}

require_once __DIR__ . '/../views/accesos/crear.php';