<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Solicitar Tutoría - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Mis Tutorías', 'url' => '/views/estudiante/panel.php'],
    ['texto' => 'Solicitar Tutoría', 'actual' => true],
];
$titulo = 'Solicitar Tutoría';
$descripcion = 'Apoyo académico o Modalidad de Grado: elige materia, docente y el horario que prefieras.';
$icono = 'bi-calendar-plus-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo enviar la solicitud:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<ul class="nav nav-pills gap-2 mb-4" role="tablist">
  <li class="nav-item" role="presentation">
    <button type="button" class="nav-link <?= $tipo === 'apoyo' ? 'active' : '' ?>" id="tab-apoyo" data-bs-toggle="pill" data-bs-target="#pane-apoyo" role="tab" aria-controls="pane-apoyo" aria-selected="<?= $tipo === 'apoyo' ? 'true' : 'false' ?>">
      <i class="bi bi-journal-bookmark me-1"></i>Tutoría de Apoyo
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <?php if ($accesoMG): ?>
      <button type="button" class="nav-link <?= $tipo === 'grado' ? 'active' : '' ?>" id="tab-grado" data-bs-toggle="pill" data-bs-target="#pane-grado" role="tab" aria-controls="pane-grado" aria-selected="<?= $tipo === 'grado' ? 'true' : 'false' ?>">
        <i class="bi bi-mortarboard me-1"></i>Modalidad de Grado
      </button>
    <?php else: ?>
      <span class="nav-link disabled d-inline-flex align-items-center" aria-disabled="true" title="Debes cumplir los requisitos para acceder a Modalidad de Grado.">
        <i class="bi bi-lock me-1"></i>Modalidad de Grado
        <a href="/controllers/mg_comprobantes_registrar.php" class="text-secondary ms-1" data-bs-toggle="tooltip" title="Ver requisitos"><i class="bi bi-question-circle"></i></a>
      </span>
    <?php endif; ?>
  </li>
</ul>

<?php if (!$accesoMG): ?>
  <div class="alert alert-warning d-flex align-items-start gap-2 rounded-3" role="alert">
    <i class="bi bi-lock-fill mt-1" aria-hidden="true"></i>
    <div>
      <strong>Modalidad de Grado bloqueada.</strong>
      Aún no cumplís los requisitos para solicitar una tutoría de graduación.
      <a href="/controllers/mg_comprobantes_registrar.php" class="alert-link">Revisá y registrá tu comprobante</a>
      para que Administración pueda habilitar la opción.
    </div>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm">
  <div class="card-body p-4">
    <div class="mb-3">
      <a href="/controllers/tutorias_unirse.php" class="btn btn-outline-secondary btn-sm rounded-3"><i class="bi bi-people me-1"></i>Unirme a una tutoría de apoyo abierta</a>
      <small class="text-muted ms-2">Si ya hay una tutoría de apoyo abierta de tu materia, podés sumarte y no necesitás crear una nueva.</small>
    </div>

    <form method="POST" action="/controllers/tutorias_solicitar.php" id="form-solicitud" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="tipo" id="input-tipo" value="<?= htmlspecialchars($tipo) ?>">
      <input type="hidden" name="id_carrera_estudiante" id="id-carrera-estudiante" value="<?= (int) ($estudiante['id_carrera'] ?? 0) ?>">
      <input type="hidden" name="id_materia" id="input-id-materia" value="">
      <input type="hidden" name="id_tutor" id="input-id-tutor" value="">
      <input type="hidden" name="id_modalidad" id="input-id-modalidad" value="">

      <div class="tab-content">
        <div class="tab-pane fade <?= $tipo === 'apoyo' ? 'show active' : '' ?>" id="pane-apoyo" role="tabpanel" aria-labelledby="tab-apoyo">
          <?php include __DIR__ . '/partials/form_materia_docente.php'; ?>
          <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>Si otro estudiante ya abrió una tutoría de apoyo de esta materia, podés <a href="/controllers/tutorias_unirse.php">unirte a ella</a> en lugar de crear una nueva.</p>
        </div>

        <?php if ($accesoMG): ?>
        <div class="tab-pane fade <?= $tipo === 'grado' ? 'show active' : '' ?>" id="pane-grado" role="tabpanel" aria-labelledby="tab-grado">
          <h5 class="fw-bold text-dark mb-3"><i class="bi bi-mortarboard me-1 text-primary"></i>Tutoría de graduación</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-modalidad">Modalidad de graduación *</label>
              <select id="select-modalidad" class="form-select rounded-3 py-2">
                <option value="">Selecciona una modalidad</option>
                <?php foreach ($modalidades as $md): ?>
                  <option value="<?= $md['id_modalidad'] ?>" <?= (($_POST['id_modalidad'] ?? '') == $md['id_modalidad']) ? 'selected' : '' ?>><?= htmlspecialchars($md['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-materia-grado">Materia (área de tu carrera) *</label>
              <select id="select-materia-grado" class="form-select rounded-3 py-2 select2-enabled" data-placeholder="Selecciona una materia">
                <option value="">Selecciona una materia</option>
                <?php foreach ($materias as $materia): ?>
                  <option value="<?= $materia['id_materia'] ?>" <?= (($_POST['id_materia'] ?? '') == $materia['id_materia']) ? 'selected' : '' ?>><?= htmlspecialchars($materia['nombre_materia']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-tutor-grado">Docente tutor de tu carrera *</label>
              <select id="select-tutor-grado" class="form-select rounded-3 py-2" data-placeholder="Primero elige la materia" disabled>
                <option value="">Primero elige la materia</option>
              </select>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <hr class="my-4">
      <?php include __DIR__ . '/partials/form_agenda.php'; ?>

      <div class="mt-4 d-flex gap-2 justify-content-end">
        <a href="/views/estudiante/panel.php" class="btn btn-outline-secondary rounded-3 px-3">Cancelar</a>
        <button type="submit" class="btn btn-primary rounded-3 px-4" id="btn-enviar"><i class="bi bi-send me-1"></i>Enviar solicitud</button>
      </div>
    </form>
  </div>
</div>

<script>
  const selectMateriaApoyo = document.getElementById('select-materia');
  const selectTutorApoyo = document.getElementById('select-tutor');
  const selectMateriaGrado = document.getElementById('select-materia-grado');
  const selectTutorGrado = document.getElementById('select-tutor-grado');
  const selectModalidad = document.getElementById('select-modalidad');
  const inputTipo = document.getElementById('input-tipo');
  const inputMateria = document.getElementById('input-id-materia');
  const inputTutor = document.getElementById('input-id-tutor');
  const inputModalidad = document.getElementById('input-id-modalidad');
  const idCarreraEstudiante = document.getElementById('id-carrera-estudiante').value;

  const paneles = {
    apoyo: { materia: selectMateriaApoyo, tutor: selectTutorApoyo, modalidad: null },
    grado: { materia: selectMateriaGrado, tutor: selectTutorGrado, modalidad: selectModalidad }
  };

  function panelActivo() {
    return inputTipo.value === 'grado' ? paneles.grado : paneles.apoyo;
  }

  function sincronizarFormulario() {
    Object.keys(paneles).forEach((clave) => {
      const activo = clave === inputTipo.value;
      const panel = paneles[clave];
      if (panel.materia) panel.materia.required = activo;
      if (panel.tutor) panel.tutor.required = activo;
      if (panel.modalidad) panel.modalidad.required = activo;
    });

    const panel = panelActivo();
    inputMateria.value = panel.materia ? panel.materia.value : '';
    inputTutor.value = panel.tutor ? panel.tutor.value : '';
    inputModalidad.value = inputTipo.value === 'grado' && panel.modalidad ? panel.modalidad.value : '';
  }

  async function cargarTutores(idMateria, selectTutor, mensajeSinTutores) {
    selectTutor.disabled = true;
    selectTutor.innerHTML = '<option value="">Cargando docentes...</option>';
    if (!idMateria) {
      selectTutor.innerHTML = '<option value="">Primero elige una materia</option>';
      return;
    }

    try {
      const url = '/controllers/TutorController.php?action=por_materia&id_materia=' + encodeURIComponent(idMateria)
        + '&id_carrera=' + encodeURIComponent(idCarreraEstudiante);
      const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      if (!res.ok || json.status !== 'success' || !Array.isArray(json.data)) {
        throw new Error(json.message || 'Error al consultar tutores.');
      }

      const tutores = json.data;
      selectTutor.innerHTML = tutores.length
        ? '<option value="">Selecciona un tutor</option>'
        : '<option value="">' + mensajeSinTutores + '</option>';
      tutores.forEach((t) => {
        const opcion = document.createElement('option');
        opcion.value = t.id_tutor;
        opcion.textContent = 'Prof. ' + t.nombre + ' ' + t.apellido + (t.especialidad ? ' - ' + t.especialidad : '');
        selectTutor.appendChild(opcion);
      });
      selectTutor.disabled = tutores.length === 0;
    } catch (err) {
      mostrarToast(err.message, 'danger');
      selectTutor.innerHTML = '<option value="">No se pudieron cargar los docentes</option>';
      selectTutor.disabled = true;
    }
  }

  if (selectMateriaApoyo) {
    selectMateriaApoyo.addEventListener('change', () => {
      cargarTutores(selectMateriaApoyo.value, selectTutorApoyo, 'No hay tutores de tu carrera para esta materia');
    });
  }
  if (selectMateriaGrado) {
    selectMateriaGrado.addEventListener('change', () => {
      cargarTutores(selectMateriaGrado.value, selectTutorGrado, 'No hay tutores de tu carrera para esta materia');
    });
  }

  document.querySelectorAll('[data-bs-toggle="pill"]').forEach((tab) => {
    tab.addEventListener('shown.bs.tab', () => {
      inputTipo.value = tab.id === 'tab-grado' ? 'grado' : 'apoyo';
      sincronizarFormulario();
    });
  });

  document.getElementById('form-solicitud').addEventListener('submit', (e) => {
    sincronizarFormulario();

    const panel = panelActivo();
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;

    if (!panel.materia || !panel.materia.value) {
      e.preventDefault();
      mostrarToast('Debes elegir una materia.', 'warning');
      return;
    }
    if (!panel.tutor || panel.tutor.disabled || !panel.tutor.value) {
      e.preventDefault();
      mostrarToast('Debes elegir un docente tutor.', 'warning');
      return;
    }
    if (inputTipo.value === 'grado' && (!panel.modalidad || !panel.modalidad.value)) {
      e.preventDefault();
      mostrarToast('Debes elegir la modalidad de graduación.', 'warning');
      return;
    }
    if (!document.getElementById('fecha').value) {
      e.preventDefault();
      mostrarToast('Debes elegir la fecha de la tutoría.', 'warning');
      return;
    }
    if (!horaInicio || !horaFin) {
      e.preventDefault();
      mostrarToast('Debes indicar la hora de inicio y fin.', 'warning');
      return;
    }
  });

  sincronizarFormulario();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>