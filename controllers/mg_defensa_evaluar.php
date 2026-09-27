<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Defensa no válida.');
    header('Location: mg_defensas_listar.php');
    exit;
}

$defensaModel = new MgDefensaModel($pdo);
$defensa = $defensaModel->obtenerConDetalle($id);
if (!$defensa) {
    flash_set('error', 'La defensa solicitada no existe.');
    header('Location: mg_defensas_listar.php');
    exit;
}

$parametroModel = new MgParametroModel($pdo);
$notaAprob = 51;
foreach ($parametroModel->obtenerTodas() as $p) {
    if ($p['clave'] === 'nota_aprobacion_mg') {
        $notaAprob = (float) $p['valor'];
    }
}

$errores = [];
$resultadoOk = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $notas = $_POST['nota'] ?? [];
    $tribunalIds = array_map('intval', array_column($defensa['tribunales'], 'id_tribunal_defensa_mg'));

    if (!$tribunalIds) {
        $errores[] = "La defensa no tiene tribunales registrados.";
    }
    foreach ($tribunalIds as $idTrib) {
        $nota = $notas[$idTrib] ?? null;
        if ($nota === null || $nota === '') {
            $errores[] = "Falta la nota de un miembro del tribunal.";
        } elseif (!is_numeric($nota) || (float) $nota < 0 || (float) $nota > 100) {
            $errores[] = "Las notas deben estar entre 0 y 100.";
        }
    }

    if (empty($errores)) {
        try {
            $notasFinales = [];
            foreach ($tribunalIds as $idTrib) {
                $notasFinales[$idTrib] = (float) $notas[$idTrib];
            }
            $resultadoOk = $defensaModel->evaluar($id, $notasFinales, $notaAprob);
            $resolucion = 'Defensa ' . $resultadoOk['resultado'] . ' - promedio ' . number_format($resultadoOk['promedio'], 2) . '/100';
            $defensaModel->actualizarEstadoExpediente($id, $resultadoOk['resultado'], $resolucion);

            mgAvisarEstudiante(
                $pdo,
                (int) $defensa['id_expediente_mg'],
                'MG_DEFENSA_EVALUADA',
                'Tu defensa N° ' . $id . ' fue evaluada: resultado ' . strtoupper($resultadoOk['resultado'])
                    . ' con promedio ' . number_format($resultadoOk['promedio'], 2) . '/100 ('
                    . mgFechaEspanol(date('Y-m-d')) . ').',
                '/views/estudiante/mg_portal.php',
                (int) ($_SESSION['id_usuario'] ?? 0)
            );

            flash_set('success', 'Evaluación guardada. Promedio: ' . number_format($resultadoOk['promedio'], 2)
                . ' — ' . strtoupper($resultadoOk['resultado']) . '.');
            header('Location: mg_defensa_evaluar.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al guardar la evaluación.";
        }
    }

    $notasActuales = $notas;
}

$defensa = $defensaModel->obtenerConDetalle($id);

require_once __DIR__ . '/../views/mg/defensas/evaluar.php';