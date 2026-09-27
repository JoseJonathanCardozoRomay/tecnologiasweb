<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Importar Padrón - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Importar Padrón de Estudiantes';
$descripcion = 'Carga masiva de estudiantes a la cohorte desde archivo CSV (HU-023).';
$icono = 'bi-upload';
$contador = '';
$accion = '';
include __DIR__ . '/../../partials/page_header.php';
?>

<?php if ($resultado): ?>
  <div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
      <div class="card card-custom shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small text-uppercase">Leídas</div>
          <div class="fs-3 fw-bold"><?= (int) $resultado['total_filas'] ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card card-custom shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small text-uppercase">Creados</div>
          <div class="fs-3 fw-bold text-success"><?= (int) $resultado['creados'] ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card card-custom shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small text-uppercase">Actualizados</div>
          <div class="fs-3 fw-bold text-primary"><?= (int) $resultado['actualizados'] ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card card-custom shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small text-uppercase">Expedientes</div>
          <div class="fs-3 fw-bold text-info"><?= (int) $resultado['expedientes'] ?></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white border-0 py-3">
        <h2 class="h6 mb-0 fw-bold">Subir archivo CSV</h2>
      </div>
      <div class="card-body pt-0">
        <form method="POST" action="/controllers/mg_padron_importar.php" enctype="multipart/form-data">
          <?= csrf_campo(); ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-cohorte-padron">Cohorte destino</label>
              <select id="sel-cohorte-padron" name="id_cohorte_mg" class="form-select rounded-3" required>
                <option value="">Seleccionar cohorte...</option>
                <?php foreach ($cohortes as $c): ?>
                  <option value="<?= (int) $c['id_cohorte_mg'] ?>"
                    <?= (int) $c['id_cohorte_mg'] === $cohorteSeleccionada ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre_periodo']) ?> (<?= htmlspecialchars($c['estado']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="input-csv">Archivo CSV</label>
              <input type="file" class="form-control rounded-3" id="input-csv" name="archivo_csv"
                     accept=".csv,.txt" required>
            </div>
            <div class="col-12">
              <div class="alert alert-info small mb-0 rounded-3">
                <i class="bi bi-info-circle me-1"></i>
                La importación crea o actualiza estudiantes (contraseña inicial
                <code>Control123+</code> para cuentas nuevas) y genera una solicitud
                de Modalidad de Grado para cada uno en la cohorte seleccionada.
                Los duplicados por <strong>registro universitario</strong> se actualizan.
              </div>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary px-4 rounded-3">
                <i class="bi bi-upload me-1"></i> Importar padrón
              </button>
            </div>
          </div>
        </form>

        <?php if (!empty($errores)): ?>
          <div class="alert alert-danger mt-3 rounded-3 mb-0">
            <strong>No se pudo importar:</strong>
            <ul class="mb-0 mt-1 ps-3">
              <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card card-custom shadow-sm h-100">
      <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
        <h2 class="h6 mb-0 fw-bold">Formato esperado</h2>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copiarPlantilla()">
          <i class="bi bi-clipboard me-1"></i> Copiar plantilla
        </button>
      </div>
      <div class="card-body pt-0">
        <p class="small text-muted mb-2">Primera fila con encabezados (separador <code>;</code> o <code>,</code>). Columnas requeridas: <code>registro_universitario</code>, <code>paterno</code>, <code>materno</code>, <code>nombre</code>, <code>correo</code>, <code>carrera</code>. Opcionales: <code>semestre</code>, <code>materias</code>.</p>
        <pre id="plantilla-padron" class="bg-light border rounded-3 p-3 small text-secondary" style="white-space: pre-wrap;">registro_universitario;paterno;materno;nombre;correo;carrera;semestre;materias
RU-2026-100;Miranda;Lopez;Ana Maria;ana.miranda@upds.edu.bo;Ingenieria de Sistemas;9;54
RU-2026-101;Quispe;Cruz;Carlos Jose;carlos.quispe@upds.edu.bo;Contaduria Publica;10;56</pre>
        <p class="small text-muted mt-2 mb-0">
          Si el <code>registro_universitario</code> ya existe, los datos del estudiante se
          actualizan y no se abre una nueva solicitud si ya tiene una en la cohorte.
        </p>
      </div>
    </div>
  </div>
</div>

<?php if ($resultado && !empty($resultado['errores'])): ?>
  <div class="card card-custom shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3">
      <h2 class="h6 mb-0 fw-bold text-danger">Filas rechazadas</h2>
    </div>
    <div class="card-body pt-0">
      <ul class="mb-0 ps-3 small text-muted" style="max-height: 220px; overflow-y: auto;">
        <?php foreach ($resultado['errores'] as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>

<script>
function copiarPlantilla() {
  var t = document.getElementById('plantilla-padron');
  if (navigator.clipboard) {
    navigator.clipboard.writeText(t.innerText);
  } else {
    var r = document.createRange(); r.selectNode(t);
    window.getSelection().removeAllRanges(); window.getSelection().addRange(r);
    document.execCommand('copy'); window.getSelection().removeAllRanges();
  }
}
</script>