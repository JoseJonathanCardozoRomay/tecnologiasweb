<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$tutorModel = new TutorModel($pdo);
$tutor = $tutorModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));

if (!$tutor) {
    flash_set('error', 'Todavía no tienes un perfil de tutor.');
    header('Location: /views/tutor/panel.php');
    exit;
}

$tutoriaModel = new TutoriaModel($pdo);
$informeModel = new InformeModel($pdo);

$id_tutoria = (int) ($_GET['id_tutoria'] ?? 0);
$errores = [];

$tutoriasProceso = array_filter(
    $tutoriaModel->obtenerPorTutor((int) $tutor['id_tutor']),
    fn($t) => in_array($t['estado'], ['aceptada', 'en_proceso'], true)
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $id_tutoria        = (int) ($_POST['id_tutoria'] ?? 0);
    $numero_informe    = (int) ($_POST['numero_informe'] ?? 0);
    $porcentaje        = (int) ($_POST['porcentaje_avance'] ?? 0);
    $descripcion       = trim($_POST['descripcion_avance'] ?? '');
    $fecha_limite      = trim($_POST['fecha_limite'] ?? '');

    $tutoria = $tutoriaModel->obtenerPorId($id_tutoria);
    if (!$tutoria || (int) $tutoria['id_tutor'] !== (int) $tutor['id_tutor']) {
        $errores[] = 'La tutoría seleccionada no existe o no es tuya.';
    } elseif (!in_array($tutoria['estado'], ['aceptada', 'en_proceso'], true)) {
        $errores[] = 'Solo puedes registrar informes de tutorías aceptadas o en proceso.';
    }

    if (empty($errores)) {
        try {
            $informeModel->registrar($id_tutoria, $numero_informe, $porcentaje, $descripcion, $fecha_limite ?: null);
            $historial = new HistorialModel($pdo);
            $historial->registrar((int) ($_SESSION['id_usuario'] ?? 0), 'INFORME_REGISTRADO', 'Informe #' . $numero_informe . ' de avance sobre tutoría #' . $id_tutoria);
            flash_set('success', 'Informe de avance registrado correctamente.');
            header('Location: informes_registrar.php?id_tutoria=' . $id_tutoria);
            exit;
        } catch (InvalidArgumentException $e) {
            $errores[] = $e->getMessage();
        } catch (RuntimeException $e) {
            $errores[] = $e->getMessage();
        }
    }
}

$informes = [];
$porcentajeAcumulado = 0;
$totalRegistrados = 0;
if ($id_tutoria > 0) {
    $informes = $informeModel->obtenerPorTutoria($id_tutoria);
    $resumen = $informeModel->porcentajeAcumulado($id_tutoria);
    $porcentajeAcumulado = (int) ($resumen['porcentaje'] ?? 0);
    $totalRegistrados = (int) ($resumen['total_informes'] ?? 0);
}

require_once __DIR__ . '/../views/tutor/informes.php';