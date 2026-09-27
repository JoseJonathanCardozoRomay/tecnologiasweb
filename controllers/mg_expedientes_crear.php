<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$expedienteModel = new MgExpedienteModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $idEstudiante = (int) ($_POST['id_estudiante'] ?? 0);
    $idModalidad = (int) ($_POST['id_modalidad_grado'] ?? 0);
    $idCohorte = (int) ($_POST['id_cohorte_mg'] ?? 0);
    $detalle = trim($_POST['solicitud_detalle'] ?? '');

    if ($idEstudiante <= 0) {
        $errores[] = "Debe seleccionar un estudiante habilitado para Modalidad de Grado.";
    }
    if ($idModalidad <= 0) {
        $errores[] = "Debe seleccionar la modalidad de grado.";
    }
    if ($idCohorte <= 0) {
        $errores[] = "Debe seleccionar la cohorte vigente.";
    }

    if (empty($errores)) {
        try {
            $nuevoId = $expedienteModel->crear($idEstudiante, $idModalidad, $idCohorte, $detalle);
            flash_set('success', "Expediente MG registrado correctamente (N° $nuevoId).");
            header('Location: mg_expediente.php?id=' . $nuevoId);
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar el expediente.";
        }
    }
}

$estudiantes = $expedienteModel->estudiantesDisponibles();
$modalidades = (new MgModalidadModel($pdo))->obtenerTodasActivas();
$cohortes = (new MgCohorteModel($pdo))->obtenerTodasAbiertas();

if (count($modalidades) === 0) {
    $errores[] = "No hay modalidades activas registradas. Registre al menos una en el catálogo.";
}
if (count($cohortes) === 0) {
    $errores[] = "No hay cohortes abiertas. Abra una cohorte antes de registrar expedientes.";
}

require_once __DIR__ . '/../views/mg/expedientes/crear.php';