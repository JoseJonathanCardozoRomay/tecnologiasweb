<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.cohortes.editar',
    '../index.php'
);

// El cambio de estado solo se permite mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cohortes_listar.php');
    exit;
}

if (!validarTokenCsrf()) {
    http_response_code(403);

    exit(
        'La solicitud no superó la validación de seguridad.'
    );
}

$modeloCohorte = new CohorteMgModel($pdo);

$idCohorte = filter_input(
    INPUT_POST,
    'id_cohorte',
    FILTER_VALIDATE_INT
);

$estadoRecibido = $_POST['activa'] ?? null;

if (
    !$idCohorte
    || !is_string($estadoRecibido)
    || !in_array(
        $estadoRecibido,
        ['0', '1'],
        true
    )
) {
    header(
        'Location: cohortes_listar.php?estado=no_encontrado'
    );
    exit;
}

$cohorte = $modeloCohorte->buscarPorId(
    $idCohorte
);

if (!$cohorte) {
    header(
        'Location: cohortes_listar.php?estado=no_encontrado'
    );
    exit;
}

$activar = $estadoRecibido === '1';

try {
    $modeloCohorte->cambiarEstado(
        $idCohorte,
        $activar
    );

    $estadoResultado = $activar
        ? 'activado'
        : 'desactivado';

    header(
        'Location: cohortes_listar.php?estado='
        . $estadoResultado
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: cohortes_listar.php?estado=error'
    );
    exit;
}