<?php
/**
 * Editar Expediente de Modalidad de Grado
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new MgExpedienteModel();
$modeloCohorte = new MgCohorteModel();
$modeloModalidad = new MgModalidadModel();
$modeloEstudiante = new EstudianteModel();

$id = (int)($_GET['id'] ?? 0);
$error = '';
$exito = '';

if ($id <= 0) {
    header('Location: index.php?accion=mg_expedientes_listar');
    exit;
}

$expediente = $modelo->obtenerPorId($id);
if (!$expediente) {
    header('Location: index.php?accion=mg_expedientes_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_estudiante' => (int)($_POST['id_estudiante'] ?? 0),
        'id_modalidad' => (int)($_POST['id_modalidad'] ?? 0),
        'id_cohorte' => (int)($_POST['id_cohorte'] ?? 0),
        'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
        'titulo_trabajo' => trim($_POST['titulo_trabajo'] ?? ''),
        'observaciones' => trim($_POST['observaciones'] ?? '')
    ];

    global $conexion;
    $verificar = $conexion->prepare("
        SELECT id_expediente FROM expedientes_mg 
        WHERE id_estudiante = :id_estudiante 
          AND id_modalidad = :id_modalidad 
          AND id_cohorte = :id_cohorte
          AND id_expediente != :id_actual
        LIMIT 1
    ");
    $verificar->execute([
        ':id_estudiante' => $datos['id_estudiante'],
        ':id_modalidad' => $datos['id_modalidad'],
        ':id_cohorte' => $datos['id_cohorte'],
        ':id_actual' => $id
    ]);
    
    if ($verificar->fetch()) {
        $error = '⚠️ Ya existe un expediente con este Estudiante + Modalidad + Cohorte. Elige valores diferentes.';
    } else {
        if ($modelo->editar($id, $datos)) {
            $exito = '✅ Expediente actualizado correctamente.';
            $expediente = $modelo->obtenerPorId($id);
        } else {
            $error = '❌ Error al actualizar.';
        }
    }
}

$estudiantes = $modeloEstudiante->listarTodos();
$modalidades = $modeloModalidad->listarTodos();
$cohortes = $modeloCohorte->listarTodos();

require_once __DIR__ . '/../views/mg_expedientes/editar.php';