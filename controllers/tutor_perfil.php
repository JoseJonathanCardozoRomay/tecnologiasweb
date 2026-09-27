<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$tutorModel = new TutorModel($pdo);
$usuarioModel = new UsuarioModel($pdo);

$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);
$id_tutor = null;

$tutorVinculado = $tutorModel->obtenerPorUsuario($id_usuario);
if ($tutorVinculado) {
    $id_tutor = (int) $tutorVinculado['id_tutor'];
}

if (!$id_tutor) {
    flash_set('danger', 'No se encontró un perfil de docente asociado a tu usuario.');
    header('Location: /views/tutor/panel.php');
    exit;
}

$errores = [];
$valores = [
    'nombre'       => (string) ($tutorVinculado['nombre'] ?? ''),
    'apellido'     => (string) ($tutorVinculado['apellido'] ?? ''),
    'correo'       => (string) ($tutorVinculado['correo'] ?? ''),
    'telefono'     => (string) ($tutorVinculado['telefono'] ?? ''),
    'especialidad' => (string) ($tutorVinculado['especialidad'] ?? ''),
    'biografia'    => (string) ($tutorVinculado['biografia'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_token_valido()) {
        flash_set('danger', 'La sesión expiró. Recarga la página e intenta nuevamente.');
        header('Location: tutor_perfil.php');
        exit;
    }
    csrf_validar();

    $valores = [
        'nombre'       => trim($_POST['nombre'] ?? ''),
        'apellido'     => trim($_POST['apellido'] ?? ''),
        'correo'       => trim($_POST['correo'] ?? ''),
        'telefono'     => trim($_POST['telefono'] ?? ''),
        'especialidad' => trim($_POST['especialidad'] ?? ''),
        'biografia'    => trim($_POST['biografia'] ?? ''),
    ];

    if ($valores['nombre'] === '') {
        $errores[] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($valores['nombre']) > 100) {
        $errores[] = 'El nombre no puede superar los 100 caracteres.';
    }

    if ($valores['apellido'] === '') {
        $errores[] = 'El apellido es obligatorio.';
    } elseif (mb_strlen($valores['apellido']) > 100) {
        $errores[] = 'El apellido no puede superar los 100 caracteres.';
    }

    if (!filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no tiene un formato válido.';
    } elseif (mb_strlen($valores['correo']) > 150) {
        $errores[] = 'El correo no puede superar los 150 caracteres.';
    }

    if ($valores['telefono'] !== '' && !preg_match('/^[0-9+\-\s()]{6,20}$/', $valores['telefono'])) {
        $errores[] = 'El teléfono solo admite números, espacios, guiones y el signo +.';
    }

    if (mb_strlen($valores['especialidad']) > 150) {
        $errores[] = 'La especialidad no puede superar los 150 caracteres.';
    }

    if (mb_strlen($valores['biografia']) > 1000) {
        $errores[] = 'La biografía no puede superar los 1000 caracteres.';
    }

    if (!$errores) {
        try {
            $pdo->beginTransaction();

            $usuarioModel->actualizarPerfilPropio($id_usuario, $valores);
            $tutorModel->actualizarPerfil($id_tutor, $valores['especialidad'], $valores['biografia']);

            $pdo->commit();

            $_SESSION['nombre'] = $valores['nombre'];
            $_SESSION['apellido'] = $valores['apellido'];

            flash_set('success', 'Tus datos se actualizaron correctamente.');
            header('Location: tutor_perfil.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errores[] = $e->getCode() === '23000'
                ? 'El correo ingresado ya está registrado por otro usuario.'
                : 'No se pudieron guardar los datos. Intenta nuevamente.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errores[] = 'Ocurrió un error inesperado al guardar tu perfil.';
        }
    }
}

$tutor = $tutorModel->obtenerPorId($id_tutor);
$carreras = $tutorModel->carrerasDelTutor($id_tutor);
$usuario = $usuarioModel->obtenerPorId($id_usuario);

$tituloPagina = 'Mi perfil - UPDS';
require __DIR__ . '/../views/tutor/perfil.php';
