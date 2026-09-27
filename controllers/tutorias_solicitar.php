<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$estudianteModel = new EstudianteModel($pdo);
$materiaModel = new MateriaModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);
$configuracion = new ConfiguracionModel($pdo);

$estudiante = $estudianteModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
if (!$estudiante) {
    flash_set('error', 'Tu perfil de estudiante aún no está completo. Contacta al administrador.');
    header('Location: ../views/estudiante/panel.php');
    exit;
}

if ($tutoriaModel->mgCompletada((int) $estudiante['id_estudiante'])) {
    flash_set('error', 'Tu Modalidad de Grado ya fue completada. No es posible solicitar nuevas tutorías.');
    header('Location: ../views/estudiante/panel.php');
    exit;
}

$tipo = $_POST['tipo'] ?? ($_GET['tipo'] ?? 'apoyo');
if (!in_array($tipo, ['apoyo', 'grado'], true)) {
    $tipo = 'apoyo';
}

$accesoMG = $estudianteModel->puedeAccederMG((int) $estudiante['id_estudiante'], $configuracion);

if (!$accesoMG) {
    $tipo = 'apoyo';
}

$errores = [];

if (($_POST['tipo'] ?? '') === 'grado' && !$accesoMG) {
    $errores[] = 'Aún no tienes habilitado el acceso a Modalidad de Grado.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $id_materia = $_POST['id_materia'] ?? '';
    $id_tutor = $_POST['id_tutor'] ?? '';
    $id_modalidad = $_POST['id_modalidad'] ?? '';
    $fecha = trim($_POST['fecha'] ?? '');
    $hora_inicio = trim($_POST['hora_inicio'] ?? '');
    $hora_fin = trim($_POST['hora_fin'] ?? '');
    $modalidad = $_POST['modalidad'] ?? 'presencial';
    $observaciones = trim($_POST['observaciones'] ?? '');

    if (!ctype_digit((string) $id_materia) || !ctype_digit((string) $id_tutor)) {
        $errores[] = "Debes elegir una materia y un docente tutor.";
    }

    $materia = ctype_digit((string) $id_materia) ? $materiaModel->obtenerPorId((int) $id_materia) : null;
    if (!$materia || (int) ($materia['id_carrera'] ?? 0) !== (int) $estudiante['id_carrera']) {
        $errores[] = "La materia debe pertenecer a tu carrera.";
    }

    if ($tipo === 'grado') {
        if (!ctype_digit((string) $id_modalidad)) {
            $errores[] = "Debes elegir la modalidad de graduación.";
        }
    } else {
        $id_modalidad = null;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !strtotime($fecha)) {
        $errores[] = "La fecha no es válida.";
    } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        $errores[] = "La fecha debe ser hoy o posterior.";
    }

    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora_inicio) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora_fin)) {
        $errores[] = "El rango horario no es válido.";
    } elseif (str_replace(':', '', $hora_inicio) >= str_replace(':', '', $hora_fin)) {
        $errores[] = "La hora de inicio debe ser anterior a la hora de fin.";
    }

    if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
        $errores[] = "La modalidad seleccionada no es válida.";
    }

    if (mb_strlen($observaciones) > 1000) {
        $errores[] = "Las observaciones no pueden superar los 1000 caracteres.";
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare(
            "SELECT 1
             FROM tutor_materia tm
             INNER JOIN docente_carreras dc ON dc.id_tutor = tm.id_tutor
             WHERE tm.id_tutor = :id_tutor
               AND tm.id_materia = :id_materia
               AND dc.id_carrera = :id_carrera"
        );
        $stmt->execute([
            ':id_tutor'    => (int) $id_tutor,
            ':id_materia'  => (int) $id_materia,
            ':id_carrera'  => (int) $estudiante['id_carrera'],
        ]);

        if (!$stmt->fetchColumn()) {
            $errores[] = "El tutor seleccionado no imparte la materia elegida en tu carrera.";
        }
    }

    if (empty($errores)) {
        try {
            $id_tutoria_creada = $tutoriaModel->crearSolicitud(
                (int) $estudiante['id_estudiante'],
                (int) $id_tutor,
                (int) $id_materia,
                $fecha,
                $hora_inicio . ':00',
                $hora_fin . ':00',
                $modalidad,
                $tipo,
                $id_modalidad !== '' ? (int) $id_modalidad : null,
                $observaciones !== '' ? $observaciones : null
            );

            $notificaciones = new NotificationModel($pdo);
            $origen = (int) ($_SESSION['id_usuario'] ?? 0);

            $stmtTutor = $pdo->prepare("SELECT tu.id_usuario FROM tutores t INNER JOIN usuarios tu ON tu.id_usuario = t.id_usuario WHERE t.id_tutor = :id_tutor LIMIT 1");
            $stmtTutor->execute([':id_tutor' => (int) $id_tutor]);
            $tutorDestinatario = $stmtTutor->fetch(PDO::FETCH_ASSOC);

            if ($tutorDestinatario && (int) $tutorDestinatario['id_usuario'] > 0) {
                $notificaciones->crear(
                    (int) $tutorDestinatario['id_usuario'],
                    'TUTORIA_SOLICITADA',
                    'Un estudiante te solicitaó una tutoría de ' . ($tipo === 'grado' ? 'Modalidad de Grado' : 'apoyo')
                        . ' para el ' . date('d/m/Y', strtotime($fecha)) . ' a las ' . substr($hora_inicio, 0, 5) . '.',
                    '/views/tutor/panel.php',
                    $origen
                );
            }

            if ($tipo === 'grado') {
                foreach ($notificaciones->idsUsuariosAdministracion() as $idUsuarioAdmin) {
                    $notificaciones->crear(
                        (int) $idUsuarioAdmin,
                        'TUTORIA_SOLICITADA',
                        'Hay una solicitud de tutoría de Modalidad de Grado (#' . $id_tutoria_creada . ') pendiente de revisión.',
                        '/views/admin/solicitudes_tutor.php',
                        $origen
                    );
                }
            }

            flash_set('success', $tipo === 'grado'
                ? 'Solicitud de Modalidad de Grado enviada. Administración la confirmará con la carta de designación.'
                : 'Solicitud de tutoría enviada correctamente. Espera la confirmación del tutor.');
            header('Location: ../views/estudiante/panel.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $errores[] = $e->getMessage();
        } catch (Throwable $e) {
            $errores[] = 'Ocurrió un error al enviar la solicitud. Intenta nuevamente.';
        }
    }
}

$modalidades = (new ModalidadModel($pdo))->obtenerTodas();

$materias = $materiaModel->obtenerPorCarrera((int) $estudiante['id_carrera']);

require_once __DIR__ . '/../views/tutorias/solicitar.php';