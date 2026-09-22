<?php
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new EstudianteModel();
$id = $_GET['id'] ?? 0;
$estudiante = $modelo->obtenerPorId($id);

if (!$estudiante) {
    header('Location: ../index.php?accion=estudiantes_listar');
    exit;
}

$usuarios = $modelo->listarUsuariosDisponibles();
$carreras = $modelo->listarCarreras();
$id_usuario_actual = $modelo->obtenerUsuarioId($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->editar($id, $_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=estudiante_editar&id=$id&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=estudiantes_listar&mensaje=actualizado");
    exit;
}

require_once __DIR__ . '/../views/estudiantes/editar.php';