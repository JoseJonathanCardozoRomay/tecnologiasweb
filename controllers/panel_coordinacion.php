<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/PanelCoordinacionModel.php';
require_once __DIR__ . '/../models/AlertaMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

// El panel está disponible para administración y coordinación
requerirPermiso(
    'mg.reportes.ver',
    '../index.php'
);

$modeloPanel = new PanelCoordinacionModel($pdo);
$modeloAlerta = new AlertaMgModel($pdo);

try {
    $resumen = $modeloPanel->obtenerResumen();

    $proximasDefensas =
        $modeloPanel->listarProximasDefensas();

    $expedientesRecientes =
        $modeloPanel->listarExpedientesRecientes();

    // Las alertas se calculan con la información actual
    $alertasActivas = $modeloAlerta->calcular();

    $resumen['alertas_activas'] = count(
        $alertasActivas
    );

    $error = '';
} catch (PDOException $e) {
    error_log(
        'Error al cargar el panel de coordinación: '
        . $e->getMessage()
    );

    $resumen = [
        'expedientes_activos' => 0,
        'expedientes_sin_tutor' => 0,
        'cartas_pendientes' => 0,
        'reuniones_pendientes' => 0,
        'informes_pendientes' => 0,
        'defensas_programadas' => 0,
        'alertas_activas' => 0
    ];

    $proximasDefensas = [];
    $expedientesRecientes = [];

    $error = 'No fue posible cargar toda la información del panel.';
}

// Datos utilizados por la vista
$tituloPagina = 'Panel de coordinación';
$rutaBase = '../';

require_once __DIR__
    . '/../views/coordinacion/panel.php';