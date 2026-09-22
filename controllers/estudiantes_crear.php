<?php
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new EstudianteModel();
$usuarios = $modelo->listarUsuariosDisponibles();
$carreras = $modelo->listarCarreras();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->crear($_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=estudiante_crear&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=estudiantes_listar&mensaje=creado");
    exit;
}

require_once __DIR__ . '/../views/estudiantes/crear.php';