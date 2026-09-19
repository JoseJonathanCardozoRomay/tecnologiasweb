<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/validador.php';
require_once __DIR__ . '/../includes/csrf.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $datos = [
        'id_rol'   => (int) ($_POST['id_rol'] ?? 0),
        'nombre'   => normalizarTexto($_POST['nombre'] ?? ''),
        'apellido' => normalizarTexto($_POST['apellido'] ?? ''),
        'correo'   => strtolower(trim($_POST['correo'] ?? '')),
        'usuario'  => strtolower(trim($_POST['usuario'] ?? '')),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'clave'    => $_POST['clave'] ?? '',
        'id_carrera' => (int) ($_POST['id_carrera'] ?? 0),
        'semestre' => (int) ($_POST['semestre'] ?? 0),
        'registro_universitario' => trim($_POST['registro_universitario'] ?? ''),
    ];

    if (!$rolModel->existe($datos['id_rol'])) {
        $errores[] = 'El rol seleccionado no es válido.';
    }
    foreach ([['nombre', 'nombre'], ['apellido', 'apellido']] as [$campo, $etiqueta]) {
        if (($error = validarNombre($datos[$campo], $etiqueta)) !== null) {
            $errores[] = $error;
        }
    }
    if (($error = validarLongitud($datos['usuario'], 4, 50, 'nombre de usuario')) !== null) {
        $errores[] = $error;
    } elseif (!preg_match('/^[a-z0-9._-]+$/', $datos['usuario'])) {
        $errores[] = 'El nombre de usuario solo puede contener letras minúsculas, números, puntos, guiones y guiones bajos.';
    } elseif ($usuarioModel->existeUsuario($datos['usuario'])) {
        $errores[] = 'El nombre de usuario ya está en uso.';
    }
    if (($error = validarLongitud($datos['correo'], 1, 150, 'correo')) !== null) {
        $errores[] = $error;
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no tiene un formato válido.";
    } elseif ($usuarioModel->existeCorreo($datos['correo'])) {
        $errores[] = 'El correo ya está registrado.';
    }
    if (strlen($datos['clave']) < 8 || !preg_match('/[A-Za-z]/', $datos['clave']) || !preg_match('/\d/', $datos['clave'])) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres, una letra y un número.';
    }
    if (($error = validarTelefono($datos['telefono'])) !== null) {
        $errores[] = $error;
    }
    if ($datos['id_rol'] === (int) $rolModel->obtenerIdRol('estudiante')) {
        if (!$datos['id_carrera'] || $datos['semestre'] < 1 || $datos['semestre'] > 12 || $datos['registro_universitario'] === '') {
            $errores[] = 'Para un estudiante, carrera, semestre y registro universitario son obligatorios.';
        }
    }

    if (empty($errores)) {
        try {
            $pdo->beginTransaction();
            $usuarioModel->crear($datos);
            $idNuevo = (int) $pdo->lastInsertId();
            if ($datos['id_rol'] === (int) $rolModel->obtenerIdRol('estudiante')) {
                $stmt = $pdo->prepare('INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario) VALUES (:usuario, :carrera, :semestre, :ru)');
                $stmt->execute([':usuario' => $idNuevo, ':carrera' => $datos['id_carrera'], ':semestre' => $datos['semestre'], ':ru' => $datos['registro_universitario']]);
            } elseif ($datos['id_rol'] === (int) $rolModel->obtenerIdRol('tutor')) {
                $stmt = $pdo->prepare("INSERT INTO tutores (id_usuario, especialidad) VALUES (:usuario, 'Docente UPDS')");
                $stmt->execute([':usuario' => $idNuevo]);
            }
            $pdo->commit();
            header("Location: usuarios_listar.php");
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            if ($e->getCode() === '23000' && $usuarioModel->existeCorreo($datos['correo'])) {
                $errores[] = 'El correo ya está registrado.';
            } elseif ($e->getCode() === '23000' && $usuarioModel->existeUsuario($datos['usuario'])) {
                $errores[] = 'El nombre de usuario ya está en uso.';
            } else {
                $errores[] = 'No se pudo registrar el usuario.';
            }
        }
    }
}

$roles = $rolModel->obtenerTodos();
$carreras = (new CarreraModel($pdo))->obtenerTodas();
require_once __DIR__ . '/../views/usuarios/crear.php';