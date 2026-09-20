<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutorModel.php';
require_once __DIR__.'/../models/TutorMateriaModel.php';

$tutorId = validarId($_GET['id'] ?? $_POST['id_tutor'] ?? null);
if (!$tutorId) {
    redirect('tutores_listar.php');
}

$tm = new TutorMateriaModel($pdo);
$tutor = (new TutorModel($pdo))->obtenerPorId($tutorId);
if (!$tutor) {
    redirect('tutores_listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $mId = validarId($_POST['id_materia'] ?? null);
    if (!$mId) {
        flash('danger', 'Materia inválida.');
        redirect('tutor_materias.php?id='.$tutorId);
    }

    try {
        if (isset($_POST['agregar'])) {
            if (!$tm->tutorActivo($tutorId)) {
                throw new RuntimeException('No se pueden asignar materias a un tutor inactivo.');
            }
            if (!$tm->materiaActiva($mId)) {
                throw new RuntimeException('Solo se pueden asignar materias activas de carreras activas.');
            }
            $tm->asignar($tutorId, $mId);
            registrarAccion($pdo, 'ASIGNAR', 'Tutor-Materia', 'Se asignó una materia al tutor #'.$tutorId.'.');
            flash('success', 'Materia asignada correctamente.');
        } elseif (isset($_POST['quitar'])) {
            $tm->quitar($tutorId, $mId);
            registrarAccion($pdo, 'QUITAR', 'Tutor-Materia', 'Se retiró una materia del tutor #'.$tutorId.'.');
            flash('success', 'Asignación retirada.');
        }
    } catch (RuntimeException $e) {
        flash('danger', $e->getMessage());
    } catch (PDOException $e) {
        flash('danger', 'La relación ya existe o no es válida.');
    }

    redirect('tutor_materias.php?id='.$tutorId);
}

$asignadas = $tm->porTutor($tutorId);
$disponibles = $tm->materiasNoAsignadas($tutorId);
$tituloPagina = 'Materias del Tutor - Sistema de Tutorías';
require __DIR__.'/../views/tutores/materias.php';
