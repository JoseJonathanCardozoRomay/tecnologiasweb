<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Nuevo Expediente de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <?php
    $titulo = 'Nuevo Expediente de Modalidad de Grado';
    $descripcion = 'Crea el expediente MG de un estudiante habilitado en la cohorte vigente.';
    $icono = 'bi-plus-circle-fill';
    $accion = '<a href="/controllers/mg_expedientes_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
    include __DIR__ . '/../../partials/page_header.php';
    ?>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
        <ul class="mb-0 ps-3 small">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Estudiante habilitado *</label>
          <?php if (empty($estudiantes)): ?>
            <div class="alert alert-warning py-2 px-3 rounded-3 small mb-3">
              No hay estudiantes habilitados disponibles. Deben tener el comprobante de pago aprobado
              (<code>acceso_mg_desbloqueado = 1</code>) y no contar con un expediente activo.
            </div>
          <?php else: ?>
            <select name="id_estudiante" class="form-select rounded-3 py-2" required <?= empty($estudiantes) ? 'disabled' : '' ?>>
              <option value="">Seleccione un estudiante...</option>
              <?php foreach ($estudiantes as $est): ?>
                <option value="<?= (int) $est['id_estudiante'] ?>"
                  <?= (($_POST['id_estudiante'] ?? '') == $est['id_estudiante']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($est['nombre_completo']) ?> — <?= htmlspecialchars($est['nombre_carrera']) ?>
                  (<?= htmlspecialchars($est['registro_universitario']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Modalidad de Grado *</label>
            <select name="id_modalidad_grado" class="form-select rounded-3 py-2" required>
              <option value="">Seleccione una modalidad...</option>
              <?php foreach ($modalidades as $mod): ?>
                <option value="<?= (int) $mod['id_modalidad_grado'] ?>"
                  <?= (($_POST['id_modalidad_grado'] ?? '') == $mod['id_modalidad_grado']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($mod['nombre']) ?>
                  <?= (int) $mod['requiere_tutor'] ? '(con tutor)' : '(sin tutor)' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Cohorte vigente *</label>
            <select name="id_cohorte_mg" class="form-select rounded-3 py-2" required>
              <option value="">Seleccione una cohorte...</option>
              <?php foreach ($cohortes as $co): ?>
                <option value="<?= (int) $co['id_cohorte_mg'] ?>"
                  <?= (($_POST['id_cohorte_mg'] ?? '') == $co['id_cohorte_mg']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($co['nombre_periodo']) ?>
                  (<?= htmlspecialchars(date('d/m/Y', strtotime($co['fecha_inicio']))) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Detalle de la solicitud</label>
          <textarea name="solicitud_detalle" class="form-control rounded-3 py-2" rows="3" maxlength="255"
                    placeholder="Motivos o antecedentes de la solicitud (opcional)."><?= htmlspecialchars($_POST['solicitud_detalle'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="/controllers/mg_expedientes_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" <?= empty($estudiantes) || count($modalidades) === 0 || count($cohortes) === 0 ? 'disabled' : '' ?>>
            <i class="bi bi-save"></i>
            <span>Registrar Expediente</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>