<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

// Solamente usuarios autorizados pueden reemplazar tribunales
requerirPermiso(
    'mg.tribunales.cambiar',
    '../index.php'
);

$modeloTribunal = new TribunalModel($pdo);
$modeloBitacora = new BitacoraModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

// El identificador llega por GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTribunal = filter_input(
        INPUT_POST,
        'id_tribunal',
        FILTER_VALIDATE_INT
    );
} else {
    $idTribunal = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idTribunal) {
    header(
        'Location: tribunales_listar.php?estado=no_encontrado'
    );
    exit;
}

$tribunalActual = $modeloTribunal->buscarPorId(
    $idTribunal
);

if (
    !$tribunalActual
    || $tribunalActual['estado'] !== 'vigente'
    || (int) $tribunalActual['asignacion_vigente'] !== 1
) {
    header(
        'Location: tribunales_listar.php?estado=no_encontrado'
    );
    exit;
}

$idExpediente = (int) $tribunalActual['id_expediente'];

$expediente = $modeloTribunal->buscarExpediente(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: tribunales_listar.php?estado=no_encontrado'
    );
    exit;
}

$tutorPrincipal = $modeloTribunal->buscarTutorPrincipal(
    $idExpediente
);

$tribunalesExpediente = $modeloTribunal->listarPorExpediente(
    $idExpediente
);

// Identificamos docentes que ya forman parte del tribunal vigente
$idsOcupados = [];

foreach ($tribunalesExpediente as $tribunal) {
    if (
        $tribunal['etapa'] === $tribunalActual['etapa']
        && $tribunal['estado'] === 'vigente'
    ) {
        $idsOcupados[] = (int) $tribunal['id_tutor'];
    }
}

// Excluimos docentes ya asignados en la misma etapa
$docentesDisponibles = array_filter(
    $modeloTribunal->listarDocentesActivos(),
    fn(array $docente): bool =>
        !in_array(
            (int) $docente['id_tutor'],
            $idsOcupados,
            true
        )
);

$idNuevoTutor = null;
$motivoCambio = '';
$confirmarTutorPrincipal = false;
$error = '';
$advertencia = '';

// Procesamos el reemplazo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevoTutor = filter_input(
        INPUT_POST,
        'id_nuevo_tutor',
        FILTER_VALIDATE_INT
    );

    $motivoCambio = trim(
        $_POST['motivo_cambio'] ?? ''
    );

    $confirmarTutorPrincipal = isset(
        $_POST['confirmar_tutor_principal']
    );

    $idsDisponibles = array_map(
        fn(array $docente): int =>
            (int) $docente['id_tutor'],
        $docentesDisponibles
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.';
    } elseif (
        !$idNuevoTutor
        || !in_array(
            $idNuevoTutor,
            $idsDisponibles,
            true
        )
    ) {
        $error = 'Selecciona un docente disponible.';
    } elseif (
        mb_strlen($motivoCambio) < 5
        || mb_strlen($motivoCambio) > 255
    ) {
        $error = 'El motivo debe contener entre 5 y 255 caracteres.';
    } elseif (
        $tutorPrincipal
        && (int) $tutorPrincipal['id_tutor'] === $idNuevoTutor
        && !$confirmarTutorPrincipal
    ) {
        $advertencia = 'El nuevo docente también es el tutor principal. Confirma que esta coincidencia fue revisada.';
    } else {
        try {
            $resultado = $modeloTribunal->reemplazar(
                $idTribunal,
                $idNuevoTutor,
                $motivoCambio,
                (int) $usuarioSesion['id_usuario']
            );

            if (!$resultado) {
                $error = 'La asignación ya no se encuentra vigente.';
            } else {
                /*
                 * El modelo realiza varias consultas durante el
                 * reemplazo. Por eso buscamos directamente la nueva
                 * asignación en lugar de usar lastInsertId().
                 */
                $idNuevoTribunal = null;

                $tribunalesActualizados =
                    $modeloTribunal->listarPorExpediente(
                        $idExpediente
                    );

                foreach (
                    $tribunalesActualizados as $tribunal
                ) {
                    if (
                        $tribunal['etapa']
                            === $tribunalActual['etapa']
                        && (int) $tribunal['id_tutor']
                            === $idNuevoTutor
                        && (int) $tribunal['orden']
                            === (int) $tribunalActual['orden']
                        && $tribunal['estado'] === 'vigente'
                        && (int) $tribunal['asignacion_vigente']
                            === 1
                    ) {
                        $idNuevoTribunal = (int)
                            $tribunal['id_tribunal'];

                        break;
                    }
                }

                try {
                    $modeloBitacora->registrar(
                        (int) $usuarioSesion['id_usuario'],
                        'reemplazar_tribunal',
                        'tribunales_defensa',
                        $idNuevoTribunal,
                        [
                            'id_tribunal' => $idTribunal,
                            'id_expediente' => $idExpediente,
                            'etapa' => $tribunalActual['etapa'],
                            'id_tutor' => (int)
                                $tribunalActual['id_tutor'],
                            'orden' => (int)
                                $tribunalActual['orden'],
                            'estado' => 'vigente'
                        ],
                        [
                            'id_tribunal_anterior' => $idTribunal,
                            'id_expediente' => $idExpediente,
                            'etapa' => $tribunalActual['etapa'],
                            'id_tutor' => $idNuevoTutor,
                            'orden' => (int)
                                $tribunalActual['orden'],
                            'estado' => 'vigente',
                            'motivo_cambio' => $motivoCambio
                        ]
                    );
                } catch (Throwable $errorBitacora) {
                    error_log(
                        'No se pudo auditar el reemplazo del tribunal: '
                        . $errorBitacora->getMessage()
                    );
                }

                header(
                    'Location: tribunales_gestionar.php?id='
                    . $idExpediente
                    . '&estado=reemplazado'
                );
                exit;
            }
        } catch (PDOException $e) {
            error_log(
                'Error al reemplazar el tribunal: '
                . $e->getMessage()
            );

            if ($e->getCode() === '23000') {
                $error = 'El docente seleccionado ya pertenece al tribunal.';
            } else {
                $error = 'No fue posible reemplazar el tribunal.';
            }
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Reemplazar tribunal';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tribunales/reemplazar.php';