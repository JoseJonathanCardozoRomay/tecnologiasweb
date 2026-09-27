<?php
if (!isset($reporteElegibilidad)) {
    header('Location: /controllers/mg_comprobantes_registrar.php');
    exit;
}
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Modalidad de Grado - Estudiante - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Modalidad de Grado';
$descripcion = $mgCompletada
    ? 'Tu proceso de grado finalizó. Consultá tu historial académico completo.'
    : 'Cumple los requisitos, registra tu comprobante de pago y solicita tu tutoría de grado.';
$icono = 'bi-mortarboard-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($mgCompletada)): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-3 border-success-subtle" role="alert">
    <div class="d-flex align-items-start gap-3">
      <i class="bi bi-patch-check-fill text-success fs-3" aria-hidden="true"></i>
      <div>
        <h5 class="alert-heading fw-bold mb-1">Modalidad de Grado Completada</h5>
        <p class="mb-0">Tu tutoría de grado finalizó y tu expediente quedó en estado <strong>Finalizada<?= !empty($mgConcluida['estado_conclusion_nombre']) ? ' — ' . htmlspecialchars($mgConcluida['estado_conclusion_nombre']) : '' ?></strong>. Ya no podés solicitar tutorías ni registrar nuevos comprobantes.</p>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-7">
    <div class="card card-custom shadow-sm h-100">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clipboard-check me-1 text-primary"></i>Requisitos para la Modalidad de Grado</h5>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex align-items-center gap-3 px-0">
            <i class="bi <?= $reporteElegibilidad['materias']['ok'] ? 'bi-check-circle-fill text-success fs-4' : 'bi-circle text-muted fs-4' ?>"></i>
            <div>
              <div class="fw-semibold">Materias completadas</div>
              <small class="text-muted"><?= $reporteElegibilidad['materias']['actual'] ?> / <?= $reporteElegibilidad['materias']['requerido'] ?> materias</small>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-center gap-3 px-0">
            <i class="bi <?= $reporteElegibilidad['semestres']['ok'] ? 'bi-check-circle-fill text-success fs-4' : 'bi-circle text-muted fs-4' ?>"></i>
            <div>
              <div class="fw-semibold">Semestres cursados</div>
              <small class="text-muted"><?= $reporteElegibilidad['semestres']['actual'] ?> / <?= $reporteElegibilidad['semestres']['requerido'] ?> semestres</small>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-center gap-3 px-0">
            <i class="bi <?= $reporteElegibilidad['comprobante']['ok'] ? 'bi-check-circle-fill text-success fs-4' : 'bi-circle text-muted fs-4' ?>"></i>
            <div>
              <div class="fw-semibold">Comprobante de pago aprobado</div>
              <small class="text-muted"><?= $reporteElegibilidad['comprobante']['ok'] ? 'Validado por administración' : 'Pendiente de validación' ?></small>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-center gap-3 px-0">
            <i class="bi <?= $reporteElegibilidad['desbloqueo']['ok'] ? 'bi-check-circle-fill text-success fs-4' : 'bi-circle text-muted fs-4' ?>"></i>
            <div>
              <div class="fw-semibold">Acceso habilitado por administración</div>
              <small class="text-muted"><?= $reporteElegibilidad['desbloqueo']['ok'] ? 'Puedes solicitar tu tutoría de grado' : 'Se habilitará tras validar tu comprobante' ?></small>
            </div>
          </li>
        </ul>

        <div class="mt-4">
          <?php if (!empty($mgCompletada)): ?>
            <a href="/views/estudiante/historial.php" class="btn btn-outline-primary btn-lg w-100 rounded-3">
              <i class="bi bi-clock-history me-1"></i>Consultar historial académico
            </a>
          <?php elseif ($accesoMG): ?>
            <a href="/controllers/tutorias_solicitar.php?tipo=grado" class="btn btn-primary btn-lg w-100 rounded-3">
              <i class="bi bi-mortarboard me-1"></i>Solicitar tutoría de Modalidad de Grado
            </a>
          <?php else: ?>
            <button class="btn btn-secondary btn-lg w-100 rounded-3" disabled title="Aún no cumples todos los requisitos.">
              <i class="bi bi-lock me-1"></i>Solicitud bloqueada
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card card-custom shadow-sm mb-3">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cash-stack me-1 text-primary"></i>Registrar comprobante de pago</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($mgCompletada)): ?>
          <div class="alert alert-secondary rounded-3 mb-0">
            <i class="bi bi-lock me-1"></i>
            Tu Modalidad de Grado ya fue completada; no se aceptan nuevos comprobantes.
          </div>
        <?php elseif ($puedeSubirComprobante): ?>
          <form method="POST" enctype="multipart/form-data" action="/controllers/mg_comprobantes_registrar.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Monto (Bs) *</label>
              <input type="number" name="monto" step="0.01" min="0.01" class="form-control rounded-3 py-2" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha del pago *</label>
              <input type="date" name="fecha_pago" class="form-control rounded-3 py-2" max="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Comprobante (JPG, PNG o PDF) *</label>
              <input type="file" name="comprobante_archivo" accept=".jpg,.jpeg,.png,.pdf" class="form-control rounded-3 py-2" required>
            </div>
            <button type="submit" class="btn btn-primary rounded-3 w-100"><i class="bi bi-upload me-1"></i>Subir comprobante</button>
          </form>
        <?php else: ?>
          <div class="alert alert-secondary rounded-3 mb-0">
            <i class="bi bi-lock me-1"></i>
            Debes completar primero las <strong>materias</strong> y <strong>semestres</strong> requeridos para poder registrar el comprobante.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-ul me-1 text-primary"></i>Mis comprobantes</h5>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php foreach ($comprobantes as $c): ?>
            <?php
            $extComprobante = strtolower(pathinfo((string) $c['ruta_archivo'], PATHINFO_EXTENSION));
            $etiquetaExt = in_array($extComprobante, ['jpg', 'jpeg', 'png', 'pdf'], true) ? strtoupper($extComprobante) : 'ARCHIVO';
            $tieneArchivo = archivo_disponible($c['ruta_archivo'], 'uploads/comprobantes');
            ?>
            <li class="list-group-item d-flex align-items-center justify-content-between gap-2">
              <div class="min-w-0">
                <div class="fw-semibold">Bs. <?= number_format((float) $c['monto'], 2) ?></div>
                <small class="text-muted d-block">Pagado el <?= date('d/m/Y', strtotime($c['fecha_pago'])) ?> · registrado el <?= date('d/m/Y', strtotime($c['fecha_registro'])) ?></small>
                <?php if ($c['estado'] === 'rechazado' && !empty($c['motivo_rechazo'])): ?>
                  <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($c['motivo_rechazo']) ?></small>
                <?php endif; ?>
              </div>
              <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <?= estado_badge($c['estado']) ?>
                <?php if ($tieneArchivo): ?>
                  <a href="/controllers/mg_comprobantes_descargar.php?id=<?= (int) $c['id_comprobante'] ?>"
                     target="_blank" rel="noopener"
                     class="btn btn-sm btn-outline-primary rounded-2 d-inline-flex align-items-center gap-1"
                     title="Ver comprobante (<?= htmlspecialchars($etiquetaExt) ?>)"
                     aria-label="Ver comprobante">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i><span class="small fw-semibold">Ver</span>
                  </a>
                <?php else: ?>
                  <span class="btn btn-sm btn-outline-secondary rounded-2 disabled"
                        role="button" tabindex="-1" aria-disabled="true"
                        title="Este comprobante aún no tiene un archivo cargado."
                        aria-label="Archivo no disponible">
                    <i class="bi bi-paperclip" aria-hidden="true"></i><span class="small fw-semibold">Ver</span>
                  </span>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
          <?php if (empty($comprobantes)): ?>
            <li class="list-group-item text-center text-muted py-4">Aún no registraste comprobantes.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>