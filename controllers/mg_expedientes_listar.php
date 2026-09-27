<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$p = parametrosListado();

$expedienteModel = new MgExpedienteModel($pdo);
$expedientes = $expedienteModel->obtenerTodas();
$expedientes = filtrarRegistros($expedientes, $p['q'], [
    'estudiante', 'usuario_estudiante', 'modalidad', 'cohorte',
]);
$expedientes = filtrarPorEstado($expedientes, $p['estado'], 'estado');

$filtroCohorte = limpiarTexto($_GET['cohorte'] ?? '', 30);
if ($filtroCohorte !== '') {
    $expedientes = array_values(array_filter($expedientes, function ($e) use ($filtroCohorte) {
        return (string) $e['cohorte'] === $filtroCohorte;
    }));
}

$expedientes = ordenarRegistros($expedientes, $p['orden'], $p['dir'], [
    'id' => 'id_expediente_mg',
    'estudiante' => 'estudiante',
    'modalidad' => 'modalidad',
    'cohorte' => 'cohorte',
    'fecha' => 'fecha_solicitud',
]);
[$expedientes, $pag] = paginarRegistros($expedientes, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$estado = $p['estado'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];
$cohortes = (new MgCohorteModel($pdo))->obtenerTodas();

require_once __DIR__ . '/../views/mg/expedientes/listar.php';