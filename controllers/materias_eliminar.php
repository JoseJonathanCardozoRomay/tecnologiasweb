<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
csrf_validar();
$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    try {
        $modelo = new MateriaModel($pdo);
        $dependencias = $modelo->contarDependencias($id);
        if ($dependencias > 0) flash_set('danger', "No se puede eliminar: tiene {$dependencias} tutorías asociadas.");
        else { $modelo->eliminar($id); flash_set('success', 'Materia eliminada correctamente.'); }
    } catch (PDOException $e) { error_log($e->getMessage()); flash_set('danger', 'No se pudo eliminar la materia.'); }
}
header('Location: materias_listar.php');
exit;
