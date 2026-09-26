<?php
/**
 * Controlador — Editar Expediente
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';

$modelo = new MgExpedienteModel();
$id = (int)($_GET['id_expediente'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('Expediente no válido');location.href='index.php?accion=mg_expedientes_listar';</script>";
    exit;
}

$expediente = $modelo->obtenerPorId($id);
if (!$expediente) {
    echo "<script>alert('Expediente no encontrado');location.href='index.php?accion=mg_expedientes_listar';</script>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_estudiante'  => (int)($_POST['id_estudiante'] ?? 0),
        'id_modalidad'   => (int)($_POST['id_modalidad'] ?? 0),
        'id_cohorte'     => (int)($_POST['id_cohorte'] ?? 0),
        'fecha_inicio'   => trim($_POST['fecha_inicio'] ?? ''),
        'titulo_trabajo' => trim($_POST['titulo_trabajo'] ?? ''),
        'observaciones'  => trim($_POST['observaciones'] ?? ''),
        'estado'         => trim($_POST['estado'] ?? 'activo')
    ];

    if ($modelo->actualizar($id, $datos)) {
        echo "<script>alert('Expediente actualizado correctamente');location.href='index.php?accion=mg_expedientes_listar';</script>";
        exit;
    } else {
        $error = "No se pudo actualizar. Verifica los datos.";
    }
}

require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';

$estudianteModel = new EstudianteModel();
$modalidadModel = new MgModalidadModel();
$cohorteModel = new MgCohorteModel();

$estudiantes = $estudianteModel->listarTodos();
$modalidades = $modalidadModel->listarTodas();
$cohortes = $cohorteModel->listarTodas();

require_once __DIR__ . '/../views/mg_expedientes/editar.php';