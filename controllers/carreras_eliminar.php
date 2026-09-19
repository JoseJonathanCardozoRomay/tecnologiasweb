<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
csrf_validar();
$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    try {
        $modelo = new CarreraModel($pdo);
        $dependencias = $modelo->contarDependencias($id);
        if ((int) $dependencias['materias'] > 0 || (int) $dependencias['estudiantes'] > 0) flash_set('danger', 'No se puede eliminar: tiene dependencias asociadas.');
        else { $modelo->eliminar($id); flash_set('success', 'Carrera eliminada correctamente.'); }
    } catch (PDOException $e) { error_log($e->getMessage()); flash_set('danger', 'No se pudo eliminar la carrera.'); }
}
header('Location: carreras_listar.php');
exit;
