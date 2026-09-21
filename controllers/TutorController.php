<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/Response.php';
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';

const DIAS_SEMANA = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];

function leerCuerpo()
{
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    return is_array($datos) ? $datos : $_POST;
}

function obtenerIdTutor(PDO $pdo, $id_usuario)
{
    $stmt = $pdo->prepare("SELECT id_tutor FROM tutores WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['id_tutor'] ?? null;
}

function validarHora($hora, $indice, $campo)
{
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $hora)) {
        throw new InvalidArgumentException("El bloque $indice tiene un $campo con formato de hora inválido.");
    }
}

function validarBloque($bloque, $indice)
{
    if (!isset($bloque['dia_semana']) || !in_array($bloque['dia_semana'], DIAS_SEMANA, true)) {
        throw new InvalidArgumentException("El bloque $indice requiere un dia_semana válido (Lunes a Sabado).");
    }

    validarHora($bloque['hora_inicio'], $indice, 'hora_inicio');
    validarHora($bloque['hora_fin'], $indice, 'hora_fin');

    if (str_replace(':', '', $bloque['hora_inicio']) >= str_replace(':', '', $bloque['hora_fin'])) {
        throw new InvalidArgumentException("El bloque $indice tiene hora_inicio posterior o igual a hora_fin.");
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
        case 'listar':
            verificar_rol(['administrador']);

            $tutorModel = new TutorModel($pdo);
            Response::json($tutorModel->obtenerTodos(), 200, 'Tutores obtenidos.');
            break;

        case 'detalle':
            verificar_sesion();

            $id = $_GET['id'] ?? null;
            if (!ctype_digit((string) $id)) {
                Response::error('El parámetro id es obligatorio y debe ser numérico.', 422);
            }

            $tutorModel = new TutorModel($pdo);
            $tutor = $tutorModel->obtenerPorId((int) $id);

            if (!$tutor) {
                Response::error('Tutor no encontrado.', 404);
            }

            Response::json($tutor, 200, 'Detalle del tutor.');
            break;

        case 'disponibilidad':
            verificar_rol('tutor');

            $id_tutor = obtenerIdTutor($pdo, $_SESSION['id_usuario']);
            if (!$id_tutor) {
                Response::error('Perfil de tutor no encontrado para este usuario.', 404);
            }

            $datos = leerCuerpo();
            $bloques = $datos['bloques'] ?? null;

            if (!is_array($bloques)) {
                Response::error('El campo bloques es obligatorio y debe ser un arreglo.', 422);
            }

            foreach ($bloques as $indice => $bloque) {
                validarBloque($bloque, $indice);
            }

            $tutorModel = new TutorModel($pdo);
            $tutorModel->guardarDisponibilidad((int) $id_tutor, $bloques);

            Response::json(
                ['id_tutor' => (int) $id_tutor, 'bloques_guardados' => count($bloques)],
                200,
                'Disponibilidad actualizada.'
            );
            break;

        case 'materias':
            verificar_rol(['administrador']);

            $materiaModel = new MateriaModel($pdo);
            Response::json($materiaModel->obtenerTodas(), 200, 'Materias obtenidas.');
            break;

        case 'mi_perfil':
            verificar_rol('tutor');

            $id_tutor = obtenerIdTutor($pdo, $_SESSION['id_usuario']);
            if (!$id_tutor) {
                Response::error('Perfil de tutor no encontrado para este usuario.', 404);
            }

            $tutorModel = new TutorModel($pdo);
            Response::json($tutorModel->obtenerPorId((int) $id_tutor), 200, 'Perfil del tutor.');
            break;

        case 'crear':
            verificar_rol(['administrador']);

            $datos = leerCuerpo();
            validarCamposObligatorios($datos, ['nombre', 'apellido', 'correo', 'usuario', 'clave']);

            if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                Response::error('El correo no es válido.', 422);
            }
            if (strlen($datos['clave']) < 6) {
                Response::error('La contraseña debe tener al menos 6 caracteres.', 422);
            }

            $ids_materias = $datos['ids_materias'] ?? [];
            if (!is_array($ids_materias)) {
                Response::error('ids_materias debe ser un arreglo.', 422);
            }

            try {
                $pdo->beginTransaction();

                $usuarioModel = new UsuarioModel($pdo);
                $usuarioModel->crear([
                    'id_rol'   => 2,
                    'nombre'   => trim($datos['nombre']),
                    'apellido' => trim($datos['apellido']),
                    'correo'   => trim($datos['correo']),
                    'usuario'  => trim($datos['usuario']),
                    'clave'    => $datos['clave'],
                ]);

                $id_usuario = (int) $pdo->lastInsertId();

                $tutorModel = new TutorModel($pdo);
                $id_tutor = (int) $tutorModel->crearPerfil(
                    $id_usuario,
                    trim($datos['especialidad'] ?? ''),
                    trim($datos['biografia'] ?? '')
                );

                if (!empty($ids_materias)) {
                    $tutorModel->asignarMaterias($id_tutor, array_map('intval', $ids_materias));
                }

                $pdo->commit();

                Response::json(
                    ['id_tutor' => $id_tutor, 'id_usuario' => $id_usuario],
                    201,
                    'Tutor registrado correctamente.'
                );
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                Response::error('No se pudo registrar el tutor. Verifica que el correo o el usuario no estén en uso.', 422);
            }
            break;

        case 'asignar_materias':
            verificar_rol(['administrador']);

            $datos = leerCuerpo();
            validarCamposObligatorios($datos, ['id_tutor']);

            if (!ctype_digit((string) $datos['id_tutor'])) {
                Response::error('id_tutor debe ser numérico.', 422);
            }

            $ids_materias = $datos['ids_materias'] ?? [];
            if (!is_array($ids_materias)) {
                Response::error('ids_materias debe ser un arreglo.', 422);
            }

            $tutorModel = new TutorModel($pdo);
            if (!$tutorModel->obtenerPorId((int) $datos['id_tutor'])) {
                Response::error('Tutor no encontrado.', 404);
            }

            $tutorModel->asignarMaterias((int) $datos['id_tutor'], array_map('intval', $ids_materias));

            Response::json(
                ['id_tutor' => (int) $datos['id_tutor'], 'materias_asignadas' => count($ids_materias)],
                200,
                'Materias del tutor actualizadas.'
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