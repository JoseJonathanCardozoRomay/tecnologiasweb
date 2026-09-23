<?php

declare(strict_types=1);

/**
 * Inicia una sesión única para toda la aplicación.
 * La fusión mantiene la sesión existente y solo centraliza sus helpers.
 */
function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'path' => '/',
        ]);
        session_start();
    }

    if (!empty($_SESSION['id_usuario'])) {
        $_SESSION['ultima_actividad'] = time();
    }
}

iniciarSesion();

/**
 * Escapa texto para HTML y corrige de forma defensiva el texto mojibake
 * producido cuando una cadena UTF-8 fue interpretada previamente como
 * Windows-1252/Latin-1 (por ejemplo, "TecnologÃ­a").
 *
 * La corrección se aplica solo cuando aparecen marcadores típicos de esa
 * corrupción, para no alterar textos UTF-8 que ya son correctos.
 */
function repararMojibake(string $texto): string
{
    /*
     * Corrige cadenas UTF-8 que en algún punto fueron interpretadas como
     * Windows-1252/Latin-1 y volvieron a almacenarse o mostrarse como texto
     * UTF-8 (por ejemplo: "TecnologÃ­a", "Ãšrsula", "Ã“scar").
     *
     * No usamos utf8_decode(): además de estar obsoleta en PHP moderno,
     * devuelve una cadena Latin-1 que puede volver a romper el HTML.
     *
     * La estrategia es convertir solo cuando detectamos marcadores típicos
     * de mojibake y aceptar el resultado únicamente si reduce la corrupción.
     */
    if ($texto === '' || !preg_match('/[ÃÂâ]/u', $texto)) {
        return $texto;
    }

    if (!function_exists('iconv')) {
        return $texto;
    }

    $actual = $texto;
    for ($intento = 0; $intento < 3; $intento++) {
        if (!preg_match('/[ÃÂâ]/u', $actual)) {
            break;
        }

        // Al re-interpretar los caracteres corruptos como Windows-1252,
        // las secuencias "Ã¡", "Ãš", "Ã“", etc. vuelven a sus
        // bytes UTF-8 originales y recuperan el carácter correcto.
        $candidato = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $actual);
        if (!is_string($candidato) || $candidato === '' || !preg_match('//u', $candidato)) {
            break;
        }

        $marcadoresActual = preg_match_all('/[ÃÂâ]/u', $actual, $tmpActual) ?: 0;
        $marcadoresCandidato = preg_match_all('/[ÃÂâ]/u', $candidato, $tmpCandidato) ?: 0;

        // Solo aceptamos la transformación si reduce las marcas de corrupción.
        if ($candidato === $actual || $marcadoresCandidato >= $marcadoresActual) {
            break;
        }

        $actual = $candidato;
    }

    return $actual;
}

function e(mixed $valor): string
{
    $texto = repararMojibake((string)$valor);
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
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

function esAdministrador(): bool { return ($_SESSION['rol'] ?? '') === 'administrador'; }
function esTutor(): bool { return ($_SESSION['rol'] ?? '') === 'tutor'; }
function esEstudiante(): bool { return ($_SESSION['rol'] ?? '') === 'estudiante'; }

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
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) return false;
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

/**
 * Guarda mensajes flash en el formato usado por el proyecto base.
 * La vista de layout los transforma en Bootstrap Toast automáticamente.
 */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['_flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function mostrarFlash(): void
{
    // El header global consume estos mensajes y los muestra como Toast.
    // Se mantiene la función para conservar compatibilidad con las vistas heredadas.
}

function flash_get(): array
{
    $mensajes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return is_array($mensajes) ? $mensajes : [];
}

function flash_set(string $tipo, string $mensaje): void
{
    flash($tipo, $mensaje);
}

function csrfToken(): string
{
    iniciarSesion();
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/** Alias compatible con las helpers del proyecto base. */
function csrf_token(): string { return csrfToken(); }
function csrf_campo(): string { return csrfField(); }

function validarCsrf(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['_csrf'])
        && hash_equals((string)$_SESSION['_csrf'], $token);
}

function exigirCsrf(): void
{
    if (!validarCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('La sesión de seguridad expiró. Recarga la página e inténtalo nuevamente.');
    }
}

function csrf_validar(): void { exigirCsrf(); }

function requireRole(array $roles): void
{
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
        http_response_code(403);
        $tituloPagina = 'Acceso no autorizado - Sistema de Tutorías';
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
}

function requerirRol(array $roles): void { requireRole($roles); }
function requerirSesion(): void
{
    if (empty($_SESSION['id_usuario'])) redirect('/views/login/login.php');
}
function usuarioActual(): array
{
    return [
        'id_usuario' => (int)($_SESSION['id_usuario'] ?? 0),
        'nombre' => (string)($_SESSION['nombre'] ?? ''),
        'apellido' => (string)($_SESSION['apellido'] ?? ''),
        'rol' => (string)($_SESSION['rol'] ?? ''),
    ];
}

function estadoBadge(string $estado): string
{
    return match ($estado) {
        'pendiente' => 'warning',
        'confirmada' => 'primary',
        'rechazada' => 'danger',
        'realizada' => 'success',
        'cancelada' => 'secondary',
        'en_proceso', 'en_curso' => 'warning',
        'detenido' => 'dark',
        'programada' => 'primary',
        'aprobada' => 'success',
        'inscrito' => 'success',
        'asistio' => 'success',
        'no_asistio' => 'danger',
        'retirado' => 'secondary',
        'activo' => 'success',
        'inactivo' => 'secondary',
        default => 'light',
    };
}

/**
 * Etiquetas específicas del módulo de proyectos de grado.
 * El valor interno `en_proceso` se presenta al usuario como "En curso"
 * y `finalizado` se presenta como "Concluido", manteniendo compatibilidad
 * con el esquema existente sin introducir una migración de base de datos.
 */
function estadoProyectoEtiqueta(string $estado): string
{
    return match ($estado) {
        'propuesto' => 'Propuesto',
        'en_proceso' => 'En curso',
        'finalizado' => 'Concluido',
        'cancelado' => 'Cancelado',
        default => ucwords(str_replace('_', ' ', $estado)),
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
        'en_proceso' => 'En proceso',
        'en_curso' => 'En curso',
        'detenido' => 'Detenida',
        'programada' => 'Programada',
        'aprobada' => 'Aprobada',
        'inscrito' => 'Aprobada',
        'asistio' => 'Asistió',
        'no_asistio' => 'No asistió',
        'retirado' => 'Retirado',
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        default => ucfirst(str_replace('_', ' ', $estado)),
    };
}

function diaSemanaEspanol(string $fecha): ?string
{
    if (!fechaValida($fecha)) return null;
    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 7 => null];
    return $dias[(int)date('N', strtotime($fecha))] ?? null;
}

/** Registra un evento de auditoría sin bloquear la operación principal si falla el log. */
function registrarAuditoria(PDO $pdo, ?int $idUsuario, string $resultado, string $accion = 'ACCESO', string $modulo = 'Autenticación', string $descripcion = ''): void
{
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO registro_accesos (id_usuario, ip_origen, resultado, accion, modulo, descripcion)
             VALUES (:u, :ip, :r, :a, :m, :d)'
        );
        $stmt->execute([
            ':u' => $idUsuario,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':r' => $resultado,
            ':a' => $accion,
            ':m' => $modulo,
            ':d' => $descripcion !== '' ? $descripcion : null,
        ]);
    } catch (Throwable $e) {
        // La auditoría es complementaria; nunca debe romper la acción principal.
        error_log('Auditoría no registrada: ' . $e->getMessage());
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

/**
 * Valida que una solicitud personal tenga coherencia académica y de agenda.
 * Las tutorías grupales tienen su propia validación en TutoriaGrupalModel.
 */
function validarTutoriaPersonal(PDO $pdo, array $datos, ?int $excepto = null): array
{
    $errores = [];
    $idEstudiante = (int)($datos['id_estudiante'] ?? 0);
    $idTutor = (int)($datos['id_tutor'] ?? 0);
    $idProyecto = (int)($datos['id_proyecto'] ?? 0);

    $stmt = $pdo->prepare(
        'SELECT e.id_carrera, c.estado AS estado_carrera, u.estado AS estado_usuario
         FROM estudiantes e
         INNER JOIN carreras c ON c.id_carrera=e.id_carrera
         INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
         WHERE e.id_estudiante=:id'
    );
    $stmt->execute([':id' => $idEstudiante]);
    $estudiante = $stmt->fetch();
    if (!$estudiante) return ['El estudiante no existe.'];
    if ($estudiante['estado_usuario'] !== 'activo') $errores[] = 'La cuenta del estudiante está inactiva.';
    if ($estudiante['estado_carrera'] !== 'activo') $errores[] = 'La carrera del estudiante está inactiva.';

    $stmt = $pdo->prepare(
        'SELECT p.id_proyecto,p.id_estudiante,p.id_carrera,p.estado,e.id_carrera AS carrera_estudiante
         FROM proyectos_grado p
         INNER JOIN estudiantes e ON e.id_estudiante=p.id_estudiante
         WHERE p.id_proyecto=:id'
    );
    $stmt->execute([':id' => $idProyecto]);
    $proyecto = $stmt->fetch();
    if (!$proyecto) $errores[] = 'El proyecto de grado seleccionado no existe.';
    else {
        if ((int)$proyecto['id_estudiante'] !== $idEstudiante) $errores[] = 'El proyecto seleccionado no pertenece al estudiante autenticado.';
        if ((int)$proyecto['id_carrera'] !== (int)$estudiante['id_carrera']) $errores[] = 'El proyecto no corresponde a la carrera del estudiante.';
        if (in_array((string)$proyecto['estado'], ['cancelado','finalizado'], true)) $errores[] = 'El proyecto seleccionado no está disponible para tutoría.';
    }

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM tutores t
         INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
         WHERE t.id_tutor=:id AND u.estado='activo'"
    );
    $stmt->execute([':id' => $idTutor]);
    if ((int)$stmt->fetchColumn() === 0) $errores[] = 'El tutor seleccionado no existe o está inactivo.';

    $fecha = (string)($datos['fecha'] ?? '');
    $hi = (string)($datos['hora_inicio'] ?? '');
    $hf = (string)($datos['hora_fin'] ?? '');
    if ($fecha !== '' && $fecha < date('Y-m-d')) $errores[] = 'No se puede agendar una tutoría personal en una fecha pasada.';

    if ($hi >= $hf) $errores[] = 'La hora de inicio debe ser menor que la hora de fin.';

    // Las tutorías personales tienen duración fija de una hora.
    if ($hi !== '' && $hf !== '') {
        try {
            $inicio = new DateTimeImmutable($fecha . ' ' . $hi);
            $fin = new DateTimeImmutable($fecha . ' ' . $hf);
            if (($fin->getTimestamp() - $inicio->getTimestamp()) !== 3600) {
                $errores[] = 'Las tutorías personales tienen una duración fija de 1 hora.';
            }
        } catch (Throwable $e) {
            $errores[] = 'El horario seleccionado no es válido.';
        }
    }
    if ($hi !== '' && $hf !== '') {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM disponibilidad_tutor
             WHERE id_tutor=:t AND dia_semana=:d AND hora_inicio<=:hi AND hora_fin>=:hf'
        );
        $stmt->execute([
            ':t' => $idTutor,
            ':d' => diaSemanaEspanol($fecha) ?? '',
            ':hi' => $hi,
            ':hf' => $hf,
        ]);
        if ((int)$stmt->fetchColumn() === 0) $errores[] = 'El horario no está dentro de la disponibilidad registrada por el tutor.';
    }

    $sql = "SELECT COUNT(*) FROM tutorias
            WHERE id_estudiante=:e AND fecha=:f
              AND estado IN ('pendiente','programada','en_proceso','realizada')
              AND hora_inicio<:hf AND hora_fin>:hi";
    $params = [':e'=>$idEstudiante, ':f'=>$fecha, ':hi'=>$hi, ':hf'=>$hf];
    if ($excepto !== null) { $sql .= ' AND id_tutoria<>:x'; $params[':x'] = $excepto; }
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    if ((int)$stmt->fetchColumn() > 0) $errores[] = 'El estudiante ya tiene otra tutoría en ese horario.';

    $sql = "SELECT COUNT(*) FROM tutorias
            WHERE id_tutor=:t AND fecha=:f
              AND estado IN ('pendiente','programada','en_proceso','realizada')
              AND hora_inicio<:hf AND hora_fin>:hi";
    $params = [':t'=>$idTutor, ':f'=>$fecha, ':hi'=>$hi, ':hf'=>$hf];
    if ($excepto !== null) { $sql .= ' AND id_tutoria<>:x'; $params[':x'] = $excepto; }
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    if ((int)$stmt->fetchColumn() > 0) $errores[] = 'El tutor ya tiene otra tutoría en ese horario.';

    return $errores;
}
