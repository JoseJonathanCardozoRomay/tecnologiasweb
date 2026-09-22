<?php
require_once __DIR__ . '/../models/PeriodoTutoriaModel.php';
require_once __DIR__ . '/../config/sesion.php';

$modelo = new PeriodoTutoriaModel();
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=periodos_listar');
    exit;
}

$periodo = $modelo->obtenerPorId($id);
if (!$periodo) {
    header('Location: index.php?accion=periodos_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'nombre' => trim($_POST['nombre'] ?? ''),
        'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
        'fecha_fin' => trim($_POST['fecha_fin'] ?? ''),
        'activo' => (int)($_POST['activo'] ?? 1)
    ];

    if (empty($datos['codigo']) || empty($datos['nombre']) || empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        $resultado = $modelo->actualizar($id, $datos);
        if ($resultado === true) {
            header('Location: index.php?accion=periodos_listar');
            exit;
        } elseif ($resultado === 'existe') {
            $error = 'El código "' . $datos['codigo'] . '" ya está en uso. Usa otro.';
        } else {
            $error = 'Error al actualizar';
        }
    }
}

require_once __DIR__ . '/../views/periodos/editar.php';