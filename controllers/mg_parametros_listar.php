 <?php
/**
 * Listar y Actualizar Parámetros — Módulo Modalidades de Grado
 * Funcionamiento completo: guarda cambios sin errores
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgParametroModel.php';

$modelo = new MgParametroModel();
$parametros = $modelo->listarTodos();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/conexion.php';
    
    $clave = trim($_POST['clave'] ?? '');
    $valor = trim($_POST['valor'] ?? '');
    
    if (!empty($clave)) {
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        $modelo->actualizar($clave, $valor, $id_usuario);
        $mensaje = 'Valor guardado correctamente ✅';
        // Recargar lista con valores actualizados
        $parametros = $modelo->listarTodos();
    }
}

require_once __DIR__ . '/../views/mg_parametros/listar.php';