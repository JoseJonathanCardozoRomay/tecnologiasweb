<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../includes/permisos.php';

// Se necesita permiso de consulta para abrir esta pantalla
requerirPermiso(
    'mg.tribunales.ver',
    '../index.php'
);

$modeloTribunal = new TribunalModel($pdo);

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

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

// Los tribunales solamente corresponden a las etapas MG1 y MG2
if (
    !in_array(
        $expediente['etapa_actual'],
        ['mg1', 'mg2'],
        true
    )
) {
    header(
        'Location: tribunales_listar.php?estado=etapa_invalida'
    );
    exit;
}

$tribunales = $modeloTribunal->listarPorExpediente(
    $idExpediente
);

$tutorPrincipal = $modeloTribunal->buscarTutorPrincipal(
    $idExpediente
);

$puedeAsignar = usuarioTienePermiso(
    'mg.tribunales.asignar'
);

$puedeCambiar = usuarioTienePermiso(
    'mg.tribunales.cambiar'
);

// Mensajes mostrados después de las operaciones
$mensajes = [
    'asignado' => 'El docente fue asignado al tribunal correctamente.',
    'reemplazado' => 'El tribunal fue reemplazado y el cambio quedó registrado.',
    'duplicado' => 'El docente seleccionado ya pertenece al tribunal de esta etapa.',
    'orden_ocupado' => 'La posición seleccionada ya está ocupada.',
    'no_encontrado' => 'No se encontró la asignación solicitada.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'duplicado',
        'orden_ocupado',
        'no_encontrado',
        'error'
    ],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Gestionar tribunales';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tribunales/gestionar.php';