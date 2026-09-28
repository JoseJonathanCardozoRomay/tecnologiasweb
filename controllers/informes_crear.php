<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../models/InformeAvanceModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.informes.crear_propios',
    '../index.php'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idExpediente = filter_input(
        INPUT_POST,
        'id_expediente',
        FILTER_VALIDATE_INT
    );
} else {
    $idExpediente = filter_input(
        INPUT_GET,
        'expediente',
        FILTER_VALIDATE_INT
    );
}

if (!$idExpediente) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloAsignacion = new AsignacionTutorModel($pdo);
$modeloInforme = new InformeAvanceModel($pdo);

$tutorado = $modeloAsignacion->buscarTutorado(
    $idExpediente,
    $idUsuario
);

if (!$tutorado) {
    header('Location: tutorados_listar.php');
    exit;
}

$etapaActual = $tutorado['etapa_actual'];

if (
    !in_array(
        $etapaActual,
        ['mg1', 'mg2'],
        true
    )
) {
    header(
        'Location: informes_listar.php?expediente='
        . $idExpediente
        . '&estado=error'
    );
    exit;
}

$numeroInforme = $modeloInforme->obtenerSiguienteNumero(
    $idExpediente,
    $etapaActual
);

$hitos = array_values(
    array_filter(
        $modeloInforme->listarHitosDisponibles(
            $idExpediente
        ),
        fn(array $hito): bool =>
            $hito['etapa'] === $etapaActual
    )
);

$idHito = null;
$fechaInforme = date('Y-m-d');
$porcentajeAvance = 0;
$resumenAvance = '';
$logros = '';
$dificultades = '';
$recomendaciones = '';
$proximasActividades = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idHito = filter_input(
        INPUT_POST,
        'id_hito',
        FILTER_VALIDATE_INT
    );

    if (!$idHito) {
        $idHito = null;
    }

    $fechaInforme = trim(
        $_POST['fecha_informe'] ?? ''
    );

    $porcentajeRecibido = filter_input(
        INPUT_POST,
        'porcentaje_avance',
        FILTER_VALIDATE_INT
    );

    $porcentajeAvance = (
        $porcentajeRecibido !== false
        && $porcentajeRecibido !== null
    )
        ? $porcentajeRecibido
        : -1;

    $resumenAvance = trim(
        $_POST['resumen_avance'] ?? ''
    );

    $logros = trim(
        $_POST['logros'] ?? ''
    );

    $dificultades = trim(
        $_POST['dificultades'] ?? ''
    );

    $recomendaciones = trim(
        $_POST['recomendaciones'] ?? ''
    );

    $proximasActividades = trim(
        $_POST['proximas_actividades'] ?? ''
    );

    $fechaValida = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $fechaInforme
    );

    $fechaCorrecta = (
        $fechaValida !== false
        && $fechaValida->format('Y-m-d') === $fechaInforme
    );

    $idsHitosValidos = array_map(
        fn(array $hito): int =>
            (int) $hito['id_hito'],
        $hitos
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif (
        $numeroInforme < 1
        || $numeroInforme > 20
    ) {
        $error = 'La etapa alcanzó el máximo de informes permitidos.';
    } elseif (
        $idHito !== null
        && !in_array(
            $idHito,
            $idsHitosValidos,
            true
        )
    ) {
        $error = 'Selecciona un hito válido.';
    } elseif (!$fechaCorrecta) {
        $error = 'Selecciona una fecha válida.';
    } elseif (
        $fechaValida > new DateTimeImmutable('today')
    ) {
        $error = 'La fecha del informe no puede ser futura.';
    } elseif (
        $porcentajeAvance < 0
        || $porcentajeAvance > 100
    ) {
        $error = 'El porcentaje de avance debe estar entre 0 y 100.';
    } elseif (
        mb_strlen($resumenAvance) < 10
        || mb_strlen($resumenAvance) > 5000
    ) {
        $error = 'El resumen debe contener entre 10 y 5000 caracteres.';
    } elseif (
        mb_strlen($logros) > 5000
        || mb_strlen($dificultades) > 5000
        || mb_strlen($recomendaciones) > 5000
        || mb_strlen($proximasActividades) > 5000
    ) {
        $error = 'Los campos de detalle no pueden superar los 5000 caracteres.';
    } else {
        try {
            $modeloInforme->crear(
                $idExpediente,
                $idUsuario,
                $idHito,
                $numeroInforme,
                $fechaInforme,
                $porcentajeAvance,
                $resumenAvance,
                $logros !== ''
                    ? $logros
                    : null,
                $dificultades !== ''
                    ? $dificultades
                    : null,
                $recomendaciones !== ''
                    ? $recomendaciones
                    : null,
                $proximasActividades !== ''
                    ? $proximasActividades
                    : null
            );

            header(
                'Location: informes_listar.php?expediente='
                . $idExpediente
                . '&estado=creado'
            );
            exit;
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'Ya existe un informe con ese número dentro de la etapa.'
                : 'No fue posible guardar el informe.';
        }
    }
}

$tituloPagina = 'Nuevo informe de avance';
$rutaBase = '../';

require_once __DIR__
    . '/../views/informes/crear.php';