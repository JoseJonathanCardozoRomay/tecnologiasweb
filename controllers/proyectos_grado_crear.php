<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$m = new ProyectoGradoModel($pdo);
$em = new EstudianteModel($pdo);
$errores = [];
$propio = $em->obtenerPorUsuario((int)$_SESSION['id_usuario']);

$datos = [
    'id_estudiante' => $_POST['id_estudiante'] ?? ($propio['id_estudiante'] ?? ''),
    'id_carrera' => $_POST['id_carrera'] ?? ($propio['id_carrera'] ?? ''),
    'titulo' => normalizarTexto((string)($_POST['titulo'] ?? '')),
    'descripcion' => normalizarTexto((string)($_POST['descripcion'] ?? '')),
    'estado' => 'propuesto',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_estudiante'] = validarId($datos['id_estudiante']);
    $datos['id_carrera'] = validarId($datos['id_carrera']);
    $datos['titulo'] = normalizarTexto((string)($_POST['titulo'] ?? ''));
    $datos['descripcion'] = normalizarTexto((string)($_POST['descripcion'] ?? ''));

    if (!$datos['id_estudiante'] || !$datos['id_carrera']) {
        $errores[] = 'Debes seleccionar un estudiante y una carrera válidos.';
    }
    if (!textoValido($datos['titulo'], 5, 200)) {
        $errores[] = 'El título debe tener entre 5 y 200 caracteres.';
    }
    if (!textoValido($datos['descripcion'], 0, 3000)) {
        $errores[] = 'La descripción no puede superar 3000 caracteres.';
    }
    if (esEstudiante() && (!$propio || (int)$datos['id_estudiante'] !== (int)$propio['id_estudiante'])) {
        $errores[] = 'No puedes registrar un proyecto para otro estudiante.';
    }
    if (!$errores && !$m->estudiantePerteneceACarrera((int)$datos['id_estudiante'], (int)$datos['id_carrera'])) {
        $errores[] = 'La carrera seleccionada no corresponde al estudiante.';
    }
    if (!$errores && $m->existeTitulo($datos['titulo'], (int)$datos['id_carrera'])) {
        $errores[] = 'Ya existe un proyecto con ese título dentro de la carrera seleccionada.';
    }

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Proyectos de grado', 'Se registró el proyecto "'.$datos['titulo'].'".');
            flash('success', 'Proyecto de grado registrado correctamente.');
            redirect('proyectos_grado_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo registrar el proyecto. Verifica los datos e inténtalo nuevamente.';
        }
    }
}

$estudiantes = $propio ? [$propio] : [];
$carreras = [];
if ($propio) {
    $stmtCarrera = $pdo->prepare("SELECT id_carrera,nombre_carrera FROM carreras WHERE id_carrera=:id AND estado='activo'");
    $stmtCarrera->execute([':id' => $propio['id_carrera']]);
    $carreras = $stmtCarrera->fetchAll();
}

$tituloPagina = 'Nuevo Proyecto de Grado - Sistema de Tutorías';
require __DIR__.'/../views/proyectos_grado/form.php';
