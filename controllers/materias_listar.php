<?php
/**
 * Listado de Materias
 * Permisos: Admin/Tutor/Estudiante pueden ver
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);

require_once __DIR__ . '/../models/MateriaModel.php';
$modelo = new MateriaModel();

// ✅ Usa el nombre EXACTO que tiene tu modelo
if (method_exists($modelo, 'listarTodas')) {
    $materias = $modelo->listarTodas();
} else {
    $materias = $modelo->listarTodos();
}

require_once __DIR__ . '/../views/materias/listar.php';