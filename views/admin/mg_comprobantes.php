<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Modalidad de Grado - Comprobantes - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Validación de Comprobantes - Modalidad de Grado';
$descripcion = 'Revisa y valida los comprobantes de pago para habilitar la Modalidad de Grado de los estudiantes.';
$icono = 'bi-file-earmark-check-fill';
$contador = $pendientes;
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-wrap gap-3 mb-4">
  <div class="card card-custom shadow-sm p-3 d-inline-flex align-items-center gap-2">
    <i class="bi bi-hourglass-split text-warning fs-4"></i>
    <div><div class="fw-bold fs-5"><?= (int) $pendientes ?></div><small class="text-muted">Pendientes</small></div>
  </div>
</div>

<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3 border-0">
    <h5 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-check me-1 text-primary"></i>Comprobantes recibidos</h5>
    <?php
    // SPRINT 6: búsqueda por teclado + estado dinámico + por_página.
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar por estudiante o username...';
    $filtroSelectores = [[
        'nombre' => 'estado',
        'etiqueta' => 'Estado',
        'opciones' => array_merge(
            [['valor' => '', 'texto' => 'Todos los estados']],
            array_map(fn($e) => ['valor' => $e, 'texto' => ucfirst($e)], $estadosMG)
        ),
        'seleccionado' => $estadoMG,
        'minWidth' => 140,
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'mg_comprobantes_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Estudiante</th>
          <th>Monto</th>
          <th>Fecha pago</th>
          <th>Comprobante</th>
          <th>Fecha registro</th>
          <th>Estado</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($comprobantes as $c): ?>
          <tr data-comprobante-fila="<?= (int) $c['id_comprobante'] ?>">
            <td class="ps-4">
              <div class="d-flex align-items-center gap-2">
                <?= avatar($c['nombre'], $c['apellido'], 'estudiante') ?>
                <div>
                  <div class="fw-bold text-dark"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($c['usuario']) ?></small>
                </div>
              </div>
            </td>
            <td class="fw-semibold">Bs. <?= number_format((float) $c['monto'], 2) ?></td>
            <td><?= date('d/m/Y', strtotime($c['fecha_pago'])) ?></td>
            <td>
              <?php if (archivo_disponible($c['ruta_archivo'], 'uploads/comprobantes')): ?>
                <a href="/controllers/mg_comprobantes_descargar.php?id=<?= (int) $c['id_comprobante'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-2"><i class="bi bi-paperclip me-1"></i>Ver</a>
              <?php else: ?>
                <span class="btn btn-sm btn-outline-secondary rounded-2 disabled" role="button" tabindex="-1" aria-disabled="true" title="El comprobante no tiene un archivo disponible."><i class="bi bi-paperclip me-1"></i>Ver</span>
              <?php endif; ?>
            </td>
            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($c['fecha_registro'])) ?></td>
            <td data-comprobante-estado>
              <?= estado_badge($c['estado']) ?>
              <small class="d-block text-danger mt-1<?= ($c['estado'] ?? '') === 'rechazado' && !empty($c['motivo_rechazo']) ? '' : ' d-none' ?>" data-comprobante-motivo><?= htmlspecialchars($c['motivo_rechazo'] ?? '') ?></small>
              <small class="d-block text-muted mt-1<?= ($c['estado'] ?? '') === 'aprobado' && !empty($c['validador_nombre']) ? '' : ' d-none' ?>" data-comprobante-validador>Validó: <?= htmlspecialchars(trim(($c['validador_nombre'] ?? '') . ' ' . ($c['validador_apellido'] ?? ''))) ?></small>
            </td>
            <td class="text-end pe-4" data-comprobante-acciones>
              <?php if ($c['estado'] === 'pendiente' && !empty($esAdministrador)): ?>
                <button type="button" class="btn btn-success btn-sm rounded-2 me-1 js-validar-comprobante" data-id="<?= (int) $c['id_comprobante'] ?>" data-estado="aprobado" data-bs-toggle="modal" data-bs-target="#aprobarModal<?= $c['id_comprobante'] ?>" title="Aprobar comprobante" aria-label="Aprobar comprobante"><i class="bi bi-check-lg"></i></button>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-2 js-validar-comprobante" data-id="<?= (int) $c['id_comprobante'] ?>" data-estado="rechazado" data-bs-toggle="modal" data-bs-target="#rechazarModal<?= $c['id_comprobante'] ?>" title="Rechazar comprobante" aria-label="Rechazar comprobante"><i class="bi bi-x-lg"></i></button>
              <?php elseif ($c['estado'] === 'pendiente'): ?>
                <span class="text-muted small" title="Solo el administrador puede validar comprobantes."><i class="bi bi-lock"></i></span>
              <?php else: ?>
                <span class="text-muted small"><i class="bi bi-dot"></i></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($comprobantes)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
              No hay comprobantes registrados todavía.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php
  $mostrarSelector = false;
  include __DIR__ . '/../partials/paginacion.php';
  ?>
</div>

<?php foreach ($comprobantes as $c): ?>
  <?php if ($c['estado'] === 'pendiente' && !empty($esAdministrador)): ?>
    <div class="modal fade comprobante-modal" id="aprobarModal<?= (int) $c['id_comprobante'] ?>" tabindex="-1" aria-labelledby="aprobarTitulo<?= (int) $c['id_comprobante'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content js-form-comprobante" data-id="<?= (int) $c['id_comprobante'] ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="accion" value="validar">
          <input type="hidden" name="id_comprobante" value="<?= (int) $c['id_comprobante'] ?>">
          <input type="hidden" name="estado" value="aprobado">
          <div class="modal-header">
            <h5 class="modal-title" id="aprobarTitulo<?= (int) $c['id_comprobante'] ?>">
              <i class="bi bi-check-circle me-2 text-success"></i>Aprobar comprobante
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">¿Confirmas que el comprobante de pago de <strong><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></strong> por <strong>Bs. <?= number_format((float) $c['monto'], 2) ?></strong> es válido?</p>
            <p class="text-muted small mb-0">Al aprobar se habilitará el acceso del estudiante a la Modalidad de Grado.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success js-enviar-comprobante">
              <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Aprobar y habilitar
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade comprobante-modal" id="rechazarModal<?= (int) $c['id_comprobante'] ?>" tabindex="-1" aria-labelledby="rechazarTitulo<?= (int) $c['id_comprobante'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content js-form-comprobante" data-id="<?= (int) $c['id_comprobante'] ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="accion" value="validar">
          <input type="hidden" name="id_comprobante" value="<?= (int) $c['id_comprobante'] ?>">
          <input type="hidden" name="estado" value="rechazado">
          <div class="modal-header">
            <h5 class="modal-title" id="rechazarTitulo<?= (int) $c['id_comprobante'] ?>">
              <i class="bi bi-x-circle me-2 text-danger"></i>Rechazar comprobante
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p class="mb-3">¿Confirmas que el comprobante de pago de <strong><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></strong> por <strong>Bs. <?= number_format((float) $c['monto'], 2) ?></strong> debe ser rechazado?</p>
            <label class="form-label fw-semibold" for="motivo<?= (int) $c['id_comprobante'] ?>">Motivo del rechazo <span class="text-danger">*</span></label>
            <textarea name="motivo" class="form-control" id="motivo<?= (int) $c['id_comprobante'] ?>" rows="3" required maxlength="500" placeholder="Ej: el depósito no coincide con el monto solicitado."></textarea>
            <div class="form-text">El motivo se envía al estudiante y queda registrado en la auditoría.</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger js-enviar-comprobante">
              <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Rechazar comprobante
            </button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
<?php endforeach; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
  (function () {
    const forms = document.querySelectorAll('.js-form-comprobante');
    if (forms.length === 0) return;

    const contadorPendientes = document.querySelector('.card .fw-bold.fs-5');

    function actualizarFila(id, datos) {
      const fila = document.querySelector('[data-comprobante-fila="' + id + '"]');
      if (!fila) return false;

      const celdaEstado = fila.querySelector('[data-comprobante-estado]');
      if (celdaEstado && datos.badge) {
        celdaEstado.innerHTML = datos.badge;
      }

      const celdaAcciones = fila.querySelector('[data-comprobante-acciones]');
      if (celdaAcciones) {
        celdaAcciones.innerHTML = '<span class="text-muted small"><i class="bi bi-dot"></i></span>';
      }

      if (contadorPendientes && typeof datos.pendientes === 'number') {
        contadorPendientes.textContent = datos.pendientes;
      }

      return true;
    }

    forms.forEach((form) => {
      form.addEventListener('submit', (evento) => {
        evento.preventDefault();

        const boton = form.querySelector('.js-enviar-comprobante');
        const id = form.dataset.id;
        const textoOriginal = boton ? boton.innerHTML : '';

        if (boton) {
          boton.disabled = true;
          boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Procesando';
        }

        fetch(window.location.href, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          credentials: 'same-origin'
        })
          .then((respuesta) => respuesta.json()
            .catch(() => { throw new Error('Respuesta no válida del servidor.'); })
            .then((datos) => ({ ok: respuesta.ok, datos })))
          .then(({ ok, datos }) => {
            const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
            if (modal) modal.hide();

            if (ok && datos.success) {
              const actualizada = actualizarFila(id, datos.comprobante || {});
              mostrarToast(datos.message || 'Comprobante validado correctamente.', 'success');
              if (!actualizada) window.location.reload();
              return;
            }

            mostrarToast(datos.message || 'No se pudo validar el comprobante.', 'danger');
          })
          .catch((error) => {
            mostrarToast('Error de conexión al validar el comprobante. Intenta nuevamente.', 'danger');
            console.error('Fallo al validar el comprobante:', error);
          })
          .finally(() => {
            if (boton) {
              boton.disabled = false;
              boton.innerHTML = textoOriginal;
            }
          });
      });
    });
  })();
</script>