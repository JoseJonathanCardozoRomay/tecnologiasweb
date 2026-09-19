<?php
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validador.php';
require_once __DIR__ . '/../config/conexion.php';

if (!empty($_SESSION['id_usuario'])) {
    header('Location: /views/login/login.php');
    exit;
}

$errores = [];
$datos = [
    'nombre' => '', 'apellido' => '', 'correo' => '', 'usuario' => '',
    'id_carrera' => '', 'semestre' => '', 'registro_universitario' => '',
];
$carreras = $pdo->query('SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = isset($_POST[$campo]) && is_scalar($_POST[$campo]) ? trim((string) $_POST[$campo]) : '';
    }
    $clave = isset($_POST['clave']) && is_scalar($_POST['clave']) ? (string) $_POST['clave'] : '';
    $confirmar = isset($_POST['confirmar_clave']) && is_scalar($_POST['confirmar_clave']) ? (string) $_POST['confirmar_clave'] : '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (!empty($_POST['sitio_web'] ?? '')) $errores['general'] = 'No se pudo procesar el registro.';
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM registro_intentos WHERE ip_origen = :ip AND fecha_hora >= (NOW() - INTERVAL 1 HOUR)');
    $stmt->execute([':ip' => $ip]);
    if ((int) $stmt->fetchColumn() >= 5) $errores['general'] = 'Se alcanzó el límite temporal de registros. Inténtalo más tarde.';
    $pdo->prepare('INSERT INTO registro_intentos (ip_origen) VALUES (:ip)')->execute([':ip' => $ip]);

    foreach (['nombre' => 'nombre', 'apellido' => 'apellido'] as $campo => $etiqueta) {
        if (($error = validarNombre($datos[$campo], $etiqueta)) !== null) $errores[$campo] = $error;
    }
    $datos['correo'] = strtolower($datos['correo']);
    $datos['usuario'] = strtolower($datos['usuario']);
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) $errores['correo'] = 'El correo no tiene un formato válido.';
    $dominio = trim((string) getenv('REGISTRO_DOMINIO_PERMITIDO'));
    if ($dominio !== '' && strtolower(substr(strrchr($datos['correo'], '@') ?: '', 1)) !== strtolower($dominio)) $errores['correo'] = 'El correo debe pertenecer al dominio permitido.';
    if (!preg_match('/^[a-z0-9._-]{4,50}$/', $datos['usuario'])) $errores['usuario'] = 'El usuario debe tener entre 4 y 50 caracteres válidos.';
    if (strlen($clave) < 8 || !preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave)) $errores['clave'] = 'La contraseña debe tener al menos 8 caracteres, una letra y un número.';
    if ($clave !== $confirmar) $errores['confirmar_clave'] = 'Las contraseñas no coinciden.';
    $stmt = $pdo->prepare('SELECT 1 FROM carreras WHERE id_carrera = :id');
    $stmt->execute([':id' => (int) $datos['id_carrera']]);
    if (!filter_var($datos['id_carrera'], FILTER_VALIDATE_INT) || !$stmt->fetchColumn()) $errores['id_carrera'] = 'La carrera seleccionada no es válida.';
    if ((int) $datos['semestre'] < 1 || (int) $datos['semestre'] > 12) $errores['semestre'] = 'El semestre debe estar entre 1 y 12.';
    if ($datos['registro_universitario'] === '') $errores['registro_universitario'] = 'El registro universitario es obligatorio.';

    $stmt = $pdo->prepare('SELECT correo, usuario FROM usuarios WHERE correo = :correo OR usuario = :usuario');
    $stmt->execute([':correo' => $datos['correo'], ':usuario' => $datos['usuario']]);
    $duplicado = $stmt->fetch();
    if ($duplicado) {
        if (strcasecmp($duplicado['correo'], $datos['correo']) === 0) $errores['correo'] = 'El correo ya está registrado.';
        if (strcasecmp($duplicado['usuario'], $datos['usuario']) === 0) $errores['usuario'] = 'El nombre de usuario ya está en uso.';
    }
    $stmt = $pdo->prepare('SELECT 1 FROM estudiantes WHERE registro_universitario = :ru');
    $stmt->execute([':ru' => $datos['registro_universitario']]);
    if ($stmt->fetchColumn()) $errores['registro_universitario'] = 'El registro universitario ya está registrado.';

    if (!$errores) {
        try {
            $pdo->beginTransaction();
            $rol = $pdo->query("SELECT id_rol FROM roles WHERE nombre_rol = 'estudiante' LIMIT 1")->fetchColumn();
            $requiereAprobacion = getenv('REGISTRO_REQUIERE_APROBACION');
            $estado = ($requiereAprobacion === false || $requiereAprobacion === '' || $requiereAprobacion === '1') ? 'pendiente' : 'activo';
            $stmt = $pdo->prepare('INSERT INTO usuarios (id_rol, nombre, apellido, correo, usuario, contrasena_hash, estado) VALUES (:rol, :nombre, :apellido, :correo, :usuario, :hash, :estado)');
            $stmt->execute([':rol' => $rol, ':nombre' => $datos['nombre'], ':apellido' => $datos['apellido'], ':correo' => $datos['correo'], ':usuario' => $datos['usuario'], ':hash' => password_hash($clave, PASSWORD_DEFAULT), ':estado' => $estado]);
            $idUsuario = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario) VALUES (:usuario, :carrera, :semestre, :ru)');
            $stmt->execute([':usuario' => $idUsuario, ':carrera' => (int) $datos['id_carrera'], ':semestre' => (int) $datos['semestre'], ':ru' => $datos['registro_universitario']]);
            $pdo->commit();
            $_SESSION['login_error'] = $estado === 'pendiente' ? 'Tu cuenta fue creada y está pendiente de aprobación.' : 'Cuenta creada, ya puedes iniciar sesión.';
            header('Location: /views/login/login.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            $errores['general'] = 'No se pudo completar el registro.';
        }
    }
}
require_once __DIR__ . '/../views/registro/registro.php';
