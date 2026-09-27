<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$defensaModel = new MgDefensaModel($pdo);
$cohorteModel = new MgCohorteModel($pdo);
$parametroModel = new MgParametroModel($pdo);

$esEdicion = (int) ($_GET['id'] ?? 0) > 0;
$idDefensa = (int) ($_GET['id'] ?? 0);
$defensaEdit = null;
if ($esEdicion) {
    $defensaEdit = $defensaModel->obtenerConDetalle($idDefensa);
    if (!$defensaEdit) {
        flash_set('error', 'La defensa solicitada no existe.');
        header('Location: mg_defensas_listar.php');
        exit;
    }
}

$errores = [];

function mgHoraValida($valor): bool
{
    if (!is_scalar($valor) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $valor)) {
        return false;
    }

    [$h, $m] = array_map('intval', explode(':', (string) $valor));

    return $h >= 0 && $h <= 23 && $m >= 0 && $m <= 59;
}

function mgHoraNumero($valor): int
{
    return (int) str_replace(':', '', (string) $valor);
}

$parametros = [];
foreach ($parametroModel->obtenerTodas() as $p) {
    $parametros[$p['clave']] = $p['valor'];
}
$minTribunales = (int) ($parametros['tribunales_min_mg'] ?? 2);
$maxTribunales = (int) ($parametros['tribunales_max_mg'] ?? 5);

$ambientes = $pdo->query(
    "SELECT id_ambiente_mg, nombre, ubicacion, capacidad FROM ambientes_mg WHERE activa = 1 ORDER BY nombre"
)->fetchAll();

$cohortes = $cohorteModel->obtenerTodas();
$cohortesPorId = [];
foreach ($cohortes as $c) {
    $cohortesPorId[(int) $c['id_cohorte_mg']] = $c;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $idExp = (int) ($_POST['id_expediente_mg'] ?? 0);
    $idCohorte = (int) ($_POST['id_cohorte_mg'] ?? 0);
    $idAmbiente = (int) ($_POST['id_ambiente_mg'] ?? 0);
    $fecha = trim($_POST['fecha_defensa'] ?? '');
    $hIni = trim($_POST['hora_inicio'] ?? '');
    $hFin = trim($_POST['hora_fin'] ?? '');
    $obs = trim($_POST['observaciones'] ?? '');
    $tribunales = array_values(array_unique(array_map('intval', $_POST['tribunales'] ?? [])));

    if (!$esEdicion) {
        if ($idExp <= 0) {
            $errores[] = "Selecciona el expediente a evaluar.";
        }
        if ($idCohorte <= 0 || !isset($cohortesPorId[$idCohorte])) {
            $errores[] = "La cohorte no es válida.";
        }
    }

    if ($idAmbiente && !in_array($idAmbiente, array_map('intval', array_column($ambientes, 'id_ambiente_mg')), true)) {
        $errores[] = "El ambiente seleccionado no es válido.";
    }
    if (!validarFechaISO($fecha)) {
        $errores[] = "La fecha de la defensa no es válida.";
    }
    if (!mgHoraValida($hIni) || !mgHoraValida($hFin)) {
        $errores[] = "Las horas de inicio y fin no son válidas.";
    } elseif (mgHoraNumero($hIni) >= mgHoraNumero($hFin)) {
        $errores[] = "La hora de fin debe ser posterior a la hora de inicio.";
    }
    if (mb_strlen($obs) > 255) {
        $errores[] = "Las observaciones no pueden superar los 255 caracteres.";
    }

    $idExpReal = $esEdicion ? (int) $defensaEdit['id_expediente_mg'] : $idExp;
    $idCohorteReal = $esEdicion ? (int) $defensaEdit['id_cohorte_mg'] : $idCohorte;

    if ($idExpReal > 0) {
        if (!$esEdicion && $defensaModel->tieneDefensa($idExpReal)) {
            $errores[] = "El expediente seleccionado ya tiene una defensa programada.";
        }
        $idCarrera = $defensaModel->expedienteCarrera($idExpReal);
        $tutorActivo = $defensaModel->tutorActivoDe($idExpReal);
    } else {
        $idCarrera = 0;
        $tutorActivo = null;
    }

    $docentesAfin = $idExpReal > 0
        ? $defensaModel->docentesAfinCarrera($defensaModel->estudianteDeExpediente($idExpReal))
        : [];
    $idsAfines = array_map('intval', array_column($docentesAfin, 'id_tutor'));

    if (count($tribunales) < $minTribunales || count($tribunales) > $maxTribunales) {
        $errores[] = "La defensa debe tener entre {$minTribunales} y {$maxTribunales} tribunales.";
    }
    if (count(array_unique($tribunales)) !== count($tribunales)) {
        $errores[] = "No se puede repetir un docente en el tribunal.";
    }
    $invitadosNoAfin = array_diff($tribunales, $idsAfines);
    if ($invitadosNoAfin) {
        $errores[] = "Todos los tribunales deben ser afines a la carrera del estudiante.";
    }
    if ($tutorActivo && in_array($tutorActivo, $tribunales, true)) {
        $errores[] = "El tutor del expediente no puede formar parte del tribunal de su propia defensa.";
    }

    if (!$idAmbiente) {
        $errores[] = "Selecciona un ambiente/aula para la defensa.";
    }
    if (empty($errores)) {
        if ($defensaModel->conflictoAmbiente($idAmbiente, $fecha, $hIni, $hFin, $esEdicion ? $idDefensa : 0)) {
            $errores[] = "El ambiente seleccionado ya está ocupado en esa fecha y hora.";
        }
        foreach ($tribunales as $idTutorTrib) {
            if ($defensaModel->docenteOcupado($idTutorTrib, $fecha, $hIni, $hFin, $esEdicion ? $idDefensa : 0)) {
                $errores[] = "Un docente del tribunal tiene otra defensa en esa fecha y hora (anti-cruce).";
                break;
            }
        }
        if ($tutorActivo && $defensaModel->docenteOcupado($tutorActivo, $fecha, $hIni, $hFin, $esEdicion ? $idDefensa : 0)) {
            $errores[] = "El tutor del expediente tiene otra defensa en esa fecha y hora (anti-cruce).";
        }
    }

    if (empty($errores)) {
        try {
            if ($esEdicion) {
                $defensaModel->actualizar($idDefensa, $idAmbiente, $fecha, $hIni, $hFin, $obs, $tribunales);
                flash_set('success', 'Defensa actualizada sin conflictos de agenda.');
            } else {
                $idNuevo = $defensaModel->crear($idExpReal, $idCohorteReal, $idAmbiente, $fecha, $hIni, $hFin, $obs, $tribunales);
                flash_set('success', 'Defensa programada correctamente (#' . $idNuevo . ').');
            }
            $ambienteTexto = '';
            foreach ($ambientes as $ambiente) {
                if ((int) $ambiente['id_ambiente_mg'] === $idAmbiente) {
                    $ambienteTexto = ' en ' . $ambiente['nombre'];
                    break;
                }
            }
            $tribunalesTexto = count($tribunales) . ' docente(s)';
            mgAvisarEstudiante(
                $pdo,
                $idExpReal,
                'MG_DEFENSA_PROGRAMADA',
                ($esEdicion ? 'Tu defensa fue reprogramada' : 'Tu defensa fue programada')
                    . ' para el ' . mgFechaEspanol($fecha) . ' de ' . substr($hIni, 0, 5) . ' a '
                    . substr($hFin, 0, 5) . $ambienteTexto . ', con tribunal de ' . $tribunalesTexto . '.',
                '/views/estudiante/mg_portal.php',
                (int) ($_SESSION['id_usuario'] ?? 0)
            );
            header('Location: mg_defensas_listar.php?cohorte=' . $idCohorteReal);
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al guardar la defensa.";
        }
    }
}

if ($esEdicion && empty($_POST)) {
    $pre = $defensaEdit;
    $selExpediente = (int) $pre['id_expediente_mg'];
    $selCohorte = (int) $pre['id_cohorte_mg'];
    $selAmbiente = (int) ($pre['id_ambiente_mg'] ?? 0);
    $sFecha = $pre['fecha_defensa'];
    $sHIni = $pre['hora_inicio'];
    $sHFin = $pre['hora_fin'];
    $sObs = (string) ($pre['observaciones'] ?? '');
    $selTribunales = array_map('intval', array_column($pre['tribunales'], 'id_tutor'));
    $idExpSeleccion = (int) $pre['id_expediente_mg'];
    $idCarreraSel = $defensaModel->expedienteCarrera($selExpediente);
    $docentesAfin = $defensaModel->docentesAfinCarrera((int) $pre['id_estudiante']);
    $candidatos = [];
    $tutorActivo = $defensaModel->tutorActivoDe($selExpediente);
} else {
    $selExpediente = (int) ($_POST['id_expediente_mg'] ?? 0);
    $selCohorte = $esEdicion
        ? (int) ($defensaEdit['id_cohorte_mg'] ?? 0)
        : (int) ($_POST['id_cohorte_mg'] ?? $_GET['cohorte'] ?? 0);
    $selAmbiente = (int) ($_POST['id_ambiente_mg'] ?? 0);
    $sFecha = $_POST['fecha_defensa'] ?? '';
    $sHIni = $_POST['hora_inicio'] ?? '';
    $sHFin = $_POST['hora_fin'] ?? '';
    $sObs = $_POST['observaciones'] ?? '';
    $selTribunales = array_map('intval', $_POST['tribunales'] ?? []);
    $idExpSeleccion = $selExpediente;
    $docentesAfin = [];
    if ($selExpediente > 0) {
        $idEst = $defensaModel->estudianteDeExpediente($selExpediente);
        if ($idEst > 0) {
            $docentesAfin = $defensaModel->docentesAfinCarrera($idEst);
        }
    }
    $tutorActivo = $selExpediente > 0 ? $defensaModel->tutorActivoDe($selExpediente) : null;
}

$candidatos = $defensaModel->candidatos($esEdicion ? (int) $defensaEdit['id_cohorte_mg'] : ($selCohorte ?: $cohortes[0]['id_cohorte_mg']));
if ($esEdicion) {
    $candIds = array_map('intval', array_column($candidatos, 'id_expediente_mg'));
    if (!in_array((int) $defensaEdit['id_expediente_mg'], $candIds, true)) {
        array_unshift($candidatos, [
            'id_expediente_mg' => (int) $defensaEdit['id_expediente_mg'],
            'estado' => $defensaEdit['estado_expediente'],
            'solicitud_detalle' => $defensaEdit['solicitud_detalle'],
            'registro_universitario' => $defensaEdit['registro_universitario'],
            'estudiante_nombre' => $defensaEdit['estudiante_nombre'],
            'estudiante_apellido' => $defensaEdit['estudiante_apellido'],
            'modalidad_nombre' => $defensaEdit['modalidad_nombre'],
        ]);
    }
}

require_once __DIR__ . '/../views/mg/defensas/programar.php';