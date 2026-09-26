<?php
/**
 * Registrar nuevo Tribunal / Jurado
 */
require_once __DIR__ . '/../config/sesion.php';

// Verificar permisos
if (!tieneRol(['administrador','coordinador_mg'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../models/MgTribunalModel.php';

$modelo = new MgTribunalModel();
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
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=mg_tribunales_listar&guardado=ok');
            exit;
        }
        $error = 'Error al guardar. Verifique la base de datos.';
    }
}

// ✅ RUTA CORREGIDA
require_once __DIR__ . '/../views/mg_tribunales/crear.php';