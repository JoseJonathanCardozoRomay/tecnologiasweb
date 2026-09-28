<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../models/ParametroMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

// La asignación corresponde al administrador o coordinador.
requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.tutores.asignar',
    '../index.php'
);

$modeloExpediente = new ExpedienteMgModel($pdo);
$modeloAsignacion = new AsignacionTutorModel($pdo);
$modeloParametro = new ParametroMgModel($pdo);

// El identificador llega por GET al abrir la página
// y por POST cuando se confirma la asignación.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idExpediente = filter_input(
        INPUT_POST,
        'id_expediente',
        FILTER_VALIDATE_INT
    );
} else {
    $idExpediente = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idExpediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

$expediente = $modeloExpediente->buscarPorId(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

$asignacionActual = $modeloAsignacion
    ->buscarVigentePorExpediente($idExpediente);

$tutores = $modeloAsignacion
    ->listarTutoresDisponibles();

// La carga recomendada es configurable.
// Si el parámetro no existe, utilizamos 3 como valor seguro.
$cargaRecomendada = (int) (
    $modeloParametro->obtener(
        'tutor_carga_recomendada'
    ) ?? 3
);

if ($cargaRecomendada < 1) {
    $cargaRecomendada = 3;
}

$idTutor = null;
$responsabilidades = '';
$referenciaDecanatura = '';
$error = '';

// Verificaciones previas del expediente.
if ((int) $expediente['requiere_tutor'] !== 1) {
    $error = 'La modalidad de este expediente no requiere tutor.';
} elseif ($expediente['estado'] !== 'activo') {
    $error = 'No se puede asignar un tutor a un expediente cerrado.';
} elseif ($expediente['etapa_actual'] !== 'previa') {
    $error = 'La asignación inicial solamente puede realizarse en la etapa previa.';
} elseif ($asignacionActual) {
    $error = 'Este expediente ya tiene un tutor asignado.';
} elseif (empty($tutores)) {
    $error = 'No existen tutores activos disponibles.';
}

// Procesamos la asignación enviada desde el formulario.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $error === ''
) {
    if (!validarTokenCsrf()) {
        $error = 'La solicitud no es válida. Actualiza la página e inténtalo nuevamente.';
    } else {
        $idTutor = filter_input(
            INPUT_POST,
            'id_tutor',
            FILTER_VALIDATE_INT
        );

        $responsabilidadesRecibidas = $_POST[
            'responsabilidades'
        ] ?? '';

        $responsabilidades = is_string(
            $responsabilidadesRecibidas
        )
            ? trim($responsabilidadesRecibidas)
            : '';

        $referenciaDecanatura = is_string($_POST['referencia_decanatura'] ?? null)
            ? trim($_POST['referencia_decanatura']) : '';
        $disponibilidadConsultada = ($_POST['disponibilidad_consultada'] ?? '') === '1';

        $tutoresValidos = array_map(
            fn(array $tutor): int => (int) $tutor['id_tutor'],
            $tutores
        );

        if (
            !$idTutor
            || !in_array(
                $idTutor,
                $tutoresValidos,
                true
            )
        ) {
            $error = 'Selecciona un tutor válido.';
        } elseif (mb_strlen($responsabilidades) > 2000) {
            $error = 'Las responsabilidades no pueden superar los 2000 caracteres.';
        } elseif (!$disponibilidadConsultada || $referenciaDecanatura === ''
            || mb_strlen($referenciaDecanatura) > 100) {
            $error = 'Confirma la disponibilidad y escribe la referencia de Decanatura.';
        } else {
            $usuarioSesion = obtenerUsuarioSesion();
            $idUsuario = (int) (
                $usuarioSesion['id_usuario'] ?? 0
            );

            if ($idUsuario < 1) {
                $error = 'No fue posible identificar al usuario de la sesión.';
            } else {
                try {
                    $modeloAsignacion->asignar(
                        $idExpediente,
                        $idTutor,
                        $idUsuario,
                        $responsabilidades !== ''
                            ? $responsabilidades
                            : null,
                        $referenciaDecanatura,
                        $disponibilidadConsultada
                    );

                    header(
                        'Location: expedientes_ver.php?id='
                        . $idExpediente
                        . '&estado=tutor_asignado'
                    );
                    exit;
                } catch (RuntimeException | InvalidArgumentException $e) {
                    $error = $e->getMessage();
                } catch (PDOException $e) {
                    $error = 'No fue posible registrar la asignación del tutor.';
                }
            }
        }
    }
}

// Datos utilizados por la vista.
$tituloPagina = 'Asignar tutor';
$rutaBase = '../';

require_once __DIR__
    . '/../views/expedientes/asignar_tutor.php';
