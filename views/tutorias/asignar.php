<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Asignar Tutoría - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Tutorías', 'url' => '/controllers/tutorias_listar.php'],
    ['texto' => 'Asignar Tutoría', 'actual' => true],
];
$titulo = 'Asignar Tutoría de Graduación';
$descripcion = 'Asigna una tutoría a un estudiante y genera automáticamente la carta de designación para el tutor.';
$icono = 'bi-person-plus-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo asignar la tutoría:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if (!empty($cupoBloqueado)): ?>
  <div class="alert alert-info alert-dismissible fade show rounded-3" role="alert">
    <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($cupoBloqueado) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm">
  <div class="card-body p-4">
    <form method="POST" action="tutorias_asignar.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-people me-1 text-primary"></i>Partes involucradas</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Estudiante *</label>
          <select name="id_estudiante" id="select-estudiante-asignar" class="form-select rounded-3 py-2" required>
            <option value="">Selecciona un estudiante</option>
            <?php foreach ($estudiantes as $est): ?>
              <option value="<?= $est['id_usuario'] ?>" data-carrera="<?= (int) ($est['id_carrera'] ?? 0) ?>" <?= ((int) ($_POST['id_estudiante'] ?? 0) === (int) $est['id_usuario']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($est['nombre'] . ' ' . $est['apellido']) ?> — <?= htmlspecialchars($est['nombre_carrera']) ?> (<?= htmlspecialchars($est['registro_universitario'] ?? '') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Docente Tutor *</label>
          <select name="id_tutor" id="select-tutor-asignar" class="form-select rounded-3 py-2" required>
            <option value="">Selecciona un tutor</option>
            <?php foreach ($tutores as $tutor): ?>
              <option value="<?= $tutor['id_usuario'] ?>" <?= ((int) ($_POST['id_tutor'] ?? 0) === (int) $tutor['id_usuario']) ? 'selected' : '' ?>>
                Prof. <?= htmlspecialchars($tutor['nombre'] . ' ' . $tutor['apellido']) ?> (<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted" id="texto-cupo-tutor">Selecciona el estudiante y el tutor para ver los cupos.</small>
          <div id="bloque-sobrecupo" class="d-none mt-2 p-2 border border-warning-subtle bg-warning bg-opacity-10 rounded-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sobreasignar" id="sobreasignar" value="1">
              <label class="form-check-label small text-warning-emphasis" for="sobreasignar">
                <i class="bi bi-exclamation-triangle me-1"></i>Sobreasignación aprobada por <strong>Decanatura</strong> (cupo agotado)
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Materia *</label>
          <select name="id_materia" class="form-select rounded-3 py-2" required>
            <option value="">Selecciona una materia</option>
            <?php foreach ($materias as $materia): ?>
              <option value="<?= $materia['id_materia'] ?>" <?= ((int) ($_POST['id_materia'] ?? 0) === (int) $materia['id_materia']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($materia['nombre_materia']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <hr class="my-4">
      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-mortarboard me-1 text-primary"></i>Modalidad de graduación *</h5>
      <div class="row g-3">
        <?php foreach ($modalidades as $mod): ?>
          <div class="col-md-4">
            <div class="form-check card h-100 p-3 border rounded-3 <?= ((int) ($_POST['id_modalidad'] ?? 0) === (int) $mod['id_modalidad']) ? 'border-primary' : '' ?>">
              <input class="form-check-input mt-1" type="radio" name="id_modalidad" id="modalidad-<?= $mod['id_modalidad'] ?>" value="<?= $mod['id_modalidad'] ?>" <?= ((int) ($_POST['id_modalidad'] ?? 0) === (int) $mod['id_modalidad']) ? 'checked' : '' ?> required>
              <label class="form-check-label fw-bold text-dark" for="modalidad-<?= $mod['id_modalidad'] ?>">
                <?= htmlspecialchars($mod['nombre']) ?>
              </label>
              <small class="text-muted d-block mt-1"><?= htmlspecialchars($mod['descripcion']) ?></small>
              <small class="text-muted d-block mt-2">
                <i class="bi bi-calendar-week me-1"></i><?= $mod['minimo_reuniones_semana'] ?> reunión(es)/sem
                · <i class="bi bi-file-earmark-text me-1"></i><?= $mod['cantidad_informes'] ?> informes
              </small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <hr class="my-4">
      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-event me-1 text-primary"></i>Agenda</h5>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha *</label>
          <input type="date" name="fecha" class="form-control rounded-3 py-2" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Hora inicio *</label>
          <input type="time" name="hora_inicio" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Hora fin *</label>
          <input type="time" name="hora_fin" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_fin'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Modalidad *</label>
          <select name="modalidad" class="form-select rounded-3 py-2">
            <option value="presencial" <?= (($_POST['modalidad'] ?? 'presencial') === 'presencial') ? 'selected' : '' ?>>Presencial</option>
            <option value="virtual" <?= (($_POST['modalidad'] ?? '') === 'virtual') ? 'selected' : '' ?>>Virtual</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Lugar o enlace (opcional)</label>
          <input type="text" name="lugar_o_enlace" class="form-control rounded-3 py-2" maxlength="200" placeholder="Salón 4 / https://meet.upds.edu.bo/..." value="<?= htmlspecialchars($_POST['lugar_o_enlace'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Observaciones (opcional)</label>
          <input type="text" name="observaciones" class="form-control rounded-3 py-2" maxlength="500" placeholder="Notas para la carta de designación" value="<?= htmlspecialchars($_POST['observaciones'] ?? '') ?>">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2 justify-content-end">
        <a href="tutorias_listar.php" class="btn btn-outline-secondary rounded-3 px-3">Cancelar</a>
        <button type="submit" class="btn btn-primary rounded-3 px-4"><i class="bi bi-file-earmark-check me-1"></i>Asignar y generar carta</button>
      </div>
    </form>
  </div>
</div>

<?php
// Datos para el JS: carrera por estudiante, carreras y cupos por tutor (id_usuario)
$jsEstudiantes = [];
foreach ($estudiantes as $est) {
    $jsEstudiantes[(int) $est['id_usuario']] = (int) ($est['id_carrera'] ?? 0);
}
$jsTutoresCarreras = [];
$jsTutoresCupos = [];
$idTutorPorUsuario = [];
foreach ($tutores as $tutor) {
    $idTutorPorUsuario[(int) $tutor['id_usuario']] = (int) ($tutor['id_tutor'] ?? 0);
    $jsTutoresCarreras[(int) $tutor['id_usuario']] = [];
    $jsTutoresCupos[(int) $tutor['id_usuario']] = ['maximo' => 0, 'disponibles' => 0, 'usados' => 0];
}
foreach ($tutoresDetalle as $idTutor => $detalle) {
    $idUsuario = array_search($idTutor, $idTutorPorUsuario, true);
    if ($idUsuario === false) {
        continue;
    }
    $jsTutoresCarreras[(int) $idUsuario] = array_map('intval', $detalle['carreras'] ?? []);
    $jsTutoresCupos[(int) $idUsuario] = [
        'maximo'      => (int) ($detalle['cupos']['maximo'] ?? 0),
        'usados'      => (int) ($detalle['cupos']['usados'] ?? 0),
        'disponibles' => (int) ($detalle['cupos']['disponibles'] ?? 0),
    ];
}
?>
<script>
  const estudiantesCarrera = <?= json_encode($jsEstudiantes) ?>;
  const tutoresCarreras = <?= json_encode($jsTutoresCarreras) ?>;
  const tutoresCupos = <?= json_encode($jsTutoresCupos) ?>;

  const selectEstudiante = document.getElementById('select-estudiante-asignar');
  const selectTutor = document.getElementById('select-tutor-asignar');
  const textoCupo = document.getElementById('texto-cupo-tutor');
  const bloqueSobrecupo = document.getElementById('bloque-sobrecupo');

  const opcionesOriginales = Array.from(selectTutor.options);

  function aplicarFiltroTutores() {
    bloqueSobrecupo.classList.add('d-none');
    textoCupo.textContent = 'Selecciona el estudiante y el tutor para ver los cupos.';

    const idEstudiante = parseInt(selectEstudiante.value, 10) || 0;
    const idCarrera = estudiantesCarrera[idEstudiante] || 0;

    selectTutor.innerHTML = '';
    opcionesOriginales.forEach((opc) => {
      const idUsuario = parseInt(opc.value, 10) || 0;
      if (idUsuario === 0) {
        selectTutor.appendChild(opc.cloneNode(true));
        return;
      }
      if (!idCarrera) {
        selectTutor.appendChild(opc.cloneNode(true));
        return;
      }
      const carreras = tutoresCarreras[idUsuario] || [];
      if (carreras.includes(idCarrera)) {
        selectTutor.appendChild(opc.cloneNode(true));
      }
    });

    if (selectTutor.options.length <= 1) {
      const vacio = document.createElement('option');
      vacio.value = '';
      vacio.textContent = 'No hay tutores de esta carrera';
      selectTutor.appendChild(vacio);
      selectTutor.disabled = true;
    } else {
      selectTutor.disabled = false;
    }
  }

  function actualizarCupo() {
    bloqueSobrecupo.classList.add('d-none');
    const idEstudiante = parseInt(selectEstudiante.value, 10) || 0;

    if (!idEstudiante) {
      textoCupo.textContent = 'Primero selecciona un estudiante.';
      return;
    }

    const idUsuario = parseInt(selectTutor.value, 10) || 0;
    if (!idUsuario) {
      textoCupo.textContent = 'Selecciona un tutor para ver su cupo.';
      return;
    }

    const cupo = tutoresCupos[idUsuario] || { maximo: 0, disponibles: 0 };
    if (cupo.disponibles > 0) {
      textoCupo.innerHTML = '<i class="bi bi-person-check"></i> Cupos disponibles: <strong>' + cupo.disponibles + ' / ' + cupo.maximo + '</strong>';
    } else {
      textoCupo.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle"></i> Cupo agotado (' + cupo.usados + ' / ' + cupo.maximo + ')</span>';
      bloqueSobrecupo.classList.remove('d-none');
    }
  }

  selectEstudiante.addEventListener('change', aplicarFiltroTutores);
  selectTutor.addEventListener('change', actualizarCupo);

  if (selectEstudiante.value) {
    aplicarFiltroTutores();
    if (selectTutor.value) {
      actualizarCupo();
    }
  }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>