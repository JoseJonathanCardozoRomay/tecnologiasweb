<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$m = new ProyectoGradoModel($pdo);
$em = new EstudianteModel($pdo);
$id = validarId($_GET['id'] ?? $_POST['id_proyecto'] ?? null);
if (!$id) redirect('proyectos_grado_listar.php');

$actual = $m->obtenerPorId($id);
if (!$actual) {
    flash('danger', 'El proyecto de grado no existe.');
    redirect('proyectos_grado_listar.php');
}

$propio = esEstudiante() ? $em->obtenerPorUsuario((int)$_SESSION['id_usuario']) : null;
if (esEstudiante() && (!$propio || (int)$actual['id_estudiante'] !== (int)$propio['id_estudiante'])) {
    http_response_code(403);
    $tituloPagina = 'Acceso no autorizado - Sistema de Tutorías';
    require __DIR__.'/../views/errors/403.php';
    exit;
}

$datos = $actual;
$errores = [];
$estados = ['propuesto','en_proceso','finalizado','cancelado'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['titulo'] = normalizarTexto((string)($_POST['titulo'] ?? ''));
    $datos['descripcion'] = normalizarTexto((string)($_POST['descripcion'] ?? ''));
    $datos['estado'] = esAdministrador() ? (string)($_POST['estado'] ?? $actual['estado']) : $actual['estado'];

    if (!textoValido($datos['titulo'], 5, 200)) $errores[] = 'El título debe tener entre 5 y 200 caracteres.';
    if (!textoValido($datos['descripcion'], 0, 3000)) $errores[] = 'La descripción no puede superar 3000 caracteres.';
    if (!in_array($datos['estado'], $estados, true)) $errores[] = 'Estado de proyecto inválido.';
    if ($m->existeTitulo($datos['titulo'], (int)$actual['id_carrera'], $id)) $errores[] = 'Ya existe otro proyecto con ese título dentro de la carrera.';

    if (!$errores) {
        try {
            $m->actualizar($id, [
                'id_estudiante' => $actual['id_estudiante'],
                'id_carrera' => $actual['id_carrera'],
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'],
                'estado' => $datos['estado'],
            ]);
            registrarAccion($pdo, 'EDITAR', 'Proyectos de grado', 'Se actualizó el proyecto #'.$id.'.');
            flash('success', 'Proyecto actualizado correctamente.');
            redirect('proyectos_grado_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo actualizar el proyecto.';
        }
    }
}

$tituloPagina = 'Editar Proyecto de Grado - Sistema de Tutorías';
require __DIR__.'/../views/proyectos_grado/form.php';
