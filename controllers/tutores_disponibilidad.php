<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../models/SolicitudActualizacionModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

const DIAS_SEMANA = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];

$esAdmin = in_array($_SESSION['rol'] ?? '', ['administrador', 'auxiliar'], true);
$esAdministrador = ($_SESSION['rol'] ?? '') === 'administrador';

if ($esAdmin) {
    requerirRol(['administrador', 'auxiliar']);
    $id_tutor = $_GET['id'] ?? null;
    if (!ctype_digit((string) $id_tutor)) {
        header('Location: tutores_listar.php');
        exit;
    }
    $id_tutor = (int) $id_tutor;
} else {
    requerirRol(['tutor']);
    $tutorPropio = (new TutorModel($pdo))->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
    if (!$tutorPropio) {
        flash_set('error', 'No se encontró tu perfil de tutor.');
        header('Location: ../views/tutor/panel.php');
        exit;
    }
    $id_tutor = (int) $tutorPropio['id_tutor'];
}

$tutorModel = new TutorModel($pdo);
$materiaModel = new MateriaModel($pdo);
$solicitudModel = new SolicitudActualizacionModel($pdo);

$tutor = $tutorModel->obtenerPorId($id_tutor);
if (!$tutor) {
    header('Location: ' . ($esAdmin ? 'tutores_listar.php' : '../views/tutor/panel.php'));
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $accion = $_POST['accion'] ?? 'guardar';

    if ($accion === 'solicitar' && !$esAdmin) {
        $tipo = $_POST['tipo'] ?? '';
        $detalle = trim($_POST['detalle'] ?? '');
        try {
            $id_solicitud = $solicitudModel->registrar(
                $id_tutor,
                (int) ($_SESSION['id_usuario'] ?? 0),
                $tipo,
                $detalle
            );
            $notifs = new NotificationModel($pdo);
            foreach ($notifs->idsUsuariosAdministracion() as $destino) {
                $notifs->crear(
                    $destino,
                    'TUTOR_SOLICITUD',
                    $tutor['nombre'] . ' ' . $tutor['apellido'] . ' solicitó una actualización de ' . SolicitudActualizacionModel::etiquetaTipo($tipo) . '.',
                    'solicitudes_tutor_listar.php?id=' . $id_solicitud,
                    (int) ($_SESSION['id_usuario'] ?? 0)
                );
            }
            flash_set('success', 'Solicitud #' . $id_solicitud . ' registrada. El administrador la revisará pronto.');
            header('Location: tutores_disponibilidad.php');
            exit;
        } catch (Throwable $e) {
            $errores[] = $e->getMessage();
        }
    } else {
        $especialidad = trim($_POST['especialidad'] ?? '');

        $dias = (array) ($_POST['dia_semana'] ?? []);
        $inicios = (array) ($_POST['hora_inicio'] ?? []);
        $fines = (array) ($_POST['hora_fin'] ?? []);
        $bloques = [];

        $totalRows = count($dias);
        for ($i = 0; $i < $totalRows; $i++) {
            $dia = trim((string) ($dias[$i] ?? ''));
            $inicio = trim((string) ($inicios[$i] ?? ''));
            $fin = trim((string) ($fines[$i] ?? ''));

            if ($dia === '' && $inicio === '' && $fin === '') {
                continue;
            }

            if (!in_array($dia, DIAS_SEMANA, true)) {
                $errores[] = "El horario $i tiene un día de la semana inválido.";
                continue;
            }
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $inicio)) {
                $errores[] = "El horario $i tiene hora de inicio inválida.";
                continue;
            }
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $fin)) {
                $errores[] = "El horario $i tiene hora de fin inválida.";
                continue;
            }
            if (str_replace(':', '', $inicio) >= str_replace(':', '', $fin)) {
                $errores[] = "El horario $i debe terminar después de iniciar.";
                continue;
            }

            $bloques[] = [
                'id_disponibilidad' => (int) ($_POST['id_disponibilidad'][$i] ?? 0) ?: null,
                'dia_semana'        => $dia,
                'hora_inicio'       => $inicio . ':00',
                'hora_fin'          => $fin . ':00',
            ];
        }

        if (empty($errores)) {
            try {
                if ($especialidad !== '') {
                    $tutorModel->actualizarEspecialidad($id_tutor, $especialidad);
                }

                if ($esAdministrador) {
                    $ids_materias = array_unique(array_map('intval', (array) ($_POST['id_materias'] ?? [])));
                    $ids_carreras = array_unique(array_map('intval', (array) ($_POST['id_carreras'] ?? [])));
                    $tutorModel->asignarMaterias($id_tutor, $ids_materias);
                    $tutorModel->asignarCarreras($id_tutor, $ids_carreras);
                }

                $tutorModel->guardarDisponibilidad($id_tutor, $bloques);

                flash_set('success', $esAdmin
                    ? 'Carreras, materias y horarios actualizados correctamente.'
                    : 'Tus horarios fueron actualizados. Las carreras y materias solo las modifica el administrador.');
                header('Location: ' . ($esAdmin ? "tutores_disponibilidad.php?id=$id_tutor" : 'tutores_disponibilidad.php'));
                exit;
            } catch (Throwable $e) {
                $errores[] = $e->getMessage();
            }
        }
    }
}

$tutor = $tutorModel->obtenerPorId($id_tutor);
$carreras = (new CarreraModel($pdo))->obtenerTodas();
$carrerasTutor = array_column($tutor['carreras'] ?? [], 'id_carrera');
$materias = $materiaModel->obtenerTodas();
$materiasTutor = array_column($tutor['materias'] ?? [], 'id_materia');
$disponibilidad = $tutor['disponibilidad'] ?? [];
$solicitudes = $solicitudModel->listar($id_tutor);
$puedeAsignar = $esAdministrador;

require_once __DIR__ . '/../views/tutores/disponibilidad.php';