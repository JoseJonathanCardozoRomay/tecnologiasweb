<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../models/BloqueModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/reglas_tutoria.php';

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$rolSesion = $_SESSION['rol'] ?? '';
$estudianteModel = new EstudianteModel($pdo);
$materiaModel = new MateriaModel($pdo);
$tutorModel = new TutorModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);
$periodoModel = new PeriodoModel($pdo);
$bloqueModel = new BloqueModel($pdo);
$estudiante = $rolSesion === 'estudiante' ? $estudianteModel->obtenerPorUsuario($idUsuario) : null;
$errores = [];

// Modalidad por defecto: el estudiante NO la elige. Queda definida por admin/tutor.
$modalidadDefecto = 'presencial';

// Periodos activos y bloques horarios definidos por admin/coordinación.
$periodosActivos = $periodoModel->obtenerActivos();
$bloques = $bloqueModel->obtenerTodos();

// Bloqueo temprano: un estudiante sin carrera no puede solicitar tutorías.
if ($rolSesion === 'estudiante' && $estudiante && empty($estudiante['id_carrera'])) {
    flash_set('danger', 'Debes completar tu registro académico (carrera y semestre) antes de solicitar tutorías. Contacta al administrador.');
    header('Location: /views/estudiante/panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/csrf.php';
    csrf_validar();

    $idEstudiante = $rolSesion === 'estudiante'
        ? ($estudiante['id_estudiante'] ?? null)
        : filter_var($_POST['id_estudiante'] ?? null, FILTER_VALIDATE_INT);

    // El estudiante solo envía: id_materia, id_tutor, id_bloque, observaciones.
    // La fecha, lugar_o_enlace y modalidad los define el sistema (coordinación).
    $datos = [
        'id_estudiante'  => $idEstudiante,
        'id_materia'     => $_POST['id_materia'] ?? '',
        'id_tutor'       => $_POST['id_tutor'] ?? '',
        'id_bloque'      => filter_var($_POST['id_bloque'] ?? null, FILTER_VALIDATE_INT),
        'fecha'          => '',
        'hora_inicio'    => '',
        'hora_fin'       => '',
        'modalidad'      => $modalidadDefecto,
        'lugar_o_enlace' => '',
        'observaciones'  => trim($_POST['observaciones'] ?? ''),
    ];

    // Campos obligatorios (ya NO hay horas libres: se derivan del bloque).
    if (!$idEstudiante || empty($datos['id_materia']) || empty($datos['id_tutor']) || empty($datos['id_bloque'])) {
        $errores[] = 'Todos los campos marcados con asterisco (*) son obligatorios.';
    } elseif (!$estudianteModel->obtenerPorId($idEstudiante)) {
        $errores[] = 'El estudiante seleccionado no es válido.';
    }

    $estudianteDestino = $estudianteModel->obtenerPorId($idEstudiante);

    // El estudiante debe tener carrera asignada.
    if ($estudianteDestino && empty($estudianteDestino['id_carrera'])) {
        $errores[] = 'El estudiante no tiene una carrera asignada. Contacte al administrador.';
    }

    // Fecha: se calcula automáticamente desde el periodo activo (no viene del estudiante)
    $periodoActivo = $periodoModel->obtenerActivoPorFecha(date('Y-m-d'));
    if (!$periodoActivo) {
        $errores[] = 'No hay un periodo académico activo. Contacte a coordinación.';
    } else {
        // Calcular la fecha más próxima disponible (hoy o la fecha de inicio del periodo si es futuro)
        $fechaHoy = date('Y-m-d');
        $fechaInicioPeriodo = $periodoActivo['fecha_inicio'];
        $fechaFinPeriodo = $periodoActivo['fecha_fin'];
        
        if ($fechaHoy < $fechaInicioPeriodo) {
            $datos['fecha'] = $fechaInicioPeriodo;
        } elseif ($fechaHoy > $fechaFinPeriodo) {
            $errores[] = 'El periodo académico actual ha finalizado. Contacte a coordinación.';
        } else {
            $datos['fecha'] = $fechaHoy;
        }
        
        if (!empty($datos['fecha'])) {
            $datos['periodo'] = $periodoActivo['codigo'];
        }
    }

    // Bloque horario: debe existir; de él se derivan hora_inicio/hora_fin.
    $bloque = !empty($datos['id_bloque']) ? $bloqueModel->obtenerPorId((int) $datos['id_bloque']) : false;
    if (!empty($datos['id_bloque']) && !$bloque) {
        $errores[] = 'El bloque horario seleccionado no existe.';
    } elseif ($bloque) {
        $datos['hora_inicio'] = $bloque['hora_inicio'];
        $datos['hora_fin']    = $bloque['hora_fin'];
    }

    if (empty($datos['periodo'])) {
        $datos['periodo'] = 'I-' . date('Y');
    }

    if (!$materiaModel->obtenerPorId((int) $datos['id_materia']) || !$tutorModel->obtenerPorId((int) $datos['id_tutor'])) {
        $errores[] = 'La materia o el tutor seleccionado no es válido.';
    }

    // Defensa en profundidad: la materia debe pertenecer a la carrera del estudiante.
    if (!$errores && $estudianteDestino) {
        $materiasPermitidas = array_map('intval', array_column($materiaModel->obtenerDisponiblesParaCarrera($estudianteDestino['id_carrera'] ?? 0), 'id_materia'));
        if (!in_array((int) $datos['id_materia'], $materiasPermitidas, true)) {
            $errores[] = 'La materia seleccionada no pertenece a tu carrera.';
        }
    }

    if (!$errores) {
        $errores = array_merge($errores, validarSolicitudTutoria($pdo, $datos, $estudianteDestino));
    }
    if (!$errores) {
        try {
            $resultado = $tutoriaModel->crearSinCruces($datos);
            if (!$resultado['ok']) {
                $errores[] = $resultado['error'];
            } else {
                try {
                    $notificacionModel = new NotificacionModel($pdo);
                    $idTutoriaCreada = (int) ($resultado['id'] ?? 0);
                    $tutorSolicitado = $tutorModel->obtenerPorId((int) $datos['id_tutor']);
                    if ($tutorSolicitado && !empty($tutorSolicitado['id_usuario'])) {
                        $notificacionModel->generarNotificacionSolicitud(
                            (int) $tutorSolicitado['id_usuario'],
                            $idTutoriaCreada,
                            (int) $datos['id_materia'],
                            (int) $datos['id_estudiante']
                        );
                    }
                    $notificacionModel->generarNotificacionParaAdmin(
                        $idTutoriaCreada,
                        (int) $datos['id_materia'],
                        (int) $datos['id_estudiante'],
                        (int) $datos['id_tutor'],
                        $idUsuario
                    );
                } catch (Throwable $e) {
                    error_log($e->getMessage());
                }
                flash_set('success', 'Solicitud registrada.');
                header('Location: ' . ($rolSesion === 'estudiante' ? '/views/estudiante/panel.php' : 'tutorias_listar.php'));
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = 'No se pudo agendar la sesión.';
        }
    }
}

// Materias visibles: SOLO las de la carrera del estudiante (o todas con tutor activo si es admin).
$materias = $rolSesion === 'estudiante'
    ? $materiaModel->obtenerDisponiblesParaCarrera($estudiante['id_carrera'] ?? 0)
    : $materiaModel->obtenerTodasConTutorActivo();
$tutores = $tutorModel->obtenerTodos();
$estudiantes = $rolSesion === 'administrador' ? $estudianteModel->obtenerTodos() : [];

// Periodo mostrado por defecto: el activo que cubre hoy (o el primero activo).
$periodoActual = $periodoModel->obtenerActivoPorFecha(date('Y-m-d')) ?: ($periodosActivos[0] ?? null);
$fechaMin = $periodoActual['fecha_inicio'] ?? date('Y-m-d');
$fechaMax = $periodoActual['fecha_fin'] ?? date('Y-m-d', strtotime('+6 months'));
require_once __DIR__ . '/../views/tutorias/solicitar.php';
