<?php
require_once __DIR__ . '/../models/BloqueHorarioModel.php';

$modelo = new BloqueHorarioModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_bloque' => trim($_POST['nombre_bloque'] ?? ''),
        'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
        'hora_fin' => trim($_POST['hora_fin'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? '')
    ];

    if (empty($datos['nombre_bloque']) || empty($datos['hora_inicio']) || empty($datos['hora_fin'])) {
        $error = 'Completa los campos obligatorios';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=bloques_listar');
            exit;
        }
        $error = 'Error al guardar. Intenta de nuevo.';
    }
}

require_once __DIR__ . '/../views/bloques/crear.php';