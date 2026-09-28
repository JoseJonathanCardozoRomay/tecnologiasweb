<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/InformeAvanceModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.informes.editar_propios',
    '../index.php'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idInforme = filter_input(
        INPUT_POST,
        'id_informe',
        FILTER_VALIDATE_INT
    );
} else {
    $idInforme = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idInforme) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloInforme = new InformeAvanceModel($pdo);

$informe = $modeloInforme->buscarPorIdParaTutor(
    $idInforme,
    $idUsuario
);

if (!$informe) {
    header('Location: tutorados_listar.php');
    exit;
}

if (
    !in_array(
        $informe['estado'],
        ['borrador', 'observado'],
        true
    )
) {
    header(
        'Location: informe_ver.php?id='
        . $idInforme
    );
    exit;
}

$idExpediente = (int) $informe['id_expediente'];
$etapaInforme = $informe['etapa'];

$hitos = array_values(
    array_filter(
        $modeloInforme->listarHitosDisponibles(
            $idExpediente
        ),
        fn(array $hito): bool =>
            $hito['etapa'] === $etapaInforme
    )
);

$idHito = $informe['id_hito'] !== null
    ? (int) $informe['id_hito']
    : null;

$fechaInforme = $informe['fecha_informe'];
$porcentajeAvance = (int) $informe['porcentaje_avance'];
$resumenAvance = $informe['resumen_avance'];
$logros = $informe['logros'] ?? '';
$dificultades = $informe['dificultades'] ?? '';
$recomendaciones = $informe['recomendaciones'] ?? '';
$proximasActividades = $informe['proximas_actividades'] ?? '';
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
        $error = 'El porcentaje debe estar entre 0 y 100.';
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
            $modeloInforme->actualizar(
                $idInforme,
                $idUsuario,
                $idHito,
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
            $error = 'No fue posible actualizar el informe.';
        }
    }
}

$tituloPagina = 'Editar informe';
$rutaBase = '../';

require_once __DIR__
    . '/../views/informes/editar.php';