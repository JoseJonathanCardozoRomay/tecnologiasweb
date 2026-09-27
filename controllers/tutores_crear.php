<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$usuarioModel = new UsuarioModel($pdo);
$tutorModel = new TutorModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $datos = [
        'nombre'       => trim($_POST['nombre'] ?? ''),
        'apellido'     => trim($_POST['apellido'] ?? ''),
        'correo'       => trim($_POST['correo'] ?? ''),
        'usuario'      => trim($_POST['usuario'] ?? ''),
        'clave'        => trim($_POST['clave'] ?? ''),
        'especialidad' => trim($_POST['especialidad'] ?? 'Docencia Universitaria'),
    ];

    if (in_array('', [$datos['nombre'], $datos['apellido'], $datos['correo'], $datos['usuario'], $datos['clave']], true)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no tiene un formato válido.";
    }
    if (strlen($datos['clave']) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if ($usu_mail = $usuarioModel->obtenerPorUsuario($datos['usuario'])) {
        if ($usu_mail['usuario'] === $datos['usuario'] || $usu_mail['correo'] === $datos['correo']) {
            $errores[] = "El nombre de usuario o correo ya está en uso.";
        }
    }

    if (empty($errores)) {
        try {
            $usuarioModel->crear([
                'id_rol'   => 2,
                'nombre'   => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'correo'   => $datos['correo'],
                'usuario'  => $datos['usuario'],
                'clave'    => $datos['clave'],
            ]);

            $usuarioNuevo = $usuarioModel->obtenerPorUsuario($datos['usuario']);
            $tutorModel->crearPerfil((int) $usuarioNuevo['id_usuario'], $datos['especialidad'], '');

            flash_set('success', 'Tutor registrado correctamente. Ya puedes asignarle materias y horarios.');
            header('Location: tutores_listar.php');
            exit;
        } catch (Throwable $e) {
            $errores[] = "No se pudo registrar el tutor: verifica los datos e inténtalo nuevamente.";
        }
    }
}

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$especialidad = $_POST['especialidad'] ?? '';

require_once __DIR__ . '/../views/tutores/crear.php';