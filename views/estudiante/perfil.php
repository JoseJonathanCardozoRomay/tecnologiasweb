<?php
/**
 * Perfil del estudiante — SOLO LECTURA.
 * Muestra cómo fue registrado el estudiante (datos personales y académicos).
 * Por el momento NO permite modificar la información: la edición la gestiona
 * el administrador. Si a futuro se habilita la edición, se añadirá aquí un
 * formulario POST con validación y CSRF.
 */
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['estudiante']);
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/EstudianteModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';

$estudianteModel = new EstudianteModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);

$tituloPagina = 'Mi Perfil - Tutorías UPDS';
include __DIR__ . '/../layouts/header.php';

if (!$estudiante) {
    $emptyIcono = 'bi-person-vcard';
    $emptyTitulo = 'Perfil académico incompleto';
    $emptyTexto = 'Tu perfil académico aún no está completo. Contacta al administrador.';
    include __DIR__ . '/../partials/empty_state.php';
    include __DIR__ . '/../layouts/footer.php';
    exit;
}

$idEstudiante = $estudiante['id_estudiante'];
$totalTutorias = $tutoriaModel->contarPorEstudiante($idEstudiante);
$metricas = $tutoriaModel->obtenerMetricasPorEstudiante($idEstudiante);
$realizadas = (int) ($metricas['realizadas'] ?? 0);

$iniciales = strtoupper(substr($estudiante['nombre'] ?? 'E', 0, 1) . substr($estudiante['apellido'] ?? '', 0, 1));

$titulo = 'Mi Perfil';
$descripcion = 'Así fuiste registrado en el sistema de tutorías. Esta información es de solo lectura.';
$icono = 'bi-person-badge';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row g-4">
  <!-- Tarjeta de identidad -->
 <div class="col-lg-4">
    <div class="card card-custom p-4 text-center h-100">
      <div class="d-flex justify-content-center mb-3">
        <span class="perfil-avatar"><?= htmlspecialchars($iniciales) ?></span>
      </div>
      <h4 class="fw-bold mb-1"><?= htmlspecialchars("{$estudiante['nombre']} {$estudiante['apellido']}") ?></h4>
      <p class="text-muted mb-3"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($estudiante['usuario'] ?? '') ?></p>
      <span class="badge bg-success bg-opacity-10 text-success align-self-center px-3 py-2 rounded-pill">
        <i class="bi bi-patch-check-fill me-1"></i><?= htmlspecialchars(ucfirst($estudiante['estado'] ?? 'activo')) ?>
      </span>

      <hr class="my-4">

      <div class="d-flex justify-content-around text-center">
        <div>
          <div class="fs-4 fw-bold text-primary"><?= $totalTutorias ?></div>
          <small class="text-muted text-uppercase" style="font-size:.7rem; letter-spacing:.06em;">Tutorías</small>
        </div>
        <div>
          <div class="fs-4 fw-bold text-success"><?= $realizadas ?></div>
          <small class="text-muted text-uppercase" style="font-size:.7rem; letter-spacing:.06em;">Completadas</small>
        </div>
      </div>
    </div>
 </div>

  <!-- Datos detallados -->
 <div class="col-lg-8">
    <div class="card card-custom h-100">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1">Información registrada</h5>
        <p class="text-muted small mb-0">Datos personales y académicos asociados a tu cuenta.</p>
      </div>
      <div class="card-body px-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Nombres</label>
            <div class="perfil-dato"><?= htmlspecialchars($estudiante['nombre']) ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Apellidos</label>
            <div class="perfil-dato"><?= htmlspecialchars($estudiante['apellido']) ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Correo electrónico</label>
            <div class="perfil-dato"><i class="bi bi-envelope me-2 text-muted"></i><?= htmlspecialchars($estudiante['correo'] ?? '—') ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Teléfono</label>
            <div class="perfil-dato"><i class="bi bi-telephone me-2 text-muted"></i><?= htmlspecialchars($estudiante['telefono'] ?? '—') ?: '—' ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Carrera</label>
            <div class="perfil-dato"><i class="bi bi-mortarboard me-2 text-muted"></i><?= htmlspecialchars($estudiante['nombre_carrera'] ?? 'Sin carrera asignada') ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Semestre</label>
            <div class="perfil-dato"><i class="bi bi-calendar3 me-2 text-muted"></i><?= htmlspecialchars((string) ($estudiante['semestre'] ?? '—')) ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Registro universitario (R.U.)</label>
            <div class="perfil-dato"><i class="bi bi-hash me-2 text-muted"></i><?= htmlspecialchars($estudiante['registro_universitario'] ?? '—') ?: '—' ?></div>
          </div>
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2 mt-4 mb-0 rounded-3">
          <i class="bi bi-lock-fill mt-1"></i>
          <div class="small">
            <strong>Información de solo lectura.</strong>
            Por el momento no puedes modificar estos datos desde aquí. Si necesitas corregir algún dato,
            contacta con coordinación académica.
          </div>
        </div>
      </div>
    </div>
 </div>
</div>

<style>
  .perfil-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 96px;
    height: 96px;
    border-radius: 50%;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, var(--upds-navy) 0%, var(--upds-accent) 100%);
    box-shadow: 0 6px 16px rgba(0, 43, 73, .18);
  }
  .perfil-dato {
    background: var(--upds-navy-soft);
    border: 1px solid var(--upds-border);
    border-radius: 8px;
    padding: .6rem .85rem;
    font-size: .9rem;
    color: var(--upds-text);
    min-height: 42px;
    display: flex;
    align-items: center;
  }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
