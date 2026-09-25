<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.modalidades.editar',
    '../index.php'
);

// El cambio de estado solo se permite mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: modalidades_listar.php');
    exit;
}

if (!validarTokenCsrf()) {
    header(
        'Location: modalidades_listar.php?estado=seguridad'
    );
    exit;
}

$modeloModalidad = new ModalidadModel($pdo);

$idModalidad = filter_input(
    INPUT_POST,
    'id_modalidad',
    FILTER_VALIDATE_INT
);

$estadoRecibido = $_POST['activa'] ?? null;

if (
    !$idModalidad
    || !is_string($estadoRecibido)
    || !in_array(
        $estadoRecibido,
        ['0', '1'],
        true
    )
) {
    header(
        'Location: modalidades_listar.php?estado=no_encontrado'
    );
    exit;
}

$modalidad = $modeloModalidad->buscarPorId(
    $idModalidad
);

if (!$modalidad) {
    header(
        'Location: modalidades_listar.php?estado=no_encontrado'
    );
    exit;
}

$activar = $estadoRecibido === '1';

try {
    $modeloModalidad->cambiarEstado(
        $idModalidad,
        $activar
    );

    $estadoResultado = $activar
        ? 'activado'
        : 'desactivado';

    header(
        'Location: modalidades_listar.php?estado='
        . $estadoResultado
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: modalidades_listar.php?estado=error'
    );
    exit;
}