<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/Response.php';
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

function leerCuerpo()
{
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    return is_array($datos) ? $datos : $_POST;
}

function obtenerIdEstudiante(PDO $pdo, $id_usuario)
{
    $stmt = $pdo->prepare("SELECT id_estudiante FROM estudiantes WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['id_estudiante'] ?? null;
}

function obtenerIdTutor(PDO $pdo, $id_usuario)
{
    $stmt = $pdo->prepare("SELECT id_tutor FROM tutores WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['id_tutor'] ?? null;
}

function validarHora($hora, $campo)
{
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $hora)) {
        throw new InvalidArgumentException("El campo $campo tiene formato de hora inválido.");
    }
}

function validarFecha($fecha)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new InvalidArgumentException('El campo fecha debe tener formato AAAA-MM-DD.');
    }

    [$anio, $mes, $dia] = array_map('intval', explode('-', $fecha));
    if (!checkdate($mes, $dia, $anio)) {
        throw new InvalidArgumentException('El campo fecha no corresponde a una fecha válida.');
    }
}

function validarCamposObligatorios(array $datos, array $campos)
{
    foreach ($campos as $campo) {
        if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
            throw new InvalidArgumentException("El campo $campo es obligatorio.");
        }
    }
}

$action = $_GET['action'] ?? null;

try {
    switch ($action) {
        case 'solicitar':
            verificar_rol('estudiante');

            $id_estudiante = obtenerIdEstudiante($pdo, $_SESSION['id_usuario']);
            if (!$id_estudiante) {
                Response::error('Perfil de estudiante no encontrado para este usuario.', 404);
            }

            $datos = leerCuerpo();
            validarCamposObligatorios($datos, [
                'id_tutor', 'id_materia', 'fecha', 'hora_inicio', 'hora_fin', 'modalidad'
            ]);

            if (!ctype_digit((string) $datos['id_tutor']) || !ctype_digit((string) $datos['id_materia'])) {
                Response::error('id_tutor e id_materia deben ser numéricos.', 422);
            }

            validarFecha($datos['fecha']);
            validarHora($datos['hora_inicio'], 'hora_inicio');
            validarHora($datos['hora_fin'], 'hora_fin');

            if (str_replace(':', '', $datos['hora_inicio']) >= str_replace(':', '', $datos['hora_fin'])) {
                Response::error('hora_inicio debe ser anterior a hora_fin.', 422);
            }

            $tutoriaModel = new TutoriaModel($pdo);
            $id_tutoria = $tutoriaModel->crearSolicitud(
                (int) $id_estudiante,
                (int) $datos['id_tutor'],
                (int) $datos['id_materia'],
                $datos['fecha'],
                $datos['hora_inicio'],
                $datos['hora_fin'],
                $datos['modalidad'],
                $datos['observaciones'] ?? null
            );

            Response::json(['id_tutoria' => (int) $id_tutoria], 201, 'Solicitud de tutoría creada.');
            break;

        case 'mis_tutorias':
            verificar_sesion();

            $tutoriaModel = new TutoriaModel($pdo);

            if ($_SESSION['rol'] === 'estudiante') {
                $id_estudiante = obtenerIdEstudiante($pdo, $_SESSION['id_usuario']);
                if (!$id_estudiante) {
                    Response::error('Perfil de estudiante no encontrado para este usuario.', 404);
                }

                $tutorias = $tutoriaModel->obtenerPorEstudiante((int) $id_estudiante);
                Response::json($tutorias, 200, 'Tutorías del estudiante.');
            }

            if ($_SESSION['rol'] === 'tutor') {
                $id_tutor = obtenerIdTutor($pdo, $_SESSION['id_usuario']);
                if (!$id_tutor) {
                    Response::error('Perfil de tutor no encontrado para este usuario.', 404);
                }

                $estado = $_GET['estado'] ?? null;
                $tutorias = $tutoriaModel->obtenerPorTutor((int) $id_tutor, $estado);
                Response::json($tutorias, 200, 'Tutorías del tutor.');
            }

            Response::error('El rol actual no puede consultar tutorías personales.', 403);
            break;

        case 'cambiar_estado':
            verificar_rol('tutor');

            $datos = leerCuerpo();
            validarCamposObligatorios($datos, ['id_tutoria', 'nuevo_estado']);

            if (!ctype_digit((string) $datos['id_tutoria'])) {
                Response::error('id_tutoria debe ser numérico.', 422);
            }

            $tutoriaModel = new TutoriaModel($pdo);
            $tutoriaModel->cambiarEstado((int) $datos['id_tutoria'], $datos['nuevo_estado']);

            Response::json(
                ['id_tutoria' => (int) $datos['id_tutoria'], 'estado' => $datos['nuevo_estado']],
                200,
                'Estado de la tutoría actualizado.'
            );
            break;

        case 'todas':
            verificar_rol(['administrador']);

            $estado = $_GET['estado'] ?? null;
            $fecha_desde = $_GET['fecha_desde'] ?? null;
            $fecha_hasta = $_GET['fecha_hasta'] ?? null;

            if ($fecha_desde !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) {
                Response::error('fecha_desde debe tener formato AAAA-MM-DD.', 422);
            }
            if ($fecha_hasta !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) {
                Response::error('fecha_hasta debe tener formato AAAA-MM-DD.', 422);
            }

            $tutoriaModel = new TutoriaModel($pdo);
            Response::json(
                $tutoriaModel->obtenerTodas($estado, $fecha_desde, $fecha_hasta),
                200,
                'Tutorías obtenidas.'
            );
            break;

        default:
            Response::error('Acción no válida.', 404);
    }
} catch (InvalidArgumentException $e) {
    Response::error($e->getMessage(), 422);
} catch (RuntimeException $e) {
    Response::error($e->getMessage(), 500);
} catch (PDOException $e) {
    Response::error('Error interno del servidor.', 500);
}