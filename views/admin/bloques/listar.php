<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Bloques Horarios - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
 <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-clock text-primary"></i>
      <span>Bloques Horarios</span>
    </h2>
    <p class="text-muted mb-0">Define los bloques (Morning / Noon / Afternoon / Night) que el estudiante puede seleccionar al solicitar una tutoría.</p>
 </div>
 <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalBloque" onclick="prepararCrear()">
    <i class="bi bi-plus-circle-fill"></i>
    <span class="fw-semibold">Nuevo Bloque</span>
 </button>
</div>

<?php if (!empty($errores)): ?>
 <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
    <ul class="mb-0 ps-3 small">
      <?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
 </div>
<?php endif; ?>

<div class="card card-custom shadow-sm overflow-hidden">
 <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Bloque</th>
          <th>Hora Inicio</th>
          <th>Hora Fin</th>
          <th>Descripción</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bloques as $b): ?>
          <tr>
            <td class="ps-4">
              <span class="badge badge-accent px-3 py-1 fw-semibold"><?= htmlspecialchars($b['nombre_bloque']) ?></span>
            </td>
            <td><i class="bi bi-sunrise text-success me-1"></i><?= substr($b['hora_inicio'], 0, 5) ?></td>
            <td><i class="bi bi-sunset text-danger me-1"></i><?= substr($b['hora_fin'], 0, 5) ?></td>
            <td class="text-muted small"><?= htmlspecialchars($b['descripcion'] ?? '—') ?></td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" title="Editar"
                        onclick='prepararEditar(<?= json_encode($b, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2" title="Eliminar"
                        onclick="confirmarEliminacionBloque(<?= $b['id_bloque'] ?>, '<?= htmlspecialchars($b['nombre_bloque'], ENT_QUOTES, 'UTF-8') ?>')">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($bloques)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-clock fs-1 d-block mb-2 text-secondary"></i>
              No hay bloques horarios registrados.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
 </div>
</div>

<form method="POST" id="formEliminarBloque" class="d-none">
  <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
 <input type="hidden" name="accion" value="eliminar">
 <input type="hidden" name="id_bloque" id="bloqueEliminar">
</form>

<!-- Modal de creación/edición -->
<div class="modal fade" id="modalBloque" tabindex="-1">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
        <input type="hidden" name="accion" id="accionBloque" value="crear">
        <input type="hidden" name="id_bloque" id="idBloque" value="">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" id="tituloModalBloque">Nuevo Bloque Horario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-secondary">Nombre del Bloque *</label>
            <input type="text" name="nombre_bloque" id="nombreBloque" class="form-control" maxlength="30" placeholder="Ej: Morning" required>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-uppercase text-secondary">Hora Inicio *</label>
              <input type="time" name="hora_inicio" id="horaInicioBloque" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-uppercase text-secondary">Hora Fin *</label>
              <input type="time" name="hora_fin" id="horaFinBloque" class="form-control" required>
            </div>
          </div>
          <div class="mb-0 mt-3">
            <label class="form-label fw-semibold small text-uppercase text-secondary">Descripción</label>
            <input type="text" name="descripcion" id="descripcionBloque" class="form-control" maxlength="200" placeholder="Ej: Mañana: 8:00 AM – 11:00 AM">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
 </div>
</div>

<script>
function prepararCrear() {
  document.getElementById('accionBloque').value = 'crear';
  document.getElementById('idBloque').value = '';
  document.getElementById('nombreBloque').value = '';
  document.getElementById('horaInicioBloque').value = '';
  document.getElementById('horaFinBloque').value = '';
  document.getElementById('descripcionBloque').value = '';
  document.getElementById('tituloModalBloque').textContent = 'Nuevo Bloque Horario';
}
function prepararEditar(b) {
  document.getElementById('accionBloque').value = 'editar';
  document.getElementById('idBloque').value = b.id_bloque;
  document.getElementById('nombreBloque').value = b.nombre_bloque;
  document.getElementById('horaInicioBloque').value = String(b.hora_inicio).slice(0, 5);
  document.getElementById('horaFinBloque').value = String(b.hora_fin).slice(0, 5);
  document.getElementById('descripcionBloque').value = b.descripcion || '';
  document.getElementById('tituloModalBloque').textContent = 'Editar Bloque Horario';
  new bootstrap.Modal(document.getElementById('modalBloque')).show();
}
function confirmarEliminacionBloque(id, nombre) {
  Swal.fire({
    title: '¿Eliminar bloque?',
    text: 'Se eliminará el bloque "' + nombre + '".',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#b42318',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    customClass: { popup: 'swal-upds-popup' }
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('bloqueEliminar').value = id;
      document.getElementById('formEliminarBloque').submit();
    }
  });
}
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
