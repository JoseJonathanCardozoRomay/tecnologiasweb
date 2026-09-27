<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$cohorteModel = new MgCohorteModel($pdo);

$id = $_GET['id'] ?? $_POST['id_cohorte_mg'] ?? null;
if (!$id) {
    header('Location: mg_cohortes_listar.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $nombrePeriodo = trim($_POST['nombre_periodo'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';
    $estado = $_POST['estado'] ?? 'planificada';
    $observaciones = $_POST['observaciones'] ?? '';

    if ($nombrePeriodo === '') {
        $errores[] = "El período de la cohorte no puede estar vacío.";
    }
    if (!validarFechaISO($fechaInicio)) {
        $errores[] = "La fecha de inicio no es válida.";
    }
    if ($fechaFin !== '' && !validarFechaISO($fechaFin)) {
        $errores[] = "La fecha de fin no es válida.";
    }
    if ($fechaFin !== '' && $fechaInicio !== '' && $fechaFin < $fechaInicio) {
        $errores[] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
    }
    if (!in_array($estado, ['planificada', 'abierta', 'cerrada'], true)) {
        $errores[] = "El estado seleccionado no es válido.";
    }
    if ($cohorteModel->existePeriodo($nombrePeriodo, $id)) {
        $errores[] = "Ya existe otra cohorte con ese período.";
    }

    if (empty($errores)) {
        try {
            $cohorteModel->actualizar($id, $nombrePeriodo, $fechaInicio, $fechaFin, $estado, $observaciones);
            flash_set('success', 'Cohorte actualizada correctamente.');
            header('Location: mg_cohortes_listar.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al actualizar la cohorte.";
        }
    }
}

$cohorte_actual = $cohorteModel->obtenerPorId($id);
if (!$cohorte_actual) {
    header('Location: mg_cohortes_listar.php');
    exit;
}

require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../views/mg/cohortes/editar.php';