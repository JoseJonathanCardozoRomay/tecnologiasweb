<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(['administrador', 'coordinador_mg'], '../index.php');
requerirPermiso('mg.tutores.cambiar', '../index.php');

$idExpediente = filter_input(
    $_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET,
    $_SERVER['REQUEST_METHOD'] === 'POST' ? 'id_expediente' : 'id',
    FILTER_VALIDATE_INT
);
if (!$idExpediente) {
    header('Location: expedientes_listar.php?estado=no_encontrado');
    exit;
}

$modeloAsignacion = new AsignacionTutorModel($pdo);
$expediente = (new ExpedienteMgModel($pdo))->buscarPorId($idExpediente);
$asignacionActual = $modeloAsignacion->buscarVigentePorExpediente($idExpediente);
if (!$expediente || !$asignacionActual || $expediente['estado'] !== 'activo'
    || $expediente['etapa_actual'] === 'previa'
    || (int) $expediente['requiere_tutor'] !== 1) {
    header('Location: expedientes_ver.php?id=' . $idExpediente);
    exit;
}

$tutores = array_values(array_filter(
    $modeloAsignacion->listarTutoresDisponibles(),
    fn(array $tutor): bool => (int) $tutor['id_tutor'] !== (int) $asignacionActual['id_tutor']
));
$error = '';
$datos = [
    'id_tutor' => '', 'motivo' => '', 'fecha_nota_renuncia' => '',
    'referencia_decanatura' => '', 'responsabilidades' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $clave => $_) {
        $datos[$clave] = is_string($_POST[$clave] ?? null)
            ? trim($_POST[$clave]) : '';
    }
    $idTutor = filter_input(INPUT_POST, 'id_tutor', FILTER_VALIDATE_INT);
    $idsValidos = array_map(fn(array $tutor): int => (int) $tutor['id_tutor'], $tutores);
    if (!validarTokenCsrf()) {
        $error = 'El formulario venció. Recarga la página.';
    } elseif (!$idTutor || !in_array($idTutor, $idsValidos, true)) {
        $error = 'Selecciona un tutor activo diferente.';
    } elseif (mb_strlen($datos['responsabilidades']) > 2000) {
        $error = 'Las responsabilidades superan los 2000 caracteres.';
    } else {
        try {
            $usuario = obtenerUsuarioSesion();
            $modeloAsignacion->cambiarTutor(
                $idExpediente, $idTutor, (int) $usuario['id_usuario'],
                $datos['motivo'], $datos['fecha_nota_renuncia'],
                $datos['referencia_decanatura'],
                ($_POST['disponibilidad_consultada'] ?? '') === '1',
                $datos['responsabilidades'] !== '' ? $datos['responsabilidades'] : null
            );
            header('Location: expedientes_ver.php?id=' . $idExpediente . '&estado=tutor_cambiado');
            exit;
        } catch (InvalidArgumentException | RuntimeException $e) {
            $error = $e->getMessage();
        } catch (Throwable $e) {
            error_log('Cambio de tutor: ' . $e->getMessage());
            $error = 'No fue posible registrar el cambio de tutor.';
        }
    }
}

$tituloPagina = 'Cambiar tutor';
$rutaBase = '../';
require_once __DIR__ . '/../views/expedientes/cambiar_tutor.php';
