<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$materiaModel = new MateriaModel($pdo);
$carreraModel = new CarreraModel($pdo);

$carreras = $carreraModel->obtenerTodas();
$id_carrera = filter_var($_GET['id_carrera'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

$materias = $materiaModel->obtenerTodas();
if ($id_carrera > 0) {
    $materias = array_values(array_filter($materias, function ($materia) use ($id_carrera) {
        return (int) ($materia['id_carrera'] ?? 0) === $id_carrera;
    }));
}
$materias = filtrarRegistros($materias, $p['q'], ['nombre_materia', 'nombre_carrera']);
$materias = ordenarRegistros($materias, $p['orden'], $p['dir'], [
    'id'      => 'id_materia',
    'nombre'  => 'nombre_materia',
    'carrera' => 'nombre_carrera',
    'tutores' => 'total_tutores',
]);
[$materias, $pag] = paginarRegistros($materias, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/materias/listar.php';