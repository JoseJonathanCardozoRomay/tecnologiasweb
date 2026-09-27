<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Nuevo Hito - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <?php
    $titulo = 'Nuevo Hito';
    $descripcion = 'Registra un hito en el calendario de la cohorte.';
    $icono = 'bi-calendar-plus';
    $volverCohorte = $cohorteSeleccionada > 0 ? '?cohorte=' . $cohorteSeleccionada : '';
    $accion = '<a href="/controllers/mg_calendario_listar.php' . $volverCohorte . '" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
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
          <label class="form-label fw-semibold text-secondary small text-uppercase">Cohorte *</label>
          <select name="id_cohorte_mg" class="form-select rounded-3 py-2" required autofocus>
            <?php foreach ($cohortes as $c): ?>
              <option value="<?= (int) $c['id_cohorte_mg'] ?>" <?= (int) $c['id_cohorte_mg'] === $cohorteSeleccionada ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre_periodo']) ?> (<?= htmlspecialchars(ucfirst($c['estado'])) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Título *</label>
          <input type="text" name="titulo" class="form-control rounded-3 py-2" maxlength="120"
                 value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                 placeholder="Ej: Defensa final ante tribunal" required>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha *</label>
            <input type="date" name="fecha_hito" class="form-control rounded-3 py-2"
                   value="<?= htmlspecialchars($_POST['fecha_hito'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Orden</label>
            <input type="number" name="orden" class="form-control rounded-3 py-2" min="0" max="999"
                   value="<?= htmlspecialchars($_POST['orden'] ?? '0') ?>">
            <div class="form-text">Posición en el calendario (0-999).</div>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Tipo de hito *</label>
          <select name="tipo_hito" class="form-select rounded-3 py-2" required>
            <?php foreach ($tiposHito as $t): ?>
              <option value="<?= htmlspecialchars($t['valor']) ?>" <?= (($_POST['tipo_hito'] ?? '') === $t['valor']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Descripción</label>
          <textarea name="descripcion" class="form-control rounded-3 py-2" rows="3" maxlength="255"
                    placeholder="Detalle opcional del hito."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="/controllers/mg_calendario_listar.php<?= $volverCohorte ?>" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-save"></i>
            <span>Guardar Hito</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>