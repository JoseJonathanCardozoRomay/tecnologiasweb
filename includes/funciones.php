<?php

declare(strict_types=1);

function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        session_start();
    }
}

iniciarSesion();

function e(mixed $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function redirect(string $ruta): never
{
    header('Location: ' . $ruta);
    exit;
}

function dashboardPorRol(?string $rol = null): string
{
    $rol = $rol ?? ($_SESSION['rol'] ?? '');
    return match ($rol) {
        'administrador' => '/controllers/dashboard.php',
        'tutor' => '/views/tutor/panel.php',
        'estudiante' => '/views/estudiante/panel.php',
        default => '/views/login/login.php',
    };
}

function esAdministrador(): bool
{
    return ($_SESSION['rol'] ?? '') === 'administrador';
}

function esTutor(): bool
{
    return ($_SESSION['rol'] ?? '') === 'tutor';
}

function esEstudiante(): bool
{
    return ($_SESSION['rol'] ?? '') === 'estudiante';
}

function validarId(mixed $id): ?int
{
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
}

function textoValido(string $valor, int $min, int $max): bool
{
    $valor = trim($valor);
    $len = function_exists('mb_strlen') ? mb_strlen($valor) : strlen($valor);
    return $len >= $min && $len <= $max;
}

function normalizarTexto(string $valor): string
{
    $valor = trim($valor);
    return preg_replace('/\s+/u', ' ', $valor) ?? $valor;
}

function fechaValida(string $fecha): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return false;
    }
    [$y, $m, $d] = array_map('intval', explode('-', $fecha));
    return checkdate($m, $d, $y);
}

function horaValida(string $hora): bool
{
    return (bool)preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $hora);
}

function telefonoValido(string $telefono): bool
{
    return $telefono === '' || (bool)preg_match('/^[0-9+()\- .]{7,20}$/', $telefono);
}

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function mostrarFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['tipo']) . ' alert-dismissible fade show shadow-sm" role="alert">'
            . '<i class="bi bi-info-circle-fill me-2"></i>' . e($f['mensaje'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

function csrfToken(): string
{
    iniciarSesion();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function validarCsrf(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function exigirCsrf(): void
{
    if (!validarCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('La sesión de seguridad expiró. Recarga la página e inténtalo nuevamente.');
    }
}

function requireRole(array $roles): void
{
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
        http_response_code(403);
        $tituloPagina = 'Acceso no autorizado - Sistema de Tutorías';
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
}

function estadoBadge(string $estado): string
{
    return match ($estado) {
        'pendiente' => 'warning',
        'confirmada' => 'primary',
        'rechazada' => 'danger',
        'realizada' => 'success',
        'cancelada' => 'secondary',
        'activo' => 'success',
        'inactivo' => 'secondary',
        default => 'light',
    };
}

function estadoEtiqueta(string $estado): string
{
    return match ($estado) {
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'rechazada' => 'Rechazada',
        'realizada' => 'Realizada',
        'cancelada' => 'Cancelada',
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        default => ucfirst($estado),
    };
}

function diaSemanaEspanol(string $fecha): ?string
{
    if (!fechaValida($fecha)) {
        return null;
    }
    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 7 => null];
    return $dias[(int)date('N', strtotime($fecha))] ?? null;
}

function registrarAuditoria(PDO $pdo, ?int $idUsuario, string $resultado, string $accion = 'ACCESO', string $modulo = 'Autenticación', string $descripcion = ''): void
{
    try {
        $stmt = $pdo->prepare('INSERT INTO registro_accesos (id_usuario, ip_origen, resultado, accion, modulo, descripcion) VALUES (:u, :ip, :r, :a, :m, :d)');
        $stmt->execute([
            ':u' => $idUsuario,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':r' => $resultado,
            ':a' => $accion,
            ':m' => $modulo,
            ':d' => $descripcion ?: null,
        ]);
    } catch (Throwable $e) {
        try {
            $stmt = $pdo->prepare('INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (:u, :ip, :r)');
            $stmt->execute([
                ':u' => $idUsuario,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':r' => $resultado,
            ]);
        } catch (Throwable $ignored) {
            // La auditoría nunca debe impedir que la operación principal continúe.
        }
    }
}

function registrarAccion(PDO $pdo, string $accion, string $modulo, string $descripcion = ''): void
{
    registrarAuditoria(
        $pdo,
        validarId($_SESSION['id_usuario'] ?? null),
        'exitoso',
        $accion,
        $modulo,
        $descripcion
    );
}
