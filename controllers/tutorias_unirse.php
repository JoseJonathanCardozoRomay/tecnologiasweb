<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$estudianteModel = new EstudianteModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

$estudiante = $estudianteModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
if (!$estudiante) {
    flash_set('error', 'Tu perfil de estudiante aún no está completo. Contacta al administrador.');
    header('Location: ../views/estudiante/panel.php');
    exit;
}

if ($tutoriaModel->mgCompletada((int) $estudiante['id_estudiante'])) {
    flash_set('error', 'Tu Modalidad de Grado ya fue completada. No es posible agregar nuevas tutorías.');
    header('Location: ../views/estudiante/panel.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $id_tutoria = (int) ($_POST['id_tutoria'] ?? 0);
    if (!$id_tutoria) {
        $errores[] = 'La tutoría seleccionada no es válida.';
    }

    if (empty($errores)) {
        try {
            $tutoria = $tutoriaModel->obtenerPorId($id_tutoria);
            if (!$tutoria || $tutoria['tipo'] !== 'apoyo') {
                $errores[] = 'La tutoría no existe o no es de apoyo.';
            } else {
                $tutoriaModel->inscribirEstudiante($id_tutoria, (int) $estudiante['id_estudiante']);
                $historial = new HistorialModel($pdo);
                $historial->registrar(
                    (int) ($_SESSION['id_usuario'] ?? 0),
                    'TUTORIA_UNIDO',
                    'El estudiante se unió a la tutoría de apoyo #' . $id_tutoria
                );
                flash_set('success', 'Te uniste a la tutoría de apoyo correctamente.');
                header('Location: ../views/estudiante/panel.php');
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errores[] = $e->getMessage();
        } catch (Throwable $e) {
            $errores[] = 'Ocurrió un error al unirte a la tutoría.';
        }
    }
}

$p = parametrosListado();
$tutorias = $tutoriaModel->obtenerApoyoAbiertas(null, (int) $estudiante['id_carrera']);
$tutorias = filtrarRegistros($tutorias, $p['q'], ['nombre_materia', 'tutor_nombre', 'tutor_apellido', 'especialidad']);
[$tutorias, $pag] = paginarRegistros($tutorias, $p['pagina'], $p['por_pagina']);
$q = $p['q'];
$totalRegistros = $pag['total'];

require_once __DIR__ . '/../views/tutorias/unirse.php';