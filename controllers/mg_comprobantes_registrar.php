<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/ComprobanteModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$estudianteModel = new EstudianteModel($pdo);
$estudiante = $estudianteModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));

if (!$estudiante) {
    flash_set('error', 'Tu perfil de estudiante aún no está completo. Contacta al administrador.');
    header('Location: ../views/estudiante/panel.php');
    exit;
}

$configuracion = new ConfiguracionModel($pdo);
$comprobanteModel = new ComprobanteModel($pdo);

$materiasRequeridas = $configuracion->int('materias_requeridas_mg', 54);
$semestresRequeridos = $configuracion->int('semestres_requeridos_mg', 9);
$materiasActuales = (int) ($estudiante['materias_completadas'] ?? 0);
$semestreActual = (int) ($estudiante['semestre'] ?? 0);

$reporteElegibilidad = [
    'materias'   => ['ok' => $materiasActuales >= $materiasRequeridas, 'actual' => $materiasActuales, 'requerido' => $materiasRequeridas],
    'semestres'  => ['ok' => $semestreActual >= $semestresRequeridos, 'actual' => $semestreActual, 'requerido' => $semestresRequeridos],
    'comprobante' => ['ok' => $comprobanteModel->tieneAprobado((int) $estudiante['id_estudiante'])],
    'desbloqueo' => ['ok' => (int) ($estudiante['acceso_mg_desbloqueado'] ?? 0) === 1],
];

$puedeSubirComprobante = $reporteElegibilidad['materias']['ok'] && $reporteElegibilidad['semestres']['ok'];
$accesoMG = $estudianteModel->puedeAccederMG((int) $estudiante['id_estudiante'], $configuracion);
$tutoriaModel = new TutoriaModel($pdo);
$mgCompletada = $tutoriaModel->mgCompletada((int) $estudiante['id_estudiante']);
$mgConcluida = $tutoriaModel->mgConcluida((int) $estudiante['id_estudiante']);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    if (!empty($mgCompletada)) {
        $errores[] = 'Tu Modalidad de Grado ya finalizó; no se aceptan nuevos comprobantes.';
    }

    if (!$puedeSubirComprobante) {
        $errores[] = 'Aún no cumples los requisitos de materias y semestres para Modalidad de Grado.';
    }

    $monto = (float) ($_POST['monto'] ?? 0);
    $fecha_pago = trim($_POST['fecha_pago'] ?? '');
    $archivo = $_FILES['comprobante_archivo'] ?? null;

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_pago)) {
        $errores[] = 'La fecha de pago no es válida.';
    }

    $ruta = null;
    if (empty($errores) && $archivo) {
        try {
            $ruta = procesarComprobante($archivo);
            $comprobanteModel->registrar(
                (int) $estudiante['id_estudiante'],
                $monto,
                $fecha_pago,
                $ruta
            );
            $historial = new HistorialModel($pdo);
            $historial->registrar(
                (int) ($_SESSION['id_usuario'] ?? 0),
                'COMPROBANTE_MG',
                'Registro de comprobante de pago para Modalidad de Grado.'
            );
            $notifs = new NotificationModel($pdo);
            foreach ($notifs->idsUsuariosAdministracion() as $dest) {
                $notifs->crear($dest, 'COMPROBANTE_MG', 'Nuevo comprobante de pago de Modalidad de Grado pendiente de validación.', 'mg_comprobantes_listar.php', (int) ($_SESSION['id_usuario'] ?? 0));
            }
            flash_set('success', 'Comprobante registrado. Espera la validación de administración.');
            header('Location: mg_comprobantes_registrar.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $errores[] = $e->getMessage();
            if ($ruta) {
                @unlink(__DIR__ . '/../' . $ruta);
            }
        } catch (RuntimeException $e) {
            $errores[] = $e->getMessage();
            if ($ruta) {
                @unlink(__DIR__ . '/../' . $ruta);
            }
        }
    } elseif (empty($errores)) {
        $errores[] = 'Debes adjuntar el comprobante de pago.';
    }
}

$comprobantes = $comprobanteModel->obtenerPorEstudiante((int) $estudiante['id_estudiante']);

require_once __DIR__ . '/../views/estudiante/modalidad_grado.php';

function procesarComprobante($archivo)
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Error al subir el comprobante.');
    }

    if ($archivo['size'] > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('El archivo no puede superar los 5MB.');
    }

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
        throw new InvalidArgumentException('El comprobante debe ser JPG, PNG o PDF.');
    }

    $carpeta = __DIR__ . '/../uploads/comprobantes/';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }

    $nombreUnico = 'comprobante_mg_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreUnico)) {
        throw new RuntimeException('No se pudo guardar el comprobante.');
    }

    return 'uploads/comprobantes/' . $nombreUnico;
}