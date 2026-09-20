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
$errores = [];
$datos = [
    'id_usuario' => $_POST['id_usuario'] ?? '',
    'id_carrera' => $_POST['id_carrera'] ?? '',
    'semestre' => $_POST['semestre'] ?? '',
    'registro_universitario' => strtoupper(trim((string)($_POST['registro_universitario'] ?? ''))),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    if (!validarId($datos['id_usuario']) || !$m->usuarioEsEstudianteActivo((int)$datos['id_usuario'])) {
        $errores[] = 'Selecciona un usuario con rol estudiante y activo.';
    }

    $idCarrera = validarId($datos['id_carrera']);
    $carrera = $idCarrera ? $c->obtenerPorId($idCarrera) : false;
    if (!$idCarrera || !$carrera || ($carrera['estado'] ?? 'inactivo') !== 'activo') {
        $errores[] = 'Selecciona una carrera activa y válida.';
    }

    if (!filter_var($datos['semestre'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]])) {
        $errores[] = 'El semestre debe estar entre 1 y 12.';
    }

    if ($datos['registro_universitario'] !== '' && !preg_match('/^[A-Z0-9-]{3,30}$/', $datos['registro_universitario'])) {
        $errores[] = 'El registro universitario debe tener entre 3 y 30 caracteres alfanuméricos o guiones.';
    }

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Estudiantes', 'Se registró un perfil de estudiante.');
            flash('success', 'Estudiante registrado correctamente.');
            redirect('estudiantes_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo registrar: el usuario o registro universitario puede estar ya asociado.';
        }
    }
}

$usuarios = $m->usuariosDisponibles();
$carreras = $c->obtenerActivas();
$tituloPagina = 'Nuevo Estudiante - Sistema de Tutorías';
require __DIR__.'/../views/estudiantes/form.php';
