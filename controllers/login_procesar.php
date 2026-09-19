<?php
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login/login.php');
    exit;
}
csrf_validar();

$usuarioInput = isset($_POST['usuario']) && is_scalar($_POST['usuario']) ? trim((string) $_POST['usuario']) : '';
$contrasenaInput = isset($_POST['contrasena']) && is_scalar($_POST['contrasena']) ? (string) $_POST['contrasena'] : '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$modelo = new UsuarioModel($pdo);
$usuario = $modelo->obtenerPorUsuario($usuarioInput);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM registro_accesos WHERE ip_origen = :ip AND fecha_hora >= (NOW() - INTERVAL 15 MINUTE) AND resultado = 'fallido' AND COALESCE(id_usuario, 0) = COALESCE(:id_usuario, 0)");
$stmt->execute([':ip' => $ip, ':id_usuario' => $usuario['id_usuario'] ?? null]);
if ((int) $stmt->fetchColumn() >= 5) {
    $_SESSION['login_error'] = 'No se puede iniciar sesión temporalmente. Inténtalo más tarde.';
    header('Location: ../views/login/login.php');
    exit;
}

$credencialesValidas = $usuario && password_verify($contrasenaInput, $usuario['contrasena_hash']);
if ($credencialesValidas && $usuario['estado'] === 'pendiente') {
    $mensaje = 'Tu cuenta está pendiente de aprobación.';
} elseif ($credencialesValidas && $usuario['estado'] === 'activo') {
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['apellido'] = $usuario['apellido'];
    $_SESSION['rol'] = $usuario['nombre_rol'];
    $_SESSION['ultima_actividad'] = time();
    $stmt = $pdo->prepare("INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (:id_usuario, :ip, 'exitoso')");
    $stmt->execute([':id_usuario' => $usuario['id_usuario'], ':ip' => $ip]);
    $destinos = ['administrador' => '../controllers/usuarios_listar.php', 'tutor' => '../views/tutor/panel.php', 'estudiante' => '../views/estudiante/panel.php'];
    header('Location: ' . ($destinos[$usuario['nombre_rol']] ?? '../views/login/login.php'));
    exit;
} else {
    $mensaje = 'Usuario o contraseña incorrectos.';
}

$stmt = $pdo->prepare("INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (:id_usuario, :ip, 'fallido')");
$stmt->bindValue(':id_usuario', $usuario['id_usuario'] ?? null, $usuario ? PDO::PARAM_INT : PDO::PARAM_NULL);
$stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
$stmt->execute();
$_SESSION['login_error'] = $mensaje;
header('Location: ../views/login/login.php');
exit;
