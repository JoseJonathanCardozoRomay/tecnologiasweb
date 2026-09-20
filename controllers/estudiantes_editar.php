<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/EstudianteModel.php';
require_once __DIR__.'/../models/CarreraModel.php';

$m = new EstudianteModel($pdo);
$c = new CarreraModel($pdo);
$id = validarId($_GET['id'] ?? $_POST['id_estudiante'] ?? null);
if (!$id) {
    redirect('estudiantes_listar.php');
}

$actual = $m->obtenerPorId($id);
if (!$actual) {
    redirect('estudiantes_listar.php');
}

$errores = [];
$datos = $actual;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos = [
        'id_usuario' => $_POST['id_usuario'] ?? '',
        'id_carrera' => $_POST['id_carrera'] ?? '',
        'semestre' => $_POST['semestre'] ?? '',
        'registro_universitario' => strtoupper(trim((string)($_POST['registro_universitario'] ?? ''))),
    ];

    if (!validarId($datos['id_usuario']) || !$m->usuarioEsEstudianteActivo((int)$datos['id_usuario'])) {
        $errores[] = 'El usuario seleccionado debe tener rol estudiante y estar activo.';
    }

    $idCarrera = validarId($datos['id_carrera']);
    $carrera = $idCarrera ? $c->obtenerPorId($idCarrera) : false;
    if (!$idCarrera || !$carrera) {
        $errores[] = 'Selecciona una carrera válida.';
    } elseif (($carrera['estado'] ?? 'inactivo') !== 'activo' && (int)$actual['id_carrera'] !== $idCarrera) {
        $errores[] = 'La nueva carrera debe estar activa.';
    }

    if (!filter_var($datos['semestre'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]])) {
        $errores[] = 'El semestre debe estar entre 1 y 12.';
    }

    if ($datos['registro_universitario'] !== '' && !preg_match('/^[A-Z0-9-]{3,30}$/', $datos['registro_universitario'])) {
        $errores[] = 'El registro universitario no tiene un formato válido.';
    }

    if (!$errores) {
        try {
            $m->actualizar($id, $datos);
            registrarAccion($pdo, 'EDITAR', 'Estudiantes', 'Se actualizó el estudiante #' . $id . '.');
            flash('success', 'Estudiante actualizado correctamente.');
            redirect('estudiantes_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo actualizar el estudiante. Comprueba que el registro universitario no esté duplicado.';
        }
    }
}

$usuarios = $m->usuariosDisponibles((int)$actual['id_usuario']);
$carreras = $c->obtenerActivas((int)$actual['id_carrera']);
$tituloPagina = 'Editar Estudiante - Sistema de Tutorías';
require __DIR__.'/../views/estudiantes/form.php';
