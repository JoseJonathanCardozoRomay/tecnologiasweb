<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Parámetros de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Parámetros de Modalidad de Grado';
$descripcion = 'Configuración de negocio del módulo de Modalidades de Grado (HU-020).';
$icono = 'bi-sliders';
$contador = count($parametros);
$accion = '<a href="/controllers/mg_expedientes_listar.php" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3"><i class="bi bi-arrow-left"></i><span>Volver</span></a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row justify-content-center">
  <div class="col-12">
    <form method="POST" action="mg_parametros_guardar.php" autocomplete="off" class="card card-custom shadow-sm overflow-hidden">
      <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>

      <div class="card-body p-4">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
          <?php foreach ($parametros as $parametro): ?>
            <?php
            $clave = htmlspecialchars($parametro['clave'], ENT_QUOTES, 'UTF-8');
            $etiqueta = str_replace('_', ' ', $parametro['clave']);
            $ayuda = htmlspecialchars($parametro['descripcion'], ENT_QUOTES, 'UTF-8');
            $tipo = $parametro['tipo'];
            ?>
            <div class="col">
              <div class="border rounded-3 p-3 bg-white h-100 d-flex flex-column">
                <label for="par-<?= $clave ?>" class="form-label fw-semibold text-secondary small text-uppercase mb-1"><?= htmlspecialchars($etiqueta) ?></label>
                <p class="small text-muted mb-3"><?= $ayuda ?></p>

                <?php if ($tipo === 'booleano'): ?>
                  <select class="form-select rounded-3" id="par-<?= $clave ?>" name="valor[<?= $clave ?>]">
                    <option value="1" <?= $parametro['valor'] === '1' ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= $parametro['valor'] === '0' ? 'selected' : '' ?>>No</option>
                  </select>
                <?php elseif ($tipo === 'entero'): ?>
                  <input type="number" step="1" min="0" class="form-control rounded-3" id="par-<?= $clave ?>"
                         name="valor[<?= $clave ?>]" value="<?= htmlspecialchars($parametro['valor'], ENT_QUOTES, 'UTF-8') ?>" required>
                <?php elseif ($tipo === 'decimal'): ?>
                  <input type="number" step="0.01" min="0" class="form-control rounded-3" id="par-<?= $clave ?>"
                         name="valor[<?= $clave ?>]" value="<?= htmlspecialchars($parametro['valor'], ENT_QUOTES, 'UTF-8') ?>" required>
                <?php else: ?>
                  <input type="text" class="form-control rounded-3" id="par-<?= $clave ?>"
                         name="valor[<?= $clave ?>]" value="<?= htmlspecialchars($parametro['valor'], ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card-footer bg-white border-top d-flex justify-content-end gap-2 p-3">
        <a href="/controllers/mg_expedientes_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
          <i class="bi bi-save"></i>
          <span>Guardar Parámetros</span>
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>