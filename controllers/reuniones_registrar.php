<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReunionModel.php';
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
$reunionModel = new ReunionModel($pdo);

$id_tutoria = (int) ($_GET['id_tutoria'] ?? 0);
$errores = [];

$tutorias = $tutoriaModel->obtenerPorTutor((int) $tutor['id_tutor']);
$tutoriasAceptadas = array_values(array_filter(
    $tutorias,
    fn($t) => in_array($t['estado'], ['aceptada', 'en_proceso'], true)
));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $accion = $_POST['accion'] ?? 'registrar';

    if ($accion === 'seguimiento') {
        $id_reunion = (int) ($_POST['id_reunion'] ?? 0);
        $asistencia = $_POST['asistencia'] ?? '';
        $cumplimiento = $_POST['cumplimiento'] ?? '';
        $observaciones = trim($_POST['observaciones'] ?? '');
        $compromisos = trim($_POST['compromisos'] ?? '');

        $reunion = $id_reunion > 0 ? $reunionModel->obtenerPorId($id_reunion) : null;
        if (!$reunion) {
            $errores[] = 'La reunión indicada no existe.';
        } elseif ((int) $reunion['id_usuario_tutor'] !== (int) ($_SESSION['id_usuario'] ?? 0)) {
            http_response_code(403);
            require __DIR__ . '/../views/errores/403.php';
            exit;
        } elseif (!$reunionModel->reunionRealizada($reunion)) {
            $errores[] = 'El seguimiento solo puede registrarse una vez que la reunión se haya realizado.';
        } else {
            try {
                $reunionModel->registrarSeguimiento(
                    $id_reunion,
                    (int) ($_SESSION['id_usuario'] ?? 0),
                    $asistencia,
                    $cumplimiento,
                    $observaciones ?: null,
                    $compromisos ?: null
                );
                (new HistorialModel($pdo))->registrar(
                    (int) ($_SESSION['id_usuario'] ?? 0),
                    'REUNION_SEGUIMIENTO',
                    'Seguimiento registrado en la reunión #' . $id_reunion . '.'
                );
                flash_set('success', 'Seguimiento de la reunión guardado correctamente.');
                header('Location: reuniones_registrar.php' . ($id_tutoria > 0 ? '?id_tutoria=' . $id_tutoria : ''));
                exit;
            } catch (Throwable $e) {
                $errores[] = $e->getMessage();
            }
        }
    } else {
        $id_tutoria = (int) ($_POST['id_tutoria'] ?? 0);
        $fecha       = trim($_POST['fecha'] ?? '');
        $hora_inicio = trim($_POST['hora_inicio'] ?? '');
        $hora_fin    = trim($_POST['hora_fin'] ?? '');
        $lugar       = trim($_POST['lugar_o_enlace'] ?? '');
        $obs         = trim($_POST['observaciones'] ?? '');

        $tutoria = $tutoriaModel->obtenerPorId($id_tutoria);
        if (!$tutoria || (int) $tutoria['id_tutor'] !== (int) $tutor['id_tutor']) {
            $errores[] = 'La tutoría seleccionada no existe o no es tuya.';
        } elseif (!in_array($tutoria['estado'], ['aceptada', 'en_proceso'], true)) {
            $errores[] = 'Solo puedes registrar reuniones de tutorías aceptadas o en proceso.';
        }

        if (!$fecha) {
            $errores[] = 'La fecha de la reunión es obligatoria.';
        }

        $evidencia_url = null;
        if (empty($errores)) {
            try {
                $evidencia_url = procesarEvidencia();
                $reunionModel->registrar(
                    $id_tutoria, $fecha, $hora_inicio, $hora_fin,
                    $lugar ?: null, $obs ?: null, $evidencia_url
                );
                if ($tutoria['estado'] === 'aceptada') {
                    $tutoriaModel->cambiarEstado($id_tutoria, 'en_proceso');
                }
                (new HistorialModel($pdo))->registrar((int) ($_SESSION['id_usuario'] ?? 0), 'REUNION_REGISTRADA', 'Registro de reunión en tutoría #' . $id_tutoria);
                flash_set('success', 'Reunión registrada. Ya puedes completar su seguimiento cuando se haya realizado.');
                header('Location: reuniones_registrar.php?id_tutoria=' . $id_tutoria);
                exit;
            } catch (InvalidArgumentException $e) {
                $errores[] = $e->getMessage();
                if ($evidencia_url) {
                    @unlink(__DIR__ . '/../' . $evidencia_url);
                }
            } catch (RuntimeException $e) {
                $errores[] = $e->getMessage();
                if ($evidencia_url) {
                    @unlink(__DIR__ . '/../' . $evidencia_url);
                }
            }
        }
    }
}

function procesarEvidencia()
{
    if (empty($_FILES['evidencia']) || ($_FILES['evidencia']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES['evidencia']['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Error al subir el archivo de evidencia.');
    }

    $archivo = $_FILES['evidencia'];
    $nombre  = $archivo['name'];
    $mime    = $archivo['type'];
    $ext     = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

    if (!in_array($ext, ReunionModel::extensionesPermitidas(), true)) {
        throw new InvalidArgumentException('La evidencia debe ser JPG, PNG o PDF.');
    }
    if (!in_array($mime, ReunionModel::mimesPermitidos(), true) && !finfoValidaMime($archivo['tmp_name'])) {
        throw new InvalidArgumentException('El tipo de archivo no es válido.');
    }
    if ($archivo['size'] > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('El archivo no puede superar los 5MB.');
    }

    $carpeta = __DIR__ . '/../uploads/evidencias/';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }

    $nombreUnico = 'evidencia_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreUnico)) {
        throw new RuntimeException('No se pudo guardar el archivo de evidencia.');
    }

    return 'uploads/evidencias/' . $nombreUnico;
}

function finfoValidaMime(string $temporal): bool
{
    if (!function_exists('finfo_open')) {
        return true;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $temporal);
    finfo_close($finfo);

    return in_array($mime, ReunionModel::mimesPermitidos(), true);
}

$reuniones = $reunionModel->obtenerPorTutor((int) $tutor['id_tutor'], $id_tutoria > 0 ? $id_tutoria : null);
$seguimientos = $reunionModel->seguimientosPorReuniones(array_column($reuniones, 'id_reunion'));
$totalReunionesTutor = count($reunionModel->obtenerPorTutor((int) $tutor['id_tutor']));

require_once __DIR__ . '/../views/tutor/reuniones.php';