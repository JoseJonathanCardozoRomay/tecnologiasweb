<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CalendarioMgModel.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.calendario.editar',
    '../index.php'
);

$modeloCalendario = new CalendarioMgModel($pdo);
$modeloCohorte = new CohorteMgModel($pdo);

// El identificador llega por GET al abrir y por POST al guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idHito = filter_input(
        INPUT_POST,
        'id_hito',
        FILTER_VALIDATE_INT
    );
} else {
    $idHito = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idHito) {
    header(
        'Location: calendario_listar.php?estado=no_encontrado'
    );
    exit;
}

$hito = $modeloCalendario->buscarPorId(
    $idHito
);

if (!$hito) {
    header(
        'Location: calendario_listar.php?estado=no_encontrado'
    );
    exit;
}

$cohortes = $modeloCohorte->listar();

$idCohorte = (int) $hito['id_cohorte'];
$etapa = $hito['etapa'];
$tipo = $hito['tipo'];
$nombre = $hito['nombre'];
$orden = (int) $hito['orden'];
$fechaLimite = $hito['fecha_limite'];

$avanceEsperado = $hito['avance_esperado_pct'] !== null
    ? (int) $hito['avance_esperado_pct']
    : null;

$error = '';

$etapasPermitidas = [
    'previa',
    'mg1',
    'mg2'
];

$tiposPermitidos = [
    'taller',
    'asignacion_tutor',
    'asignacion_tribunal',
    'informe',
    'defensa',
    'ingreso_mg2',
    'otro'
];

// Comprueba el formato real de una fecha
$esFechaValida = static function (
    string $fecha
): bool {
    $fechaConvertida = DateTime::createFromFormat(
        '!Y-m-d',
        $fecha
    );

    return $fechaConvertida !== false
        && $fechaConvertida->format('Y-m-d') === $fecha;
};

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCohorte = filter_input(
        INPUT_POST,
        'id_cohorte',
        FILTER_VALIDATE_INT
    );

    $orden = filter_input(
        INPUT_POST,
        'orden',
        FILTER_VALIDATE_INT
    );

    $etapaRecibida = $_POST['etapa'] ?? '';
    $tipoRecibido = $_POST['tipo'] ?? '';
    $nombreRecibido = $_POST['nombre'] ?? '';
    $fechaRecibida = $_POST['fecha_limite'] ?? '';
    $avanceRecibido = $_POST['avance_esperado_pct'] ?? '';

    $etapa = is_string($etapaRecibida)
        ? $etapaRecibida
        : '';

    $tipo = is_string($tipoRecibido)
        ? $tipoRecibido
        : '';

    $nombre = is_string($nombreRecibido)
        ? trim($nombreRecibido)
        : '';

    $fechaLimite = is_string($fechaRecibida)
        ? trim($fechaRecibida)
        : '';

    $avanceTexto = is_string($avanceRecibido)
        ? trim($avanceRecibido)
        : '';

    if ($avanceTexto === '') {
        $avanceEsperado = null;
    } else {
        $avanceEsperado = filter_var(
            $avanceTexto,
            FILTER_VALIDATE_INT
        );
    }

    $cohorteSeleccionada = $idCohorte
        ? $modeloCohorte->buscarPorId($idCohorte)
        : null;

    if (!validarTokenCsrf()) {
        http_response_code(403);

        $error = 'La solicitud no superó la validación de seguridad.';
    } elseif (!$cohorteSeleccionada) {
        $error = 'Selecciona una cohorte válida.';
    } elseif (
        !in_array(
            $etapa,
            $etapasPermitidas,
            true
        )
    ) {
        $error = 'Selecciona una etapa válida.';
    } elseif (
        !in_array(
            $tipo,
            $tiposPermitidos,
            true
        )
    ) {
        $error = 'Selecciona un tipo de hito válido.';
    } elseif (
        mb_strlen($nombre) < 3
        || mb_strlen($nombre) > 150
    ) {
        $error = 'El nombre debe contener entre 3 y 150 caracteres.';
    } elseif (
        $orden === false
        || $orden === null
        || $orden < 1
        || $orden > 999
    ) {
        $error = 'El orden debe estar entre 1 y 999.';
    } elseif (!$esFechaValida($fechaLimite)) {
        $error = 'Selecciona una fecha límite válida.';
    } elseif (
        $fechaLimite
        < $cohorteSeleccionada['fecha_inicio']
    ) {
        $error = 'La fecha límite no puede ser anterior al inicio de la cohorte.';
    } elseif (
        !empty($cohorteSeleccionada['fecha_fin'])
        && $fechaLimite
            > $cohorteSeleccionada['fecha_fin']
    ) {
        $error = 'La fecha límite no puede ser posterior al cierre de la cohorte.';
    } elseif (
        $avanceTexto !== ''
        && (
            $avanceEsperado === false
            || $avanceEsperado < 0
            || $avanceEsperado > 100
        )
    ) {
        $error = 'El avance esperado debe estar entre 0 y 100.';
    } elseif (
        $modeloCalendario->existeOrden(
            $idCohorte,
            $etapa,
            $orden,
            $idHito
        )
    ) {
        $error = 'Ya existe otro hito con ese orden dentro de la etapa seleccionada.';
    } else {
        try {
            $modeloCalendario->actualizar(
                $idHito,
                $idCohorte,
                $etapa,
                $tipo,
                $nombre,
                $orden,
                $fechaLimite,
                $avanceEsperado !== false
                    ? $avanceEsperado
                    : null
            );

            header(
                'Location: calendario_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar el hito.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar hito';
$rutaBase = '../';

require_once __DIR__
    . '/../views/calendario/editar.php';