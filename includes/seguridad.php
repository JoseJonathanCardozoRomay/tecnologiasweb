<?php

const CSP_SCRIPT_SRC = "'self' 'unsafe-inline' https://cdn.jsdelivr.net";
const CSP_STYLE_SRC  = "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com";
const CSP_FONT_SRC   = "'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:";

const LIMITE_INTENTOS_POR_IP      = 20;
const LIMITE_INTENTOS_POR_CUENTA  = 5;
const VENTANA_INTENTOS_LOGIN_MIN  = 15;

function csp_actual(): string
{
    return implode('; ', [
        "default-src 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'",
        "form-action 'self'",
        'script-src ' . CSP_SCRIPT_SRC,
        'style-src ' . CSP_STYLE_SRC,
        'font-src ' . CSP_FONT_SRC,
        "img-src 'self' data: blob:",
        "connect-src 'self'",
        "frame-src 'self'",
        "media-src 'self'",
        "object-src 'none'",
    ]);
}

function peticion_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        return true;
    }
    return (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
}

function enviar_cabeceras_seguridad(): void
{
    if (headers_sent()) {
        return;
    }

    header('Content-Security-Policy: ' . csp_actual());
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header_remove('X-Powered-By');
    header_remove('Server');

    if (peticion_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

const CLAVES_SENSIBLES = [
    'contrasena', 'contrasena_hash', 'contrasenia', 'contraseña', 'contraseña_hash',
    'password', 'passwd', 'pwd', 'clave', 'secret', 'token', 'csrf_token',
    'api_key', 'apikey', 'authorization', 'session', 'cookie',
];

function clave_es_sensible(string $clave): bool
{
    $normalizada = strtolower(trim($clave));
    if (in_array($normalizada, CLAVES_SENSIBLES, true)) {
        return true;
    }
    foreach (CLAVES_SENSIBLES as $sensible) {
        if (strpos($normalizada, $sensible) !== false) {
            return true;
        }
    }
    return false;
}

function enmascarar_texto_auditable(?string $texto): string
{
    if ($texto === null || $texto === '') {
        return (string) $texto;
    }

    $sustituido = preg_replace(
        '/((?:contrasena|contrase\s*na|password|passwd|pwd|clave|secret|token|csrf_token|api[_-]?key|authorization)'
        . '\s*["\']?\s*(?:=>|:|=)\s*["\']?)([^"\'\s,;&}\]]+)/iu',
        '$1[REDACTADO]',
        $texto
    );

    $sustituido = preg_replace('/\bBearer\s+[A-Za-z0-9._\-]+/i', 'Bearer [REDACTADO]', (string) $sustituido);

    return (string) $sustituido;
}

function enmascarar_datos_auditable($datos)
{
    if (is_array($datos)) {
        $salida = [];
        foreach ($datos as $clave => $valor) {
            if ((string) $clave === 'contrasena_hash' || clave_es_sensible((string) $clave)) {
                $salida[$clave] = '[REDACTADO]';
                continue;
            }
            $salida[$clave] = enmascarar_datos_auditable($valor);
        }
        return $salida;
    }

    if (is_string($datos)) {
        return enmascarar_texto_auditable($datos);
    }

    return $datos;
}

function enmascarar_json_auditable(?string $json): ?string
{
    if ($json === null || trim($json) === '') {
        return $json;
    }

    $decodificado = json_decode($json, true);
    if (is_array($decodificado)) {
        $enmascarado = json_encode(
            enmascarar_datos_auditable($decodificado),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($enmascarado !== false) {
            return $enmascarado;
        }
    }

    return enmascarar_texto_auditable($json);
}

function obtenerIpClienteReal(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

function ip_cliente(): string
{
    return obtenerIpClienteReal();
}

function excede_limite_cuenta(PDO $pdo, string $cuenta, int $maxIntentos = LIMITE_INTENTOS_POR_CUENTA, int $ventanaMinutos = VENTANA_INTENTOS_LOGIN_MIN): bool
{
    $cuenta = trim($cuenta);
    if ($cuenta === '') {
        return false;
    }

    try {
        $desde = date('Y-m-d H:i:s', time() - ($ventanaMinutos * 60));
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
               FROM registro_accesos r
               LEFT JOIN usuarios u ON u.id_usuario = r.id_usuario
              WHERE r.resultado = 'fallido'
                AND r.fecha_hora >= :desde
                AND (u.usuario = :cuenta OR u.correo = :cuenta_correo)"
        );
        $stmt->execute([':desde' => $desde, ':cuenta' => $cuenta, ':cuenta_correo' => $cuenta]);
        return ((int) $stmt->fetchColumn()) >= $maxIntentos;
    } catch (PDOException $e) {
        error_log('[seguridad] limite por cuenta: ' . $e->getMessage());
        return false;
    }
}

function excede_limite_intentos(PDO $pdo, int $maxIntentos = LIMITE_INTENTOS_POR_IP, int $ventanaMinutos = VENTANA_INTENTOS_LOGIN_MIN): bool
{
    try {
        $desde = date('Y-m-d H:i:s', time() - ($ventanaMinutos * 60));
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM registro_accesos
              WHERE ip_origen = :ip
                AND resultado = 'fallido'
                AND fecha_hora >= :desde"
        );
        $stmt->execute([':ip' => ip_cliente(), ':desde' => $desde]);
        return ((int) $stmt->fetchColumn()) >= $maxIntentos;
    } catch (PDOException $e) {
        error_log('[seguridad] limite por IP: ' . $e->getMessage());
        return false;
    }
}

function minutos_para_reintento(PDO $pdo, int $ventanaMinutos = VENTANA_INTENTOS_LOGIN_MIN): int
{
    try {
        $stmt = $pdo->prepare(
            "SELECT TIMESTAMPDIFF(SECOND, MIN(fecha_hora), NOW()) FROM registro_accesos
              WHERE ip_origen = :ip
                AND resultado = 'fallido'
                AND fecha_hora >= :desde"
        );
        $desde = date('Y-m-d H:i:s', time() - ($ventanaMinutos * 60));
        $stmt->execute([':ip' => ip_cliente(), ':desde' => $desde]);
        $segundos = (int) $stmt->fetchColumn();
        $restantes = ($ventanaMinutos * 60) - $segundos;
        return $restantes > 0 ? (int) ceil($restantes / 60) : 1;
    } catch (PDOException $e) {
        error_log('[seguridad] espera de reintento: ' . $e->getMessage());
        return $ventanaMinutos;
    }
}

function registrar_intento_fallido(PDO $pdo, $id_usuario = null): void
{
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (:id_usuario, :ip, 'fallido')"
        );
        $stmt->execute([':id_usuario' => $id_usuario, ':ip' => ip_cliente()]);
    } catch (PDOException $e) {
        error_log('[seguridad] registro de intento fallido: ' . $e->getMessage());
    }
}
