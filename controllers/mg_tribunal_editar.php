<?php
/**
 * Editar Tribunal / Jurado
 */
require_once __DIR__ . '/../config/sesion.php';

// Verificar permisos
if (!tieneRol(['administrador','coordinador_mg'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../models/MgTribunalModel.php';

$modelo = new MgTribunalModel();
$id_tribunal = (int)($_GET['id'] ?? 0);
$tribunal = $modelo->obtenerPorId($id_tribunal);

if (!$tribunal) {
    header('Location: index.php?accion=mg_tribunales_listar');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
        'especialidad' => trim($_POST['especialidad'] ?? ''),
        'correo' => trim($_POST['correo'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'activo' => isset($_POST['activo']) ? 1 : 0
    ];

    if (empty($datos['nombre_completo'])) {
        $error = 'Escriba el nombre completo del tribunal';
    } else {
        if ($modelo->actualizar($id_tribunal, $datos)) {
            header('Location: index.php?accion=mg_tribunales_listar&actualizado=ok');
            exit;
        }
        $error = 'Error al actualizar el registro';
    }
}

require_once __DIR__ . '/../views/mg_tribunales/editar.php';