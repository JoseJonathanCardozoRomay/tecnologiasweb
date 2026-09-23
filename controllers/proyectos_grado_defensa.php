<?php
declare(strict_types=1);

/**
 * Registra el resultado de la defensa de un proyecto de grado.
 * Solo administración puede informar el resultado.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('proyectos_grado_listar.php');
}

exigirCsrf();
$id = validarId($_POST['id_proyecto'] ?? null);
$resultado = (string)($_POST['resultado_defensa'] ?? '');

if (!$id || !in_array($resultado, ['aprobado','reprobado'], true)) {
    flash('danger', 'Debes seleccionar un proyecto y un resultado de defensa válidos.');
    redirect('proyectos_grado_listar.php');
}

$m = new ProyectoGradoModel($pdo);
$proyecto = $m->obtenerPorId($id);
if (!$proyecto) {
    flash('danger', 'El proyecto de grado no existe.');
    redirect('proyectos_grado_listar.php');
}

try {
    if (!$m->registrarResultadoDefensa($id, $resultado)) {
        flash('danger', $resultado === 'aprobado'
            ? 'No se puede concluir el proyecto mientras tenga tutorías personales pendientes, programadas o en ejecución.'
            : 'El proyecto ya no está en estado En curso o fue gestionado anteriormente.');
        redirect('proyectos_grado_listar.php');
    }

    if ($resultado === 'aprobado') {
        registrarAccion(
            $pdo,
            'DEFENSA_APROBADA',
            'Proyectos de grado',
            'La defensa del proyecto #'.$id.' fue aprobada. El proyecto pasó a estado concluido.'
        );
        flash('success', 'Defensa aprobada. El proyecto ahora está Concluido y no admite nuevas acciones.');
    } else {
        registrarAccion(
            $pdo,
            'DEFENSA_REPROBADA',
            'Proyectos de grado',
            'La defensa del proyecto #'.$id.' fue reprobada. El proyecto permanece En curso.'
        );
        flash('warning', 'Defensa no aprobada. El proyecto permanece En curso y el estudiante puede continuar trabajando en él.');
    }
} catch (PDOException $e) {
    flash('danger', 'No se pudo registrar el resultado de la defensa.');
}

redirect('proyectos_grado_listar.php');
