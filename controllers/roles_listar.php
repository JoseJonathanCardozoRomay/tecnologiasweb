 <?php
/**
 * Controlador para la visualización del listado de roles
 * Sistema de Gestión de Tutorías
 */

require_once __DIR__ . '/../models/RolModel.php';

$modelo = new RolModel();
$roles = $modelo->listarTodos();
$mensaje = $_GET['mensaje'] ?? '';

require_once __DIR__ . '/../views/roles/listar.php';