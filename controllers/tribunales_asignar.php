<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

// Solamente los usuarios autorizados pueden asignar tribunales
requerirPermiso(
    'mg.tribunales.asignar',
    '../index.php'
);

$modeloTribunal = new TribunalModel($pdo);
$modeloBitacora = new BitacoraModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

// El identificador llega por GET al abrir la página
// y por POST cuando se guarda la asignación.
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
        'Location: tribunales_listar.php?estado=no_encontrado'
    );
    exit;
}

$expediente = $modeloTribunal->buscarExpediente(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: tribunales_listar.php?estado=no_encontrado'
    );
    exit;
}

$etapaActual = $expediente['etapa_actual'];

if (
    $expediente['estado'] !== 'activo'
    || !in_array(
        $etapaActual,
        ['mg1', 'mg2'],
        true
    )
) {
    header(
        'Location: tribunales_listar.php?estado=etapa_invalida'
    );
    exit;
}

$docentes = $modeloTribunal->listarDocentesActivos();

$tribunales = $modeloTribunal->listarPorExpediente(
    $idExpediente
);

$tutorPrincipal = $modeloTribunal->buscarTutorPrincipal(
    $idExpediente
);

// Sugerimos la primera posición disponible
$ordenesOcupados = [];

foreach ($tribunales as $tribunal) {
    if (
        $tribunal['etapa'] === $etapaActual
        && $tribunal['estado'] === 'vigente'
    ) {
        $ordenesOcupados[] = (int) $tribunal['orden'];
    }
}

$orden = 1;

while (in_array($orden, $ordenesOcupados, true)) {
    $orden++;
}

$idTutor = null;
$confirmarTutorPrincipal = false;
$error = '';
$advertencia = '';

// Procesamos el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTutor = filter_input(
        INPUT_POST,
        'id_tutor',
        FILTER_VALIDATE_INT
    );

    $orden = filter_input(
        INPUT_POST,
        'orden',
        FILTER_VALIDATE_INT
    );

    $confirmarTutorPrincipal = isset(
        $_POST['confirmar_tutor_principal']
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.';
    } elseif (
        !$idTutor
        || !$modeloTribunal->docenteDisponible($idTutor)
    ) {
        $error = 'Selecciona un docente activo.';
    } elseif (
        !$orden
        || $orden < 1
        || $orden > 10
    ) {
        $error = 'La posición del tribunal debe estar entre 1 y 10.';
    } elseif (
        $modeloTribunal->docenteYaAsignado(
            $idExpediente,
            $etapaActual,
            $idTutor
        )
    ) {
        $error = 'El docente seleccionado ya pertenece al tribunal de esta etapa.';
    } elseif (
        $modeloTribunal->ordenOcupado(
            $idExpediente,
            $etapaActual,
            $orden
        )
    ) {
        $error = 'La posición seleccionada ya está ocupada.';
    } elseif (
        $tutorPrincipal
        && (int) $tutorPrincipal['id_tutor'] === $idTutor
        && !$confirmarTutorPrincipal
    ) {
        $advertencia = 'El docente seleccionado también es el tutor principal del expediente. Confirma la decisión para continuar.';
    } else {
        try {
            $pdo->beginTransaction();

            $resultado = $modeloTribunal->asignar(
                $idExpediente,
                $etapaActual,
                $idTutor,
                $orden,
                (int) $usuarioSesion['id_usuario']
            );

            if (!$resultado) {
                throw new RuntimeException(
                    'No fue posible crear la asignación.'
                );
            }

            $idTribunal = (int) $pdo->lastInsertId();

            $modeloBitacora->registrar(
                (int) $usuarioSesion['id_usuario'],
                'asignar_tribunal',
                'tribunales_defensa',
                $idTribunal > 0
                    ? $idTribunal
                    : null,
                null,
                [
                    'id_expediente' => $idExpediente,
                    'etapa' => $etapaActual,
                    'id_tutor' => $idTutor,
                    'orden' => $orden,
                    'estado' => 'vigente'
                ]
            );

            $pdo->commit();

            header(
                'Location: tribunales_gestionar.php?id='
                . $idExpediente
                . '&estado=asignado'
            );
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log(
                'Error al asignar el tribunal: '
                . $e->getMessage()
            );

            if (
                $e instanceof PDOException
                && $e->getCode() === '23000'
            ) {
                $error = 'El docente o la posición seleccionada ya se encuentra asignada.';
            } else {
                $error = 'No fue posible registrar el tribunal.';
            }
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Asignar tribunal';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tribunales/asignar.php';