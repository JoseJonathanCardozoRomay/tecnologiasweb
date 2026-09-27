<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$tribunalModel = new TribunalModel($pdo);
$tutoriaModel  = new TutoriaModel($pdo);
$historial     = new HistorialModel($pdo);

$id_tutoria = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$filtroTribunal = limpiarTexto($_GET['q_tribunal'] ?? '', 60);
$errores = [];

function notificarAccionTribunal(NotificationModel $nm, int $id_tutoria, string $tipo, string $mensaje, int $id_operador, array $destinatariosExtra = []): void
{
    $involucrados = $nm->usuariosDeTutoria($id_tutoria);
    $idEstudiante = (int) ($involucrados['id_usuario_estudiante'] ?? 0);
    $idTutor = (int) ($involucrados['id_usuario_tutor'] ?? 0);

    $destinos = [];
    if ($idEstudiante > 0) {
        $destinos[$idEstudiante] = '/views/estudiante/mg_portal.php';
    }
    if ($idTutor > 0) {
        $destinos[$idTutor] = '/views/tutor/panel.php';
    }
    foreach ($destinatariosExtra as $extra) {
        $extra = (int) $extra;
        if ($extra > 0 && !isset($destinos[$extra])) {
            $destinos[$extra] = '/views/tutor/panel.php';
        }
    }

    foreach ($destinos as $dest => $destino) {
        if ($dest !== $id_operador) {
            $nm->crear($dest, $tipo, $mensaje, $destino, $id_operador);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $accion = in_array($_POST['accion'] ?? '', ['asignar', 'editar', 'eliminar'], true)
        ? $_POST['accion']
        : 'asignar';
    $id_operador = (int) ($_SESSION['id_usuario'] ?? 0);
    $enlaceBase = null;

    try {
        if ($accion === 'asignar') {
            $id_tutoria = (int) ($_POST['id'] ?? 0);

            // Tribunales 1..5 (1 y 2 obligatorios; 3, 4 y 5 opcionales).
            $miembros = [];
            for ($n = 1; $n <= $tribunalModel->maximoTribunales(); $n++) {
                $valor = (int) ($_POST['tribunal_' . $n] ?? 0);
                if ($valor > 0) {
                    $miembros[] = $valor;
                }
            }

            if ($id_tutoria <= 0) {
                throw new InvalidArgumentException('Debes seleccionar una tutoría.');
            }
            if (count($miembros) !== count(array_unique($miembros))) {
                throw new InvalidArgumentException('Un mismo docente no puede ser asignado dos veces como tribunal del mismo expediente.');
            }
            if (count($miembros) < $tribunalModel->cantidadPorTutoriaEsperada()) {
                throw new InvalidArgumentException('Debes asignar al menos ' . $tribunalModel->cantidadPorTutoriaEsperada() . ' tribunales.');
            }

            $tribunalModel->asignar($id_tutoria, $miembros);
            $historial->registrar($id_operador, 'TRIBUNAL_ASIGNADO', 'Asignación de ' . count($miembros) . ' tribunales al expediente MG de la tutoría #' . $id_tutoria . '.');
            notificarAccionTribunal(
                new NotificationModel($pdo),
                $id_tutoria,
                'TRIBUNAL_ASIGNADO',
                'Se asignaron ' . count($miembros) . ' tribunales a tu tutoría MG #' . $id_tutoria . '.',
                $id_operador,
                $miembros
            );

            flash_set('success', 'Tribunales asignados correctamente al expediente.');
            $enlaceBase = 'tribunales_asignar.php?id=' . $id_tutoria;
        } elseif ($accion === 'editar') {
            $id_tribunal = (int) ($_POST['id_tribunal'] ?? 0);
            $nuevo = (int) ($_POST['nuevo_miembro'] ?? 0);

            $id_tutoria = $tribunalModel->editarMiembro($id_tribunal, $nuevo);
            $historial->registrar($id_operador, 'TRIBUNAL_MODIFICADO', 'Tribunal #' . $id_tribunal . ' modificado en el expediente MG de la tutoría #' . $id_tutoria . '.');
            notificarAccionTribunal(
                new NotificationModel($pdo),
                $id_tutoria,
                'TRIBUNAL_MODIFICADO',
                'Se modificó la integración del tribunal de tu tutoría MG #' . $id_tutoria . '.',
                $id_operador,
                [$nuevo]
            );

            flash_set('success', 'Tribunal modificado correctamente.');
            $enlaceBase = 'tribunales_asignar.php?id=' . $id_tutoria;
        } elseif ($accion === 'eliminar') {
            $id_tribunal = (int) ($_POST['id_tribunal'] ?? 0);
            $miembro_afectado = (int) ($_POST['id_usuario_miembro'] ?? 0);

            $id_tutoria = $tribunalModel->eliminarMiembro($id_tribunal);
            $historial->registrar($id_operador, 'TRIBUNAL_ELIMINADO', 'Tribunal #' . $id_tribunal . ' eliminado del expediente MG de la tutoría #' . $id_tutoria . '.');
            notificarAccionTribunal(
                new NotificationModel($pdo),
                $id_tutoria,
                'TRIBUNAL_ELIMINADO',
                'Se removió un miembro del tribunal de tu tutoría MG #' . $id_tutoria . '.',
                $id_operador,
                $miembro_afectado > 0 ? [$miembro_afectado] : []
            );

            flash_set('success', 'Tribunal eliminado correctamente.');
            $enlaceBase = 'tribunales_asignar.php?id=' . $id_tutoria;
        }
    } catch (InvalidArgumentException $e) {
        $errores[] = $e->getMessage();
    } catch (RuntimeException $e) {
        $errores[] = $e->getMessage();
    }

    if (empty($errores)) {
        header('Location: ' . ($enlaceBase ?: 'tribunales_asignar.php'));
        exit;
    }
}

$tutorias = array_filter(
    $tutoriaModel->obtenerTodas(),
    fn($t) => !empty($t['id_modalidad'])
        && in_array($t['estado'], ['asignada', 'aceptada', 'en_proceso'], true)
);

$tribunalesAsignados = [];
$docentesDisponibles = [];
$infoTutoria = null;
if ($id_tutoria > 0) {
    $infoTutoria = $tribunalModel->infoAsignacion($id_tutoria);
    if ($infoTutoria) {
        $tribunalesAsignados = $tribunalModel->obtenerPorTutoria($id_tutoria);
        $docentesDisponibles = $tribunalModel->docentesDisponibles(
            $id_tutoria,
            $filtroTribunal !== '' ? $filtroTribunal : null,
            (int) $infoTutoria['id_carrera']
        );
    } else {
        $errores[] = 'La tutoría seleccionada no existe o no corresponde a una Modalidad de Grado.';
    }
}

require_once __DIR__ . '/../views/tutorias/tribunales.php';