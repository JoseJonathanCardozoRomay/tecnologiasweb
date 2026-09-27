<?php
require_once '../includes/sesion.php';
require_once '../includes/seguridad.php';
require_once '../includes/csrf.php';
require_once '../config/conexion.php';
require_once '../config/Response.php';
require_once '../models/UsuarioModel.php';
require_once '../models/HistorialModel.php';

$esJSON = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($esJSON) {
        Response::error('Método no permitido.', 405);
    }
    header('Location: ../views/login/login.php');
    exit;
}

if (!csrf_token_valido()) {
    http_response_code(403);
    if ($esJSON) {
        Response::error('Sesión expirada o solicitud inválida. Recarga la página e inténtalo nuevamente.', 403);
    }
    exit('Solicitud expirada o inválida. Regresa al formulario e inténtalo nuevamente.');
}

if (excede_limite_intentos($pdo)) {
    $espera = minutos_para_reintento($pdo);
    if ($esJSON) {
        Response::error('Demasiados intentos fallidos desde esta conexión. Intenta nuevamente en ' . $espera . ' minuto(s).', 429);
    }
    $_SESSION['login_error'] = 'Demasiados intentos fallidos desde esta conexión. Intenta nuevamente en ' . $espera . ' minuto(s).';
    header('Location: ../views/login/login.php');
    exit;
}

$raw = file_get_contents('php://input');
$cuerpoJSON = json_decode($raw, true);

$usuarioInput = trim($_POST['usuario'] ?? $cuerpoJSON['usuario'] ?? '');
// trim() solo descarta espacios/tabulaciones/nuevas linea/NUL de los extremos:
// conserva intactos los caracteres especiales de la clave (@ . + - etc.).
$contrasenaInput = trim((string) ($_POST['contrasena'] ?? $cuerpoJSON['contrasena'] ?? ''));

if (excede_limite_cuenta($pdo, $usuarioInput)) {
    if ($esJSON) {
        Response::error('Cuenta bloqueada temporalmente por intentos fallidos. Intenta nuevamente en ' . minutos_para_reintento($pdo) . ' minuto(s).', 429);
    }
    $_SESSION['login_error'] = 'Cuenta bloqueada temporalmente por intentos fallidos. Intenta nuevamente en ' . minutos_para_reintento($pdo) . ' minuto(s).';
    header('Location: ../views/login/login.php');
    exit;
}

$modelo = new UsuarioModel($pdo);
$usuario = $modelo->obtenerPorUsuario($usuarioInput);

function registrarAcceso(PDO $pdo, $id_usuario, $resultado): void
{
    $stmt = $pdo->prepare("INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (?, ?, ?)");
    $stmt->execute([$id_usuario, ip_cliente(), $resultado]);
}

if ($usuario && $usuario['estado'] === 'activo' && password_verify($contrasenaInput, $usuario['contrasena_hash'])) {
    session_regenerate_id(true);

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['apellido'] = $usuario['apellido'] ?? '';
    $_SESSION['rol'] = $usuario['nombre_rol'];

    unset($_SESSION['login_error']);

    registrarAcceso($pdo, $usuario['id_usuario'], 'exitoso');

    $historial = new HistorialModel($pdo);
    $historial->registrar((int) $usuario['id_usuario'], 'LOGIN', 'Inicio de sesión exitoso de ' . enmascarar_texto_auditable($usuario['nombre'] . ' ' . ($usuario['apellido'] ?? '')));

    if ($esJSON) {
        Response::json([
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'rol'        => $usuario['nombre_rol'],
        ], 200, 'Inicio de sesión correcto.');
    }

    switch ($usuario['nombre_rol']) {
        case 'administrador':
            header('Location: ../controllers/usuarios_listar.php');
            break;
        case 'auxiliar':
            header('Location: ../controllers/usuarios_listar.php');
            break;
        case 'tutor':
            header('Location: ../views/tutor/panel.php');
            break;
        case 'estudiante':
            header('Location: ../views/estudiante/panel.php');
            break;
        default:
            header('Location: ../views/login/login.php');
    }
    exit;
}

if ($usuario) {
    registrarAcceso($pdo, $usuario['id_usuario'], 'fallido');
} else {
    registrar_intento_fallido($pdo, null);
}

$historialFallido = new HistorialModel($pdo);
$mensajeFallo = 'Intento de acceso fallido desde ' . ip_cliente()
    . ' para el usuario "' . enmascarar_texto_auditable($usuarioInput) . '".';
$historialFallido->registrar(
    $usuario ? (int) $usuario['id_usuario'] : null,
    'LOGIN_FALLIDO',
    enmascarar_texto_auditable($mensajeFallo)
);

if ($esJSON) {
    Response::error('Usuario o contraseña incorrectos, o cuenta inactiva.', 401);
}

$_SESSION['login_error'] = 'Usuario o contraseña incorrectos, o cuenta inactiva.';
header('Location: ../views/login/login.php');
exit;