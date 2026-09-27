<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CartaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$tutorModel = new TutorModel($pdo);
$tutor = $tutorModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));

if (!$tutor) {
    flash_set('error', 'Todavía no tienes un perfil de tutor.');
    header('Location: /views/tutor/panel.php');
    exit;
}

$idTutor = (int) $tutor['id_tutor'];
$cartaModel = new CartaModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $accion   = $_POST['accion'] ?? '';
    $id_carta = (int) ($_POST['id_carta'] ?? 0);
    $motivo   = trim($_POST['motivo_rechazo'] ?? '');

    $carta = $cartaModel->obtenerPorId($id_carta);

    if (!$carta || (int) $carta['id_tutor'] !== $idTutor) {
        $errores[] = 'La carta seleccionada no existe o no te pertenece.';
    } elseif ($carta['estado'] !== 'pendiente') {
        $errores[] = 'La carta ya fue respondida.';
    } else {
        try {
            $id_operador = (int) ($_SESSION['id_usuario'] ?? 0);
            $historial = new HistorialModel($pdo);
            $notifs = new NotificationModel($pdo);
            $adminIds = $notifs->idsUsuariosAdministracion();

            if ($accion === 'aceptar') {
                $historial->registrar($id_operador, 'CARTA_ACEPTADA', 'Carta de designación #' . $id_carta . ' aceptada.');
                $cartaModel->aceptar($id_carta);
                foreach ($adminIds as $dest) {
                    $notifs->crear($dest, 'CARTA_ACEPTADA', 'El tutor aceptó la carta de designación #' . $id_carta . '.', 'tutorias_listar.php', $id_operador);
                }
                flash_set('success', 'Carta de designación aceptada. La tutoría quedó asignada.');
            } elseif ($accion === 'rechazar') {
                $historial->registrar($id_operador, 'CARTA_RECHAZADA', 'Carta de designación #' . $id_carta . ' rechazada: ' . mb_substr($motivo, 0, 200));
                $cartaModel->rechazar($id_carta, $motivo);
                foreach ($adminIds as $dest) {
                    $notifs->crear($dest, 'CARTA_RECHAZADA', 'El tutor rechazó la carta de designación #' . $id_carta . ' y la tutoría entró en reasignación.', 'tutorias_asignar.php', $id_operador);
                }
                flash_set('success', 'Carta rechazada. La tutoría quedó en reasignación.');
            } else {
                $errores[] = 'Acción no válida.';
            }
        } catch (InvalidArgumentException $e) {
            $errores[] = $e->getMessage();
        } catch (RuntimeException $e) {
            $errores[] = $e->getMessage();
        }
    }

    if (!empty($errores)) {
        flash_set('error', implode(' ', $errores));
    }

    header('Location: cartas_responder.php');
    exit;
}

$cartas = $cartaModel->obtenerPorTutor($idTutor);
$cupoDisponible = $cartaModel->cupoDisponible($idTutor);

require_once __DIR__ . '/../views/tutor/cartas.php';