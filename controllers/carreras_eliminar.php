<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/flash.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $carreraModel = new CarreraModel($pdo);
    try {
        $dependencias = $carreraModel->contarDependencias($id);
        if ((int) $dependencias['materias'] > 0 || (int) $dependencias['estudiantes'] > 0) {
            $partes = [];
            if ((int) $dependencias['materias'] > 0) {
                $partes[] = $dependencias['materias'] . ' materias';
            }
            if ((int) $dependencias['estudiantes'] > 0) {
                $partes[] = $dependencias['estudiantes'] . ' estudiantes';
            }
            flash_set('danger', 'No se puede eliminar: tiene ' . implode(' y ', $partes) . ' asociados.');
        } else {
            $carreraModel->eliminar($id);
            flash_set('success', 'Carrera eliminada correctamente.');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('danger', 'No se pudo eliminar la carrera.');
    }
}

header("Location: carreras_listar.php");
exit;
