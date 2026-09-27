<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AccesosModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$accesosModel = new AccesosModel($pdo);
$accesos = $accesosModel->obtenerTodos();
$accesos = filtrarRegistros($accesos, $p['q'], ['usuario', 'nombre', 'apellido', 'ip_origen']);
$accesos = filtrarPorEstado($accesos, $p['estado'], 'resultado');
$accesos = ordenarRegistros($accesos, $p['orden'], $p['dir'], [
    'fecha'     => 'fecha_hora',
    'resultado' => 'resultado',
]);
if ($p['orden'] === '') {
    usort($accesos, function ($a, $b) {
        return strcmp($b['fecha_hora'] ?? '', $a['fecha_hora'] ?? '');
    });
}
$totalExitosos = count(array_filter($accesos, fn($a) => (($a['resultado'] ?? '') === 'exitoso')));
$totalFallidos = count(array_filter($accesos, fn($a) => (($a['resultado'] ?? '') === 'fallido')));
[$accesos, $pag] = paginarRegistros($accesos, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$resultado = $p['estado'];
$q = $p['q'];
$resultados = $accesosModel->resultados();
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/admin/accesos.php';