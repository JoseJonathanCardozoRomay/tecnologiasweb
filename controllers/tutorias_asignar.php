<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/CartaModel.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tutoriaModel = new TutoriaModel($pdo);
$cartaModel = new CartaModel($pdo);
$modalidadModel = new ModalidadModel($pdo);

$errores = [];
$cupoBloqueado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $id_usuario_estudiante = (int) ($_POST['id_estudiante'] ?? 0);
    $id_usuario_tutor      = (int) ($_POST['id_tutor'] ?? 0);
    $id_materia            = (int) ($_POST['id_materia'] ?? 0);
    $id_modalidad          = (int) ($_POST['id_modalidad'] ?? 0);
    $fecha                 = trim($_POST['fecha'] ?? '');
    $hora_inicio           = trim($_POST['hora_inicio'] ?? '');
    $hora_fin              = trim($_POST['hora_fin'] ?? '');
    $modalidad             = $_POST['modalidad'] ?? 'presencial';
    $lugar_o_enlace        = trim($_POST['lugar_o_enlace'] ?? '');
    $observaciones         = trim($_POST['observaciones'] ?? '');
    $sobreasignar          = !empty($_POST['sobreasignar']);

    if (!$id_usuario_estudiante || !$id_usuario_tutor || !$id_materia) {
        $errores[] = 'Debes seleccionar el estudiante, el tutor y la materia.';
    }
    if (!$id_modalidad) {
        $errores[] = 'Debes seleccionar la modalidad de graduación (Proyecto, Tesis o Trabajo Dirigido).';
    }
    if (!$fecha) {
        $errores[] = 'La fecha de la tutoría es obligatoria.';
    }
    if (!$hora_inicio || !$hora_fin || $hora_fin <= $hora_inicio) {
        $errores[] = 'Las horas de inicio y fin no son válidas.';
    }

    if (empty($errores)) {
        $estudianteModel = new EstudianteModel($pdo);
        $tutorModel      = new TutorModel($pdo);

        $estudiante = $estudianteModel->obtenerPorUsuario($id_usuario_estudiante);
        $tutor      = $tutorModel->obtenerPorUsuario($id_usuario_tutor);

        if (!$estudiante || !$tutor) {
            $errores[] = 'El estudiante o el tutor seleccionado no tienen perfil válido.';
        } else {
            $tutorCarreras = array_column($tutorModel->carrerasDelTutor((int) $tutor['id_tutor']), 'id_carrera');
            if (!in_array((int) $estudiante['id_carrera'], array_map('intval', $tutorCarreras), true)) {
                $errores[] = 'El tutor seleccionado no pertenece a la carrera del estudiante.';
            }

            $cupo = $cartaModel->cupoDisponible((int) $tutor['id_tutor']);
            if ($cupo <= 0) {
                if ($sobreasignar) {
                    $cupoBloqueado = 'Cupo alcanzado: se registró la asignación con sobrecupo (aprobación de Decanatura).';
                } else {
                    $errores[] = 'El tutor alcanzó el cupo máximo de ' . $cartaModel->cupoMaximo() . ' tutorías activas. Para sobreasignar, marca la aprobación de Decanatura.';
                }
            }
        }

        if (empty($errores)) {
            try {
                $id_tutoria = $tutoriaModel->asignar(
                    (int) $estudiante['id_estudiante'],
                    (int) $tutor['id_tutor'],
                    $id_materia,
                    $id_modalidad,
                    $fecha,
                    $hora_inicio,
                    $hora_fin,
                    $modalidad,
                    $lugar_o_enlace ?: null,
                    $observaciones ?: null
                );

                $cartaModel->crear((int) $id_tutoria, (int) $tutor['id_tutor'], (int) $estudiante['id_estudiante']);

                $historial = new HistorialModel($pdo);
                $historial->registrar(
                    (int) ($_SESSION['id_usuario'] ?? 0),
                    'TUTORIA_ASIGNADA',
                    ($sobreasignar ? '[Sobrecupo - aprobado por Decanatura] ' : '') . 'Asignación de tutoría #' . $id_tutoria . ' con carta de designación.'
                );

                $notifs = new NotificationModel($pdo);
                $notifs->crear($id_usuario_estudiante, 'TUTORIA_ASIGNADA', 'Te asignaron una tutoría de Modalidad de Grado (#' . $id_tutoria . '). Revisa tu panel.', '/views/estudiante/panel.php', (int) ($_SESSION['id_usuario'] ?? 0));
                $notifs->crear($id_usuario_tutor, 'TUTORIA_ASIGNADA', 'Recibiste una carta de designación para la tutoría #' . $id_tutoria . '.', 'cartas_responder.php', (int) ($_SESSION['id_usuario'] ?? 0));

                if ($sobreasignar) {
                    flash_set('warning', $cupoBloqueado);
                }
                flash_set('success', 'Tutoría asignada y carta de designación generada.');
                header('Location: tutorias_listar.php');
                exit;
            } catch (InvalidArgumentException $e) {
                $errores[] = $e->getMessage();
            } catch (RuntimeException $e) {
                $errores[] = $e->getMessage();
            }
        }
    }
}

$tutorModel = new TutorModel($pdo);

$estudiantes = (new EstudianteModel($pdo))->obtenerTodosConCarrera();
$tutores     = (new UsuarioModel($pdo))->obtenerTutoresConPerfil();
$materias    = (new MateriaModel($pdo))->obtenerTodas();
$modalidades = $modalidadModel->obtenerTodas();

$tutoresDetalle = [];
foreach ((new TutorModel($pdo))->obtenerTodos() as $td) {
    $tutoresDetalle[(int) $td['id_tutor']] = [
        'carreras' => array_column($td['carreras'] ?? [], 'id_carrera'),
        'cupos'    => $cartaModel->cuposMG((int) $td['id_tutor']),
    ];
}

require_once __DIR__ . '/../views/tutorias/asignar.php';