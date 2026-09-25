<?php
/**
 * Crear Estudiante — Versión final corregida
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

if (!tieneRol(['administrador'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

$modelo = new EstudianteModel();
$carreraModel = new CarreraModel();
$usuarioModel = new UsuarioModel();

$error = '';
$carreras = $carreraModel->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $id_carrera = (int)($_POST['id_carrera'] ?? 0);
    $semestre = (int)($_POST['semestre'] ?? 0);
    $registro_universitario = trim($_POST['registro_universitario'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($contrasena) || $id_carrera <= 0 || $semestre <= 0 || empty($registro_universitario)) {
        $error = 'Completa todos los campos con *';
    } else {
        global $conexion;
        try {
            $conexion->beginTransaction();

            // ✅ Usuario único automático
            $usuario_base = strtolower($nombre[0] . $apellido);
            $usuario = $usuario_base;
            $n = 1;
            while (true) {
                $check = $conexion->prepare("SELECT 1 FROM usuarios WHERE usuario = ? LIMIT 1");
                $check->execute([$usuario]);
                if (!$check->rowCount()) break;
                $usuario = $usuario_base . $n++;
            }

            // ✅ Crear usuario
            $datosUsuario = [
                'id_rol' => 3,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'usuario' => $usuario,
                'contrasena' => $contrasena,
                'telefono' => $telefono,
                'estado' => 'activo'
            ];
            $usuarioModel->crear($datosUsuario);

            // ✅ Obtener ID del usuario nuevo
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ? LIMIT 1");
            $stmt->execute([$usuario]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_usuario = $u['id_usuario'];

            // ✅ Usar tu modelo que ya está bien
            $modelo->crear($id_usuario, $id_carrera, $semestre, $registro_universitario);

            $conexion->commit();
            header("Location: index.php?accion=estudiantes_listar");
            exit;
        } catch (Exception $e) {
            if ($conexion->inTransaction()) $conexion->rollBack();
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../views/estudiantes/crear.php';