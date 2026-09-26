 <?php
/**
 * Listado de Roles
 */
// ✅ AGREGA ESTAS 2 LÍNEAS NUEVAS 👇
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);

// ✅ TU CÓDIGO QUE YA FUNCIONA — SE QUEDA IGUAL
require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();
$roles = $modelo->listarTodos();
require_once __DIR__ . '/../views/roles/listar.php';