<?php
/**
 * Catálogos de etiquetas del Módulo Modalidades de Grado (SPRINT 7).
 * Centraliza los textos visibles para no duplicarlos entre vistas/controladores.
 */

function mgFlujos(): array
{
    return [
        ['valor' => 'perfil_mg', 'label' => 'Perfil de Proyecto de Grado'],
        ['valor' => 'excelencia', 'label' => 'Graduacion por Excelencia'],
        ['valor' => 'examen_areas', 'label' => 'Examen por Areas'],
    ];
}

function mgFlujoLabel(string $flujo): string
{
    foreach (mgFlujos() as $f) {
        if ($f['valor'] === $flujo) {
            return $f['label'];
        }
    }
    return $flujo;
}

function mgExpEstadoLabel(string $estado): string
{
    $mapa = [
        'solicitado' => 'Solicitado',
        'validado' => 'Validado',
        'tutor_asignado' => 'Tutor asignado',
        'en_curso' => 'En curso',
        'concluido' => 'Concluido',
        'cerrado' => 'Cerrado',
    ];
    return $mapa[$estado] ?? ucfirst($estado);
}

function mgTiposHito(): array
{
    return [
        ['valor' => 'inscripcion', 'label' => 'Inscripcion'],
        ['valor' => 'tutor', 'label' => 'Asignacion de tutor'],
        ['valor' => 'perfil', 'label' => 'Entrega de perfil'],
        ['valor' => 'defensa', 'label' => 'Defensa'],
        ['valor' => 'conclusion', 'label' => 'Conclusion'],
        ['valor' => 'gestion', 'label' => 'Gestion'],
    ];
}

function mgTipoHitoLabel(string $tipo): string
{
    foreach (mgTiposHito() as $t) {
        if ($t['valor'] === $tipo) {
            return $t['label'];
        }
    }
    return ucfirst($tipo);
}

function mgTipoHitoBadge(string $tipo): string
{
    $clases = [
        'inscripcion' => 'bg-light text-dark border',
        'tutor' => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
        'perfil' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
        'defensa' => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
        'conclusion' => 'bg-success bg-opacity-10 text-success border border-success-subtle',
        'gestion' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
    ];
    $clase = $clases[$tipo] ?? 'bg-light text-dark border';
    return '<span class="badge ' . $clase . ' px-2 py-1">' . htmlspecialchars(mgTipoHitoLabel($tipo), ENT_QUOTES, 'UTF-8') . '</span>';
}

function mgEstadoExpedienteBadge(string $estado): string
{
    $clases = [
        'solicitado' => 'status-badge status-pending',
        'validado' => 'status-badge status-process',
        'tutor_asignado' => 'status-badge status-process',
        'en_curso' => 'status-badge status-active',
        'concluido' => 'status-badge status-completed',
        'cerrado' => 'status-badge status-inactive',
    ];
    $clase = $clases[$estado] ?? 'status-badge status-neutral';
    return '<span class="' . $clase . '">' . htmlspecialchars(mgExpEstadoLabel($estado), ENT_QUOTES, 'UTF-8') . '</span>';
}

function mgDefensaEstadoLabel(string $estado): string
{
    $mapa = [
        'programada' => 'Programada',
        'evaluada' => 'Evaluada',
    ];
    return $mapa[$estado] ?? ucfirst($estado);
}

function mgDefensaEstadoBadge(string $estado): string
{
    $clase = $estado === 'evaluada' ? 'status-badge status-completed' : 'status-badge status-active';

    return '<span class="' . $clase . '">' . htmlspecialchars(mgDefensaEstadoLabel($estado), ENT_QUOTES, 'UTF-8') . '</span>';
}

function mgResultadoBadge(?string $resultado): string
{
    if (!$resultado) {
        return '<span class="status-badge status-neutral">Sin resultado</span>';
    }
    $clase = $resultado === 'aprobado' ? 'status-badge status-completed' : 'status-badge status-inactive';

    return '<span class="' . $clase . '">' . htmlspecialchars(ucfirst($resultado), ENT_QUOTES, 'UTF-8') . '</span>';
}

function mgAlertasNivelBadge(string $nivel): string
{
    $clases = [
        'danger' => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
        'warning' => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
        'info' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
    ];
    $clase = $clases[$nivel] ?? 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
    $etiqueta = $nivel === 'danger' ? 'Crítica' : ($nivel === 'warning' ? 'Precaución' : 'Informativa');

    return '<span class="badge ' . $clase . ' px-2 py-1">' . $etiqueta . '</span>';
}

/**
 * Genera el siguiente correlativo de un documento para una gestión.
 * Devuelve el número secuencial (para persistirlo) y el código formateado.
 */
function mgSiguienteCorrelativo($pdo, string $tipo, string $gestion): string
{
    $stmt = $pdo->prepare(
        "INSERT INTO correlativos_documentos (tipo_documento, gestion, ultimo_correlativo)
         VALUES (:tipo, :gestion, 1)
         ON DUPLICATE KEY UPDATE ultimo_correlativo = ultimo_correlativo + 1"
    );
    $stmt->execute([':tipo' => $tipo, ':gestion' => $gestion]);

    $stmt = $pdo->prepare(
        "SELECT ultimo_correlativo FROM correlativos_documentos
         WHERE tipo_documento = :tipo AND gestion = :gestion"
    );
    $stmt->execute([':tipo' => $tipo, ':gestion' => $gestion]);
    $numero = (int) $stmt->fetchColumn();

    $prefijo = $tipo === 'carta_tutor' ? 'MG-CTA-TUT' : 'MG-CIT';

    return $prefijo . '-' . $gestion . '-' . str_pad((string) $numero, 4, '0', STR_PAD_LEFT);
}

/** Formatea una fecha (mysql DATE/DATETIME) en español legible, ej: "25 de septiembre de 2026". */
function mgFechaEspanol($fecha): string
{
    if (!$fecha) {
        return '';
    }
    $ts = strtotime((string) $fecha);
    if ($ts === false) {
        return (string) $fecha;
    }
    $meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];

    return (int) date('j', $ts) . ' de ' . $meses[(int) date('n', $ts) - 1] . ' de ' . date('Y', $ts);
}

/**
 * Tipos de aviso interno que recibe el estudiante del módulo MG.
 */
function mgTiposAviso(): array
{
    return [
        'MG_EXPEDIENTE_ESTADO'    => ['label' => 'Estado de expediente', 'icono' => 'bi-folder2-open', 'clase' => 'bi-info-circle-fill text-info'],
        'MG_TUTOR_ASIGNADO'       => ['label' => 'Tutor asignado', 'icono' => 'bi-person-video3', 'clase' => 'bi-person-check-fill text-primary'],
        'MG_DEFENSA_PROGRAMADA'   => ['label' => 'Defensa programada', 'icono' => 'bi-calendar2-check', 'clase' => 'bi-calendar-event-fill text-warning'],
    ];
}

function mgAvisoLabel(string $tipo): string
{
    $tipos = mgTiposAviso();

    return $tipos[$tipo]['label'] ?? ucfirst(strtolower(str_replace('_', ' ', $tipo)));
}

function mgAvisoIcono(string $tipo): string
{
    $tipos = mgTiposAviso();

    return $tipos[$tipo]['icono'] ?? 'bi-bell-fill';
}

function mgAvisoClase(string $tipo): string
{
    $tipos = mgTiposAviso();

    return $tipos[$tipo]['clase'] ?? 'bi-bell-fill text-secondary';
}

/**
 * Envia un aviso interno al estudiante dueno de un expediente de MG.
 * Resuelve expedientes_mg -> estudiantes -> usuarios.id_usuario y delega la
 * escritura en NotificationModel (silencia errores de FK si el alta falla).
 */
function mgAvisarEstudiante($pdo, $idExpediente, string $tipo, string $mensaje, ?string $enlace = null, ?int $idOrigen = null): bool
{
    require_once __DIR__ . '/../models/NotificationModel.php';

    $stmt = $pdo->prepare(
        "SELECT es.id_usuario
           FROM expedientes_mg e
           JOIN estudiantes es ON es.id_estudiante = e.id_estudiante
          WHERE e.id_expediente_mg = :id
          LIMIT 1"
    );
    $stmt->execute([':id' => (int) $idExpediente]);
    $idUsuario = (int) ($stmt->fetchColumn() ?: 0);
    if ($idUsuario <= 0) {
        return false;
    }

    $notificaciones = new NotificationModel($pdo);

    return $notificaciones->crear($idUsuario, $tipo, $mensaje, $enlace, $idOrigen);
}

/** Datos institucionales para cartas y citaciones (HU-027/030). */
function mgDatosInstitucion($config): array
{
    return [
        'universidad' => (string) ($config->obtener('nombre_universidad') ?: 'Universidad Privada Domingo Savio'),
        'eslogan' => (string) ($config->obtener('eslogan') ?: ''),
        'telefono' => (string) ($config->obtener('telefono_contacto') ?: ''),
        'correo' => (string) ($config->obtener('correo_contacto') ?: ''),
        'plazo_carta' => (int) ($config->obtener('plazo_dias_carta') ?: 7),
        'gestion' => (string) ($config->obtener('gestion_academica') ?: date('Y')),
    ];
}