<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Expediente de Tutoría - UPDS';
include __DIR__ . '/../layouts/header.php';

require_once __DIR__ . '/../../models/NotificationModel.php';
$notifModel = new NotificationModel($pdo);
$idsTutoria = $tutoriaSeleccionada !== null ? $notifModel->usuariosDeTutoria($tutoriaSeleccionada['id_tutoria']) : [];
?>

<?php
$migas = [
    ['texto' => 'Tutorías', 'url' => '/controllers/tutorias_listar.php'],
    ['texto' => 'Expedientes', 'actual' => true],
];
$titulo = 'Expediente de Tutoría';
$descripcion = 'Adjunta y consulta documentos (.doc, .docx, .pdf) del expediente de la Modalidad de Grado.';
$icono = 'bi-folder2-open';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar3 me-1 text-primary"></i>Tutorías de Modalidad de Grado</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr><th class="ps-4">Tutoría</th><th>Tutor</th><th>Estudiante</th><th>Estado</th><th class="text-end pe-4">Expediente</th></tr>
          </thead>
          <tbody>
            <?php if (empty($tutoriasMG)): ?>
              <tr><td colspan="5" class="text-center py-5">
                <?php $emptyTitulo='No hay tutorías de MG'; $emptyTexto='Las tutorías de Modalidad de Grado con expediente aparecerán aquí.'; include __DIR__ . '/../partials/empty_state.php'; ?>
              </td></tr>
            <?php endif; ?>
            <?php foreach ($tutoriasMG as $t): ?>
              <tr class="<?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'table-primary' : '' ?>">
                <td class="ps-4 fw-bold text-dark">#<?= $t['id_tutoria'] ?></td>
                <td>Prof. <?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido']) ?></td>
                <td><span class="badge rounded-pill border px-3 py-1 badge-accent"><?= str_replace('_', ' ', ucfirst($t['estado'])) ?></span></td>
                <td class="text-end pe-4">
                  <a href="/controllers/expediente_documentos.php?id=<?= (int) $t['id_tutoria'] ?>"
                     class="btn btn-sm js-abrir-expediente <?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'btn-primary' : 'btn-outline-primary' ?> rounded-3"
                     data-id="<?= (int) $t['id_tutoria'] ?>"><i class="bi bi-folder2-open me-1"></i>Abrir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div id="panel-expediente">
    <?php if ($tutoriaSeleccionada !== null): ?>
      <div class="card card-custom shadow-sm mb-3">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-upload me-1 text-primary"></i>Adjuntar documento — Tutoría #<?= $tutoriaSeleccionada['id_tutoria'] ?></h5>
        </div>
        <div class="card-body">
          <form method="POST" action="/controllers/expediente_documentos.php" enctype="multipart/form-data" autocomplete="off" id="form-expediente">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="accion" value="subir">
            <input type="hidden" name="id" value="<?= (int) $tutoriaSeleccionada['id_tutoria'] ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Destinatario *</label>
              <select name="destinatario" class="form-select rounded-3 py-2" required>
                <option value="">Selecciona al destinatario</option>
                <option value="<?= (int) ($idsTutoria['id_usuario_estudiante'] ?? 0) ?>" <?= (int) ($idsTutoria['id_usuario_estudiante'] ?? 0) === 0 ? 'disabled' : '' ?>>
                  Estudiante: <?= htmlspecialchars($tutoriaSeleccionada['estudiante_nombre'] ?? '') . ' ' . htmlspecialchars($tutoriaSeleccionada['estudiante_apellido'] ?? '') ?>
                </option>
                <option value="<?= (int) ($idsTutoria['id_usuario_tutor'] ?? 0) ?>" <?= (int) ($idsTutoria['id_usuario_tutor'] ?? 0) === 0 ? 'disabled' : '' ?>>
                  Tutor: <?= htmlspecialchars($tutoriaSeleccionada['tutor_nombre'] ?? '') . ' ' . htmlspecialchars($tutoriaSeleccionada['tutor_apellido'] ?? '') ?>
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase" for="input-archivo">Archivo (.doc, .docx, .pdf) *</label>
              <input type="file" name="archivo" id="input-archivo" class="form-control rounded-3 py-2" accept="<?= htmlspecialchars(document_upload_accepted_attr(), ENT_QUOTES, 'UTF-8') ?>" required>
              <div class="form-text text-muted" id="ayuda-archivo">Tamaño máximo: <?= number_format($docModel->tamanioMaximo() / 1048576, 0) ?> MB. Se valida el contenido real del archivo, no solo su extensión.</div>
              <div class="invalid-feedback d-block" id="error-archivo" hidden></div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small text-uppercase" for="input-descripcion">Descripción (opcional)</label>
              <textarea name="descripcion" id="input-descripcion" class="form-control rounded-3" rows="2" maxlength="500" placeholder="P. ej.: Acta de defensa firmada, informe del dictamen, etc."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2"><i class="bi bi-upload me-1"></i>Subir y notificar</button>
          </form>
        </div>
      </div>

      <div class="card card-custom shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0"><i class="bi bi-files me-1 text-primary"></i>Documentos del expediente</h5>
        </div>
        <div class="card-body d-flex flex-column gap-2">
          <?php if (empty($documentos)): ?>
            <p class="text-muted small mb-0">Aún no hay documentos en este expediente.</p>
          <?php endif; ?>
          <?php foreach ($documentos as $d): ?>
            <?php
            $extensionDoc = strtolower((string) pathinfo((string) $d['ruta_archivo'], PATHINFO_EXTENSION));
            $verificacionDoc = verify_stored_file((string) $d['ruta_archivo'], 'uploads/expedientes');
            $disponible = !empty($verificacionDoc['ok']);
            ?>
            <div class="border rounded-3 p-2 bg-light d-flex align-items-center gap-2">
              <i class="bi bi-file-earmark-<?= $extensionDoc === 'pdf' ? 'pdf' : 'word' ?> fs-4 <?= $disponible ? 'text-primary' : 'text-secondary' ?>"></i>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-medium text-truncate" title="<?= htmlspecialchars($d['nombre_original']) ?>"><?= htmlspecialchars($d['nombre_original']) ?></div>
                <small class="text-muted">
                  <?= date('d/m/Y H:i', strtotime($d['fecha'])) ?> · Para: <?= htmlspecialchars($d['destinatario_nombre'] . ' ' . $d['destinatario_apellido']) ?>
                </small>
                <?php if (!empty($d['descripcion'])): ?><div class="small text-muted text-truncate"><?= htmlspecialchars($d['descripcion']) ?></div><?php endif; ?>
                <?php if (!$disponible): ?>
                  <div class="small text-warning-emphasis"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($verificacionDoc['motivo']) ?></div>
                <?php endif; ?>
              </div>
              <?php if ($disponible): ?>
                <button type="button"
                        class="btn btn-sm btn-outline-primary rounded-3 js-ver-documento download-btn"
                        data-url="/controllers/expediente_documento_descargar.php?id=<?= (int) $d['id_documento'] ?>&ver=inline"
                        data-nombre="<?= htmlspecialchars($d['nombre_original'], ENT_QUOTES, 'UTF-8') ?>"
                        title="Previsualizar"><i class="bi bi-eye"></i></button>
                <a href="/controllers/expediente_documento_descargar.php?id=<?= (int) $d['id_documento'] ?>&ver=descargar"
                   class="btn btn-sm btn-primary rounded-3 js-descargar-documento download-btn"
                   data-nombre="<?= htmlspecialchars($d['nombre_original'], ENT_QUOTES, 'UTF-8') ?>"
                   title="Descargar"><i class="bi bi-download"></i></a>
              <?php else: ?>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" disabled title="<?= htmlspecialchars($verificacionDoc['motivo']) ?>"><i class="bi bi-download"></i></button>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="card card-custom shadow-sm">
        <div class="card-body p-4">
          <?php $emptyTitulo='Selecciona una tutoría'; $emptyTexto='Elige una tutoría de Modalidad de Grado para administrar su expediente.'; $emptyIcono='bi-folder2-open'; include __DIR__ . '/../partials/empty_state.php'; ?>
        </div>
      </div>
    <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDocumentoTitulo">
          <i class="bi bi-file-earmark-text me-1 text-primary"></i><span id="modalDocumentoNombre">Documento</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger d-none rounded-3 mb-3" id="modalDocumentoError" role="alert"></div>
        <iframe id="modalDocumentoFrame" class="border rounded-3 w-100" style="height: 62vh;" title="Previsualización del documento"></iframe>
      </div>
      <div class="modal-footer flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i>Cerrar
        </button>
        <button type="button" class="btn btn-outline-primary rounded-3 px-3" id="btnDocumentoImprimir">
          <i class="bi bi-printer me-1"></i>Imprimir
        </button>
        <a href="#" class="btn btn-primary rounded-3 px-3" id="btnDocumentoDescargar" download>
          <i class="bi bi-file-earmark-arrow-down me-1"></i><span id="btnDocumentoDescargarTexto">Descargar</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  const LIMITE_BYTES = <?= (int) $docModel->tamanioMaximo() ?>;
  const EXTENSIONES = ['pdf', 'doc', 'docx'];
  const MIME_PDF = 'application/pdf';
  const CSRF = <?= json_encode(csrf_token()) ?>;

  const panel = document.getElementById('panel-expediente');
  const modalEl = document.getElementById('modalDocumento');
  const frame = document.getElementById('modalDocumentoFrame');
  const nombreDoc = document.getElementById('modalDocumentoNombre');
  const btnDescargar = document.getElementById('btnDocumentoDescargar');
  const btnDescargarTexto = document.getElementById('btnDocumentoDescargarTexto');
  const btnImprimir = document.getElementById('btnDocumentoImprimir');
  const errorDocumento = document.getElementById('modalDocumentoError');
  let modal = null;
  let urlActual = '';
  let urlDescargaActual = '';

  function escapar(texto) {
    const div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML;
  }

  function formatearBytes(bytes) {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  // ---------- Validación del archivo antes de enviarlo ----------
  function validarArchivo(archivo) {
    const error = document.getElementById('error-archivo');
    if (!error) return true;

    if (!archivo) {
      error.hidden = false;
      error.textContent = 'Seleccioná un archivo .doc, .docx o .pdf.';
      return false;
    }

    const extension = (archivo.name.split('.').pop() || '').toLowerCase();
    if (EXTENSIONES.indexOf(extension) === -1) {
      error.hidden = false;
      error.textContent = 'La extensión .' + extension + ' no está permitida. Usá .doc, .docx o .pdf.';
      return false;
    }

    if (archivo.size > LIMITE_BYTES) {
      error.hidden = false;
      error.textContent = 'El archivo pesa ' + formatearBytes(archivo.size) + ' y supera el máximo de '
        + formatearBytes(LIMITE_BYTES) + '.';
      return false;
    }

    if (archivo.size === 0) {
      error.hidden = false;
      error.textContent = 'El archivo está vacío.';
      return false;
    }

    if (archivo.type && archivo.type !== MIME_PDF
        && archivo.type.indexOf('msword') === -1
        && archivo.type.indexOf('wordprocessingml') === -1
        && archivo.type !== 'application/octet-stream'
        && archivo.type !== 'application/zip') {
      error.hidden = false;
      error.textContent = 'El navegador reporta el tipo "' + archivo.type + '", que no es un documento válido.';
      return false;
    }

    error.hidden = true;
    error.textContent = '';
    return true;
  }

  const inputArchivo = document.getElementById('input-archivo');
  if (inputArchivo) {
    inputArchivo.addEventListener('change', function () {
      validarArchivo(inputArchivo.files[0]);
    });
  }

  // ---------- Render del panel de expediente ----------
  function renderDocumentos(lista) {
    if (!lista.length) {
      return '<p class="text-muted small mb-0">Aún no hay documentos en este expediente.</p>';
    }

    return lista.map(function (d) {
      const icono = d.esPdf ? 'bi-file-earmark-pdf' : 'bi-file-earmark-word';
      const colorIcono = d.disponible ? 'text-primary' : 'text-secondary';
      let acciones;

      if (d.disponible) {
        acciones = '<button type="button" class="btn btn-sm btn-outline-primary rounded-3 js-ver-documento"'
          + ' data-url="' + escapar(d.urlVer) + '" data-nombre="' + escapar(d.nombre) + '" title="Previsualizar">'
          + '<i class="bi bi-eye"></i></button>'
          + '<a href="' + escapar(d.urlDescargar) + '" class="btn btn-sm btn-primary rounded-3 js-descargar-documento"'
          + ' data-nombre="' + escapar(d.nombre) + '" title="Descargar"><i class="bi bi-download"></i></a>';
      } else {
        acciones = '<button type="button" class="btn btn-sm btn-outline-secondary rounded-3" disabled'
          + ' title="' + escapar(d.motivo) + '"><i class="bi bi-download"></i></button>';
      }

      const descripcion = d.descripcion
        ? '<div class="small text-muted text-truncate">' + escapar(d.descripcion) + '</div>' : '';
      const aviso = d.disponible ? ''
        : '<div class="small text-warning-emphasis"><i class="bi bi-exclamation-triangle me-1"></i>' + escapar(d.motivo) + '</div>';

      return '<div class="border rounded-3 p-2 bg-light d-flex align-items-center gap-2">'
        + '<i class="bi ' + icono + ' fs-4 ' + colorIcono + '"></i>'
        + '<div class="flex-grow-1 min-w-0">'
        + '<div class="fw-medium text-truncate" title="' + escapar(d.nombre) + '">' + escapar(d.nombre) + '</div>'
        + '<small class="text-muted">' + escapar(d.fecha) + ' · Para: ' + escapar(d.destinatario)
        + (d.bytes ? ' · ' + escapar(formatearBytes(d.bytes)) : '') + '</small>'
        + descripcion + aviso
        + '</div>' + acciones + '</div>';
    }).join('');
  }

  function renderPanel(datos) {
    const opciones = datos.destinatarios.map(function (dest) {
      return '<option value="' + dest.id + '"' + (dest.id === 0 ? ' disabled' : '') + '>'
        + escapar(dest.etiqueta) + '</option>';
    }).join('');

    panel.innerHTML = ''
      + '<div class="card card-custom shadow-sm mb-3">'
      + '  <div class="card-header bg-white py-3">'
      + '    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-upload me-1 text-primary"></i>Adjuntar documento — Tutoría #'
      + datos.tutoria.id + '</h5>'
      + '  </div>'
      + '  <div class="card-body">'
      + '    <form id="form-expediente" autocomplete="off">'
      + '      <input type="hidden" name="csrf_token" value="' + escapar(CSRF) + '">'
      + '      <input type="hidden" name="accion" value="subir">'
      + '      <input type="hidden" name="id" value="' + datos.tutoria.id + '">'
      + '      <div class="mb-3">'
      + '        <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-destinatario">Destinatario *</label>'
      + '        <select name="destinatario" id="select-destinatario" class="form-select rounded-3 py-2" required>'
      + '          <option value="">Selecciona al destinatario</option>' + opciones
      + '        </select>'
      + '      </div>'
      + '      <div class="mb-3">'
      + '        <label class="form-label fw-semibold text-secondary small text-uppercase" for="input-archivo">Archivo (.doc, .docx, .pdf) *</label>'
      + '        <input type="file" name="archivo" id="input-archivo" class="form-control rounded-3 py-2"'
      + '          accept=".pdf,.doc,.docx,application/pdf,application/msword,'
      + 'application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>'
      + '        <div class="form-text text-muted">Tamaño máximo: ' + formatearBytes(datos.limiteBytes)
      + '. Se valida el contenido real del archivo, no solo su extensión.</div>'
      + '        <div class="invalid-feedback d-block" id="error-archivo" hidden></div>'
      + '      </div>'
      + '      <div class="mb-3">'
      + '        <label class="form-label fw-semibold text-secondary small text-uppercase" for="input-descripcion">Descripción (opcional)</label>'
      + '        <textarea name="descripcion" id="input-descripcion" class="form-control rounded-3" rows="2" maxlength="500"'
      + '          placeholder="P. ej.: Acta de defensa firmada, informe del dictamen, etc."></textarea>'
      + '      </div>'
      + '      <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">'
      + '        <i class="bi bi-upload me-1"></i>Subir y notificar</button>'
      + '    </form>'
      + '  </div>'
      + '</div>'
      + '<div class="card card-custom shadow-sm">'
      + '  <div class="card-header bg-white py-3">'
      + '    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-files me-1 text-primary"></i>Documentos del expediente</h5>'
      + '  </div>'
      + '  <div class="card-body d-flex flex-column gap-2">' + renderDocumentos(datos.documentos) + '</div>'
      + '</div>';

    const nuevoInput = document.getElementById('input-archivo');
    if (nuevoInput) {
      nuevoInput.addEventListener('change', function () { validarArchivo(nuevoInput.files[0]); });
    }
  }

  function marcarFilaActiva(idTutoria) {
    document.querySelectorAll('.js-abrir-expediente').forEach(function (boton) {
      const activa = Number(boton.dataset.id) === Number(idTutoria);
      boton.classList.toggle('btn-primary', activa);
      boton.classList.toggle('btn-outline-primary', !activa);
    });
  }

  function cargarExpediente(idTutoria) {
    const url = '/controllers/expediente_documentos.php?accion=documentos&id=' + encodeURIComponent(idTutoria);

    const panel = document.getElementById('panel-expediente');
    if (panel) panel.classList.add('loading');

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
      .then(function (respuesta) {
        return respuesta.json().then(function (json) {
          if (!respuesta.ok || json.status !== 'success') {
            throw new Error(json.message || 'No se pudo cargar el expediente.');
          }
          return json;
        });
      })
      .then(function (json) {
        if (panel) panel.classList.remove('loading');
        renderPanel(json);
        marcarFilaActiva(idTutoria);
        history.replaceState({ id: idTutoria }, '', '/controllers/expediente_documentos.php?id=' + idTutoria);
        constobserver = document.getElementById('form-expediente');
        if (constobserver) { conectarFormulario(constobserver); }
      })
      .catch(function (err) {
        if (panel) panel.classList.remove('loading');
        mostrarToast(err.message, 'danger');
      });
  }

  function conectarFormulario(form) {
    form.addEventListener('submit', function (evento) {
      evento.preventDefault();

      const input = document.getElementById('input-archivo');
      if (!validarArchivo(input ? input.files[0] : null)) {
        return;
      }

      const boton = form.querySelector('button[type="submit"]');
      const htmlOriginal = boton.innerHTML;
      boton.disabled = true;
      boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Subiendo...';

      fetch('/controllers/expediente_documentos.php', {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (respuesta) {
          return respuesta.json().then(function (json) {
            if (!respuesta.ok || json.status !== 'success') {
              throw new Error(json.message || 'No se pudo subir el documento.');
            }
            return json;
          });
        })
        .then(function (json) {
          renderPanel(json);
          conectarFormulario(document.getElementById('form-expediente'));
          mostrarToast('Documento subido y notificado correctamente.', 'success');
        })
        .catch(function (err) {
          mostrarToast(err.message, 'danger');
        })
        .finally(function () {
          const botonActivo = document.querySelector('#form-expediente button[type="submit"]');
          if (botonActivo) {
            botonActivo.disabled = false;
            botonActivo.innerHTML = htmlOriginal;
          }
        });
    });
  }

  // ---------- Modal de previsualización ----------
  function ocultarErrorDocumento() {
    if (!errorDocumento) return;
    errorDocumento.classList.add('d-none');
    errorDocumento.textContent = '';
  }

  function mostrarErrorDocumento(mensaje) {
    if (errorDocumento) {
      errorDocumento.textContent = mensaje;
      errorDocumento.classList.remove('d-none');
    }
    mostrarToast(mensaje, 'danger');
  }

  function abrirModal(url, nombre, botonOrigen) {
    urlActual = url;
    urlDescargaActual = url.replace('ver=inline', 'ver=descargar');
    nombreDoc.textContent = nombre || 'Documento';
    btnDescargar.href = urlDescargaActual;
    /* La etiqueta sigue al tipo real: no todo documento del expediente es PDF. */
    const esPdf = /\.pdf(\?|$)/i.test(url) || /\.pdf(\?|$)/i.test(nombre || '');
    btnDescargarTexto.textContent = esPdf ? 'Descargar PDF' : 'Descargar documento';
    btnImprimir.disabled = !esPdf;
    btnImprimir.title = esPdf ? '' : 'Solo los documentos PDF se pueden imprimir';
    ocultarErrorDocumento();
    if (botonOrigen) botonOrigen.classList.add('loading');
    frame.src = url;
    if (!modal) { modal = new bootstrap.Modal(modalEl); }
    modal.show();
    if (botonOrigen) {
      setTimeout(function () { botonOrigen.classList.remove('loading'); }, 1200);
    }
  }

  if (modalEl) {
    /* El backend valida existencia e integridad: si responde con un error,
       se muestra dentro del modal y también como Toast. */
    frame.addEventListener('load', function () {
      try {
        const tipo = (frame.contentDocument && frame.contentDocument.contentType) || '';
        if (tipo && tipo.indexOf('application/pdf') === -1 && tipo.indexOf('html') === -1) {
          ocultarErrorDocumento();
        }
      } catch (e) { /* origen distinto: el backend ya validó la respuesta */ }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      frame.src = 'about:blank';
      urlActual = '';
      urlDescargaActual = '';
    });

    btnDescargar.addEventListener('click', function (evento) {
      if (!urlDescargaActual) {
        evento.preventDefault();
        mostrarToast('No hay ningún documento cargado para descargar.', 'warning');
        return;
      }
      /* Comprueba que el archivo siga existiendo antes de iniciar la descarga. */
      evento.preventDefault();
      fetch(urlDescargaActual, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        .then(function (respuesta) {
          if (respuesta.ok) {
            window.location.href = urlDescargaActual;
            return;
          }
          return respuesta.json()
            .then(function (cuerpo) { throw new Error(cuerpo.message || 'El archivo no está disponible.'); })
            .catch(function (e) { throw e; });
        })
        .catch(function (error) {
          mostrarErrorDocumento(error.message || 'El archivo no está disponible.');
        });
    });

    btnImprimir.addEventListener('click', function () {
      if (!urlActual) {
        mostrarToast('No hay ningún documento cargado para imprimir.', 'warning');
        return;
      }
      /* Impresión nativa del navegador sobre el PDF ya validado por el backend. */
      try {
        frame.contentWindow.focus();
        frame.contentWindow.print();
      } catch (e) {
        const ventana = window.open(urlActual, '_blank');
        if (ventana) {
          ventana.addEventListener('load', function () { ventana.print(); });
        } else {
          mostrarToast('El navegador bloqueó la impresión. Permití las ventanas emergentes.', 'warning');
        }
      }
    });
  }

  document.addEventListener('click', function (evento) {
    const ver = evento.target.closest('.js-ver-documento');
    if (ver) {
      evento.preventDefault();
      abrirModal(ver.dataset.url, ver.dataset.nombre, ver);
      return;
    }

    const descargar = evento.target.closest('.js-descargar-documento');
    if (descargar) {
      evento.preventDefault();
      descargar.classList.add('loading');
      const nombre = descargar.dataset.nombre || 'documento';
      mostrarToast('Preparando descarga de "' + nombre + '"...', 'info');
      const enlace = document.createElement('a');
      enlace.href = descargar.getAttribute('href');
      enlace.rel = 'noopener';
      document.body.appendChild(enlace);
      enlace.click();
      enlace.remove();
      setTimeout(function () { descargar.classList.remove('loading'); }, 1200);
      return;
    }

    const abrir = evento.target.closest('.js-abrir-expediente');
    if (abrir) {
      evento.preventDefault();
      cargarExpediente(abrir.dataset.id);
    }
  });

  const formInicial = document.getElementById('form-expediente');
  if (formInicial) { conectarFormulario(formInicial); }
})();
</script>