<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Cartas de Designación - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Mi panel', 'url' => '/views/tutor/panel.php'],
    ['texto' => 'Cartas de designación', 'actual' => true],
];
$titulo = 'Cartas de Designación';
$descripcion = 'Revisa las cartas de designación de tutorías. Acepta o rechaza según tu disponibilidad.';
$icono = 'bi-envelope-paper-fill';
$accion = '<a href="/views/tutor/panel.php" class="btn btn-outline-secondary rounded-3 px-3"><i class="bi bi-arrow-left me-1"></i>Volver al panel</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo procesar la carta:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <?php $kpiClase='primary'; $kpiIcono='bi-envelope-paper'; $kpiValor=count($cartas); $kpiEtiqueta='Cartas recibidas'; include __DIR__ . '/../partials/kpi_card.php'; ?>
  </div>
  <div class="col-6 col-md-3">
    <?php $kpiClase='warning'; $kpiIcono='bi-hourglass-split'; $kpiValor=count(array_filter($cartas, fn($c) => $c['estado'] === 'pendiente')); $kpiEtiqueta='Por responder'; include __DIR__ . '/../partials/kpi_card.php'; ?>
  </div>
  <div class="col-6 col-md-3">
    <?php $kpiClase='info'; $kpiIcono='bi-check2-circle'; $kpiValor=count(array_filter($cartas, fn($c) => $c['estado'] === 'aceptada')); $kpiEtiqueta='Aceptadas'; include __DIR__ . '/../partials/kpi_card.php'; ?>
  </div>
  <div class="col-6 col-md-3">
    <?php $kpiClase='accent'; $kpiIcono='bi-speedometer'; $kpiValor=$cupoDisponible; $kpiEtiqueta='Cupo disponible'; include __DIR__ . '/../partials/kpi_card.php'; ?>
  </div>
</div>

<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-envelope-paper me-1 text-primary"></i>Mis cartas de designación</h5>
    <span class="badge bg-accent bg-opacity-10 text-accent border border-accent-subtle">Máximo <?= $cartaModel->cupoMaximo() ?> tutorías activas</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">N.º Carta</th>
          <th>Estudiante</th>
          <th>Materia</th>
          <th>Modalidad</th>
          <th>Generada</th>
          <th>Estado</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($cartas)): ?>
          <tr>
            <td colspan="7" class="text-center py-5">
              <?php $emptyTitulo='No tienes cartas de designación'; $emptyTexto='Cuando el administrador asigne una tutoría, se generará aquí la carta para tu revisión.'; include __DIR__ . '/../partials/empty_state.php'; ?>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($cartas as $carta): ?>
          <?php
            $btnEstado = 'status-badge status-neutral';
            if ($carta['estado'] === 'aceptada') $btnEstado = 'status-badge status-active';
            if ($carta['estado'] === 'rechazada') $btnEstado = 'status-badge status-cancelled';
            if ($carta['estado'] === 'pendiente') $btnEstado = 'status-badge status-pending';
          ?>
          <tr>
            <td class="ps-4 fw-bold text-dark">#<?= $carta['id_carta'] ?></td>
            <td>
              <div class="fw-medium text-dark"><?= htmlspecialchars($carta['estudiante_nombre'] . ' ' . $carta['estudiante_apellido']) ?></div>
            </td>
            <td><span class="fw-semibold text-primary"><?= htmlspecialchars($carta['nombre_materia']) ?></span></td>
            <td>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-mortarboard me-1"></i><?= htmlspecialchars($carta['modalidad_nombre'] ?? 'Sin modalidad') ?></span>
            </td>
            <td class="text-muted"><?= date('d/m/Y', strtotime($carta['fecha_generacion'])) ?></td>
            <td><span class="<?= $btnEstado ?>"><?= ucfirst($carta['estado']) ?></span></td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-carta-<?= $carta['id_carta'] ?>" title="Ver carta"><i class="bi bi-eye"></i></button>
                <?php if ($carta['estado'] === 'pendiente'): ?>
                  <form method="POST" action="/controllers/cartas_responder.php" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id_carta" value="<?= $carta['id_carta'] ?>">
                    <input type="hidden" name="accion" value="aceptar">
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="confirmarAceptarCarta(this.form)" title="Aceptar carta"><i class="bi bi-check-lg"></i></button>
                  </form>
                  <form method="POST" action="/controllers/cartas_responder.php" id="form-rechazo-<?= $carta['id_carta'] ?>" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id_carta" value="<?= $carta['id_carta'] ?>">
                    <input type="hidden" name="accion" value="rechazar">
                    <input type="hidden" name="motivo_rechazo" id="motivo-rechazo-<?= $carta['id_carta'] ?>" value="">
                    <button type="button" class="btn btn-outline-accent btn-sm" onclick="confirmarRechazoCarta('form-rechazo-<?= $carta['id_carta'] ?>', 'motivo-rechazo-<?= $carta['id_carta'] ?>')" title="Rechazar carta"><i class="bi bi-slash-circle"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($cartas as $carta): ?>
<div class="modal fade" id="modal-carta-<?= $carta['id_carta'] ?>" tabindex="-1" aria-labelledby="modal-carta-<?= $carta['id_carta'] ?>-label" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 overflow-hidden">
      <div class="modal-header bg-upds text-white py-3">
        <h5 class="modal-title fw-bold" id="modal-carta-<?= $carta['id_carta'] ?>-label"><i class="bi bi-envelope-paper me-1"></i>Carta de Designación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <ul class="nav nav-pills nav-underline mb-3 gap-1" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pestana-carta-texto-<?= $carta['id_carta'] ?>"
                    data-bs-toggle="tab" data-bs-target="#pane-carta-texto-<?= $carta['id_carta'] ?>"
                    type="button" role="tab" aria-controls="pane-carta-texto-<?= $carta['id_carta'] ?>" aria-selected="true">
              <i class="bi bi-card-text me-1"></i>Resumen
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pestana-carta-pdf-<?= $carta['id_carta'] ?>"
                    data-bs-toggle="tab" data-bs-target="#pane-carta-pdf-<?= $carta['id_carta'] ?>"
                    type="button" role="tab" aria-controls="pane-carta-pdf-<?= $carta['id_carta'] ?>" aria-selected="false">
              <i class="bi bi-file-earmark-pdf me-1"></i>Documento PDF
            </button>
          </li>
        </ul>
        <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-carta-texto-<?= $carta['id_carta'] ?>" role="tabpanel" aria-labelledby="pestana-carta-texto-<?= $carta['id_carta'] ?>">
        <div class="text-center mb-3">
          <div class="fs-4 fw-bold text-upds">UNIVERSIDAD PRIVADA DOMINGO SAVIO</div>
          <div class="text-muted small">Sede Tarija · Dirección de Carrera de Sistemas · Tecnologías Web</div>
        </div>
        <div class="border-top border-bottom py-2 text-center fw-bold text-dark bg-light">CARTA DE DESIGNACIÓN N.º <?= $carta['id_carta'] ?></div>
        <div class="p-3">
          <p class="mb-2 text-justify">Por medio de la presente se designa al docente tutor <strong>Prof. <?= htmlspecialchars($carta['tutor_nombre'] . ' ' . $carta['tutor_apellido']) ?></strong> para que guíe el proceso de tutoría académica del estudiante <strong><?= htmlspecialchars($carta['estudiante_nombre'] . ' ' . $carta['estudiante_apellido']) ?></strong> en la materia <strong><?= htmlspecialchars($carta['nombre_materia']) ?></strong>.</p>
          <dl class="row mb-0 small">
            <dt class="col-sm-4 text-muted">Modalidad de graduación</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($carta['modalidad_nombre'] ?? '—') ?></dd>
            <dt class="col-sm-4 text-muted">Primera sesión</dt>
            <dd class="col-sm-8"><?= date('d/m/Y', strtotime($carta['fecha_tutoria'])) ?> · <?= substr($carta['hora_inicio'], 0, 5) ?> - <?= substr($carta['hora_fin'], 0, 5) ?> h</dd>
            <dt class="col-sm-4 text-muted">Fecha de emisión</dt>
            <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($carta['fecha_generacion'])) ?></dd>
            <dt class="col-sm-4 text-muted">Estado</dt>
            <dd class="col-sm-8">
              <?php if ($carta['estado'] === 'aceptada'): ?><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Aceptada</span>
              <?php elseif ($carta['estado'] === 'rechazada'): ?><span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Rechazada</span>
              <?php else: ?><span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle">Pendiente</span><?php endif; ?>
            </dd>
          </dl>
          <?php if (!empty($carta['motivo_rechazo'])): ?>
            <div class="alert mt-3 alert-danger rounded-3 py-2 px-3"><strong>Motivo del rechazo:</strong> <?= htmlspecialchars($carta['motivo_rechazo']) ?></div>
          <?php endif; ?>
          <div class="text-end mt-4 pt-3 border-top">
            <div class="fw-bold text-uppercase">Prof. <?= htmlspecialchars($carta['tutor_nombre'] . ' ' . $carta['tutor_apellido']) ?></div>
            <div class="text-muted small">Firma del tutor <?= $carta['fecha_firma'] ? '· ' . date('d/m/Y', strtotime($carta['fecha_firma'])) : '' ?></div>
          </div>
        </div>
        </div>
        <div class="tab-pane fade" id="pane-carta-pdf-<?= $carta['id_carta'] ?>" role="tabpanel" aria-labelledby="pestana-carta-pdf-<?= $carta['id_carta'] ?>">
          <p class="text-muted small mb-2">
            Documento oficial generado y validado por el servidor. Usá los botones del pie para descargarlo o imprimirlo.
          </p>
          <iframe src="/controllers/carta_pdf.php?id=<?= (int) $carta['id_carta'] ?>&amp;ver=ver"
                  title="Carta de designación N.º <?= (int) $carta['id_carta'] ?>"
                  class="w-100 border rounded-3 bg-white"
                  style="height: 60vh; min-height: 420px;"></iframe>
        </div>
        </div>
      </div>
      <div class="modal-footer bg-light flex-wrap gap-2">
        <?php if ($carta['estado'] === 'pendiente'): ?>
          <form method="POST" action="/controllers/cartas_responder.php" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id_carta" value="<?= $carta['id_carta'] ?>">
            <input type="hidden" name="accion" value="aceptar">
            <button type="button" class="btn btn-success rounded-3 px-4" onclick="confirmarAceptarCarta(this.form)"><i class="bi bi-check-lg me-1"></i>Aceptar</button>
          </form>
          <button type="button" class="btn btn-outline-accent rounded-3 px-4" onclick="confirmarRechazoCarta('form-rechazo-<?= $carta['id_carta'] ?>', 'motivo-rechazo-<?= $carta['id_carta'] ?>')"><i class="bi bi-slash-circle me-1"></i>Rechazar</button>
        <?php else: ?>
          <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cerrar</button>
        <?php endif; ?>
        <div class="ms-auto d-flex flex-wrap gap-2">
          <a class="btn btn-outline-primary rounded-3 px-3"
             href="/controllers/carta_pdf.php?id=<?= (int) $carta['id_carta'] ?>&amp;ver=descargar"
             data-carta-pdf-descargar="<?= (int) $carta['id_carta'] ?>">
            <i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
          </a>
          <button type="button" class="btn btn-primary rounded-3 px-3"
                  onclick="imprimirCartaPDF(<?= (int) $carta['id_carta'] ?>)">
            <i class="bi bi-printer me-1"></i>Imprimir
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<div class="modal fade" id="modal-confirmar-rechazo" tabindex="-1" aria-hidden="true"></div>

<script>
  function confirmarAceptarCarta(formulario) {
    Swal.fire({
      title: '¿Aceptar la carta de designación?',
      text: 'Al aceptar, la tutoría quedará asignada y contará dentro de tu cupo activo.',
      icon: 'success',
      showCancelButton: true,
      confirmButtonColor: '#198754',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Sí, aceptar',
      cancelButtonText: 'Cancelar',
      customClass: { popup: 'swal-upds-popup' }
    }).then((result) => {
      if (result.isConfirmed) { formulario.submit(); }
    });
  }

  function confirmarRechazoCarta(formId, motivoId) {
    Swal.fire({
      title: 'Motivo del rechazo',
      input: 'textarea',
      inputPlaceholder: 'Explica por qué rechazas la carta (mínimo 5 caracteres)',
      inputAttributes: { maxlength: 255 },
      showCancelButton: true,
      confirmButtonColor: '#b42318',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Rechazar carta',
      cancelButtonText: 'Volver',
      customClass: { popup: 'swal-upds-popup' },
      inputValidator: (valor) => {
        if (!valor || valor.trim().length < 5) {
          return 'El motivo es obligatorio (mínimo 5 caracteres).';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById(motivoId).value = result.value.trim();
        document.getElementById(formId).submit();
      }
    });
  }

  /* =========================================================
     PDF de la carta de designación
     El backend valida existencia e integridad del archivo antes de
     servirlo; aquí solo se muestra el error como Toast.
     ========================================================= */
  const MARCO_CARTA_PDF = 'marco-carta-pdf';

  function marcoCartaPdf(idCarta) {
    let marco = document.getElementById(MARCO_CARTA_PDF);
    if (!marco) {
      marco = document.createElement('iframe');
      marco.id = MARCO_CARTA_PDF;
      marco.title = 'Vista previa de la carta de designación';
      marco.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
      document.body.appendChild(marco);
    }
    marco.dataset.cartaId = String(idCarta);
    return marco;
  }

  async function cargarCartaPdf(idCarta, destino) {
    const url = '/controllers/carta_pdf.php?id=' + encodeURIComponent(idCarta) + '&ver=ver';
    try {
      const respuesta = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/pdf' } });
      if (respuesta.status === 200 && (respuesta.headers.get('content-type') || '').includes('application/pdf')) {
        const blob = await respuesta.blob();
        if (destino === 'marco') {
          marcoCartaPdf(idCarta).src = URL.createObjectURL(blob);
          return true;
        }
        const enlace = document.createElement('a');
        enlace.href = URL.createObjectURL(blob);
        enlace.download = 'carta-designacion-' + idCarta + '.pdf';
        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove();
        setTimeout(() => URL.revokeObjectURL(enlace.href), 4000);
        return true;
      }
      let mensaje = 'No se pudo generar el PDF de la carta.';
      try {
        const cuerpo = await respuesta.json();
        if (cuerpo && cuerpo.message) mensaje = cuerpo.message;
      } catch (e) { /* la respuesta no era JSON */ }
      mostrarToast(mensaje, 'danger');
    } catch (e) {
      mostrarToast('No hay conexión con el servidor para generar el PDF.', 'danger');
    }
    return false;
  }

  /** Imprime usando la función nativa del navegador sobre el PDF ya validado. */
  function imprimirCartaPDF(idCarta) {
    const marco = marcoCartaPdf(idCarta);
    const alCargar = () => {
      marco.contentWindow.focus();
      marco.contentWindow.print();
    };
    marco.onload = alCargar;
    if (!marco.src || marco.dataset.cartaId !== String(idCarta)) {
      marco.src = '/controllers/carta_pdf.php?id=' + encodeURIComponent(idCarta) + '&ver=ver';
    } else {
      alCargar();
    }
  }

  document.querySelectorAll('[data-carta-pdf-descargar]').forEach((enlace) => {
    enlace.addEventListener('click', (evento) => {
      evento.preventDefault();
      cargarCartaPdf(enlace.dataset.cartaPdfDescargar, 'descarga');
    });
  });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>