<?php
require_once __DIR__ . '/../models/PeriodoTutoriaModel.php';
require_once __DIR__ . '/../config/sesion.php';

$modelo = new PeriodoTutoriaModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'nombre' => trim($_POST['nombre'] ?? ''),
        'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
        'fecha_fin' => trim($_POST['fecha_fin'] ?? ''),
        'activo' => (int)($_POST['activo'] ?? 1),
        'creado_por' => $_SESSION['id_usuario'] ?? null
    ];

    if (empty($datos['codigo']) || empty($datos['nombre']) || empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        $resultado = $modelo->crear($datos);
        if ($resultado === true) {
            header('Location: index.php?accion=periodos_listar');
            exit;
        } elseif ($resultado === 'existe') {
            $error = 'El código "' . $datos['codigo'] . '" YA EXISTE. Escribe otro diferente.';
        } else {
            $error = 'Error al guardar. Verifica los datos.';
        }
    }
}

require_once __DIR__ . '/../views/periodos/crear.php';