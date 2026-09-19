<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/validador.php';
require_once __DIR__ . '/../includes/csrf.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);
$estudianteModel = new EstudianteModel($pdo);
$tutorModel = new TutorModel($pdo);
$carreraModel = new CarreraModel($pdo);
$id = $_GET['id'] ?? $_POST['id_usuario'] ?? null;
if (!$id || !($usuario_actual = $usuarioModel->obtenerPorId($id))) {
    header('Location: usuarios_listar.php');
    exit;
}

$roles = $rolModel->obtenerTodos();
$rolesPorNombre = array_column($roles, 'id_rol', 'nombre_rol');
$idRolEstudiante = (int) ($rolesPorNombre['estudiante'] ?? 0);
$idRolTutor = (int) ($rolesPorNombre['tutor'] ?? 0);
$carreras = $carreraModel->obtenerTodas();
$perfilEstudianteActual = $estudianteModel->obtenerPorUsuario($id);
$perfilTutorActual = $tutorModel->obtenerPorUsuario($id);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $datos = [
        'id_rol' => (int) ($_POST['id_rol'] ?? 0),
        'nombre' => normalizarTexto($_POST['nombre'] ?? ''),
        'apellido' => normalizarTexto($_POST['apellido'] ?? ''),
        'correo' => strtolower(trim($_POST['correo'] ?? '')),
        'usuario' => strtolower(trim($_POST['usuario'] ?? '')),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'clave' => $_POST['clave'] ?? '',
        'estado' => $_POST['estado'] ?? 'activo',
        'id_carrera' => (int) ($_POST['id_carrera'] ?? 0),
        'semestre' => (int) ($_POST['semestre'] ?? 0),
        'registro_universitario' => trim($_POST['registro_universitario'] ?? ''),
    ];
    $perfilEstudiante = $perfilEstudianteActual;
    $perfilTutor = $perfilTutorActual;

    if (!$rolModel->existe($datos['id_rol'])) $errores[] = 'El rol seleccionado no es válido.';
    foreach (['nombre', 'apellido'] as $campo) {
        if (($error = validarNombre($datos[$campo], $campo)) !== null) $errores[] = $error;
    }
    if (($error = validarLongitud($datos['usuario'], 4, 50, 'nombre de usuario')) !== null) {
        $errores[] = $error;
    } elseif (!preg_match('/^[a-z0-9._-]+$/', $datos['usuario'])) {
        $errores[] = 'El nombre de usuario solo puede contener letras minúsculas, números, puntos, guiones y guiones bajos.';
    } elseif ($usuarioModel->existeUsuario($datos['usuario'], $id)) {
        $errores[] = 'El nombre de usuario ya está en uso.';
    }
    if (($error = validarLongitud($datos['correo'], 1, 150, 'correo')) !== null) {
        $errores[] = $error;
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no tiene un formato válido.';
    } elseif ($usuarioModel->existeCorreo($datos['correo'], $id)) {
        $errores[] = 'El correo ya está registrado.';
    }
    if (($error = validarTelefono($datos['telefono'])) !== null) $errores[] = $error;
    if ($datos['clave'] !== '' && (strlen($datos['clave']) < 8 || !preg_match('/[A-Za-z]/', $datos['clave']) || !preg_match('/\d/', $datos['clave']))) $errores[] = 'La contraseña debe tener al menos 8 caracteres, una letra y un número.';
    if (!in_array($datos['estado'], ['activo', 'inactivo'], true)) $errores[] = 'El estado seleccionado no es válido.';
    if ((int) $id === (int) $_SESSION['id_usuario'] && $datos['estado'] !== 'activo') $errores[] = 'No puedes desactivar tu propia cuenta de administrador.';

    if ($datos['id_rol'] === $idRolEstudiante && !$perfilEstudiante) {
        if (!$datos['id_carrera'] || $datos['semestre'] < 1 || $datos['semestre'] > 12 || $datos['registro_universitario'] === '') {
            $errores[] = 'Para un estudiante, carrera, semestre y registro universitario son obligatorios.';
        } elseif ($estudianteModel->existeRegistroUniversitario($datos['registro_universitario'])) {
            $errores[] = 'El registro universitario ya está en uso.';
        }
    }

    if (!$errores) {
        try {
            $pdo->beginTransaction();
            $usuarioModel->actualizar($id, $datos);
            if ($datos['id_rol'] === $idRolEstudiante && !$perfilEstudiante) {
                $stmt = $pdo->prepare('INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario) VALUES (:usuario, :carrera, :semestre, :ru)');
                $stmt->execute([':usuario' => $id, ':carrera' => $datos['id_carrera'], ':semestre' => $datos['semestre'], ':ru' => $datos['registro_universitario']]);
            } elseif ($datos['id_rol'] === $idRolTutor && !$perfilTutor) {
                $stmt = $pdo->prepare("INSERT INTO tutores (id_usuario, especialidad) VALUES (:usuario, 'Docente UPDS')");
                $stmt->execute([':usuario' => $id]);
            }
            $pdo->commit();
            header('Location: usuarios_listar.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            $errores[] = 'No se pudo actualizar el usuario.';
        }
    }
}

require_once __DIR__ . '/../views/usuarios/editar.php';
