<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AlertaMgModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.alertas.atender',
    '../index.php'
);

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !validarTokenCsrf()
) {
    header(
        'Location: alertas_listar.php?estado=error'
    );
    exit;
}

$tipo = trim(
    $_POST['tipo_alerta'] ?? ''
);

$clave = trim(
    $_POST['clave_alerta'] ?? ''
);

$idReferencia = filter_input(
    INPUT_POST,
    'id_referencia',
    FILTER_VALIDATE_INT
);

$nota = trim(
    $_POST['nota'] ?? ''
);

if (
    $tipo === ''
    || $clave === ''
    || !$idReferencia
    || mb_strlen($tipo) > 60
    || mb_strlen($clave) > 150
    || mb_strlen($nota) < 5
    || mb_strlen($nota) > 500
) {
    header(
        'Location: alertas_listar.php?estado=error'
    );
    exit;
}

$modeloAlerta = new AlertaMgModel($pdo);
$modeloBitacora = new BitacoraModel($pdo);

// Verificamos que la alerta realmente exista y siga activa
$alertasActuales = $modeloAlerta->calcular();
$alertaValida = null;

foreach ($alertasActuales as $alerta) {
    if (
        hash_equals(
            $alerta['clave'],
            $clave
        )
        && $alerta['tipo'] === $tipo
        && (int) $alerta['id_referencia']
            === $idReferencia
    ) {
        $alertaValida = $alerta;
        break;
    }
}

if (!$alertaValida) {
    header(
        'Location: alertas_listar.php?estado=no_encontrada'
    );
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

try {
    $resultado = $modeloAlerta->marcarAtendida(
        $tipo,
        $idReferencia,
        $clave,
        $nota,
        $idUsuario
    );

    if (!$resultado) {
        throw new RuntimeException(
            'No fue posible registrar la atención de la alerta.'
        );
    }

    $idAtencion = (int) $pdo->lastInsertId();

    $modeloBitacora->registrar(
        $idUsuario,
        'atender_alerta',
        'alertas_atendidas',
        $idAtencion > 0
            ? $idAtencion
            : null,
        null,
        [
            'tipo_alerta' => $tipo,
            'id_referencia' => $idReferencia,
            'clave_alerta' => $clave,
            'nota' => $nota
        ]
    );

    header(
        'Location: alertas_listar.php?estado=atendida'
    );
    exit;
} catch (Throwable $e) {
    error_log(
        'Error al atender la alerta: '
        . $e->getMessage()
    );

    header(
        'Location: alertas_listar.php?estado=error'
    );
    exit;
}