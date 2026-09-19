<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Solicitar Tutoría Académica - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-calendar-plus-fill text-primary"></i>
        <span>Agendar Sesión de Tutoría</span>
      </h3>
      <a href="<?= ($_SESSION['rol'] === 'estudiante') ? '../views/estudiante/panel.php' : 'tutorias_listar.php' ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Corrige los siguientes datos:</div>
        <ul class="mb-0 ps-3 small">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
        <div class="row g-3">
          <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?>
            <div class="col-md-12">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Estudiante *</label>
              <select name="id_estudiante" class="form-select rounded-3 py-2" required>
                <option value="" disabled <?= empty($_POST['id_estudiante']) ? 'selected' : '' ?>>Selecciona al estudiante...</option>
                <?php foreach ($estudiantes as $estudianteItem): ?>
                  <option value="<?= $estudianteItem['id_estudiante'] ?>" <?= (($_POST['id_estudiante'] ?? '') == $estudianteItem['id_estudiante']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($estudianteItem['nombre'] . ' ' . $estudianteItem['apellido']) ?> — <?= htmlspecialchars($estudianteItem['registro_universitario']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
          <!-- Materia -->
          <div class="col-md-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Materia Académica *</label>
            <select name="id_materia" id="id_materia" class="form-select rounded-3 py-2" required>
              <option value="" disabled selected>Selecciona la materia que deseas reforzar...</option>
              <?php foreach ($materias as $m): ?>
                <option value="<?= htmlspecialchars($m['id_materia']) ?>" <?= (isset($_POST['id_materia']) && $_POST['id_materia'] == $m['id_materia']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['nombre_materia']) ?> (<?= htmlspecialchars($m['nombre_carrera'] ?? 'General') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Periodo académico: definido por coordinación; el estudiante solo lo VE -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Periodo Académico</label>
            <input type="text" class="form-control rounded-3 py-2 bg-light" value="<?= $periodoActual ? htmlspecialchars($periodoActual['nombre'] . ' (' . $periodoActual['codigo'] . ')') : 'Sin periodo activo' ?>" readonly disabled>
            <div class="form-text mt-1">
              <?php if ($periodoActual): ?>
                <i class="bi bi-calendar-range me-1"></i>Fechas habilitadas: <?= date('d/m/Y', strtotime($periodoActual['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($periodoActual['fecha_fin'])) ?>
              <?php else: ?>
                <span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>No hay un periodo activo. Contacta a coordinación.</span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Tutor -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Docente Tutor *</label>
            <select name="id_tutor" id="id_tutor" data-seleccionado="<?= htmlspecialchars($_POST['id_tutor'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-select rounded-3 py-2" required>
              <option value="" disabled selected>Selecciona al tutor académico...</option>
              <?php foreach ($tutores as $t): ?>
                <option value="<?= $t['id_tutor'] ?>" <?= (isset($_POST['id_tutor']) && $_POST['id_tutor'] == $t['id_tutor']) ? 'selected' : '' ?>>
                  Prof. <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?> <?= !empty($t['especialidad']) ? '— ' . htmlspecialchars($t['especialidad']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div id="horarios-tutor" class="form-text mt-1" aria-live="polite"></div>
          </div>

          <!-- Fecha: solo dentro del rango del periodo activo (min/max dinámicos) -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha de la Sesión *</label>
            <input type="date" name="fecha" id="fecha" class="form-control rounded-3 py-2"
                   min="<?= htmlspecialchars($fechaMin) ?>" max="<?= htmlspecialchars($fechaMax) ?>"
                   value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" required>
            <div id="nombre-dia" class="form-text mt-1" aria-live="polite"></div>
          </div>

          <!-- Bloque horario: lo define el admin; el estudiante solo elige el bloque -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Bloque Horario *</label>
            <select name="id_bloque" id="id_bloque" class="form-select rounded-3 py-2" required>
              <option value="" disabled <?= empty($_POST['id_bloque']) ? 'selected' : '' ?>>Selecciona un bloque...</option>
              <?php foreach ($bloques as $b): ?>
                <option value="<?= (int) $b['id_bloque'] ?>" data-inicio="<?= substr($b['hora_inicio'], 0, 5) ?>" data-fin="<?= substr($b['hora_fin'], 0, 5) ?>"
                  <?= (isset($_POST['id_bloque']) && (int) $_POST['id_bloque'] === (int) $b['id_bloque']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['nombre_bloque']) ?><?= !empty($b['descripcion']) ? ' — ' . htmlspecialchars($b['descripcion']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div id="rango-bloque" class="form-text mt-1" aria-live="polite"></div>
          </div>

          <!-- Modalidad: definida por admin/tutor; el estudiante NO la modifica -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Modalidad</label>
            <input type="text" class="form-control rounded-3 py-2 bg-light" value="Presencial" readonly disabled>
            <input type="hidden" name="modalidad_display" value="presencial">
            <div class="form-text mt-1"><i class="bi bi-lock-fill me-1"></i>Definida por coordinación/tutor.</div>
          </div>

          <!-- Lugar o Enlace -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Lugar o Enlace</label>
            <input type="text" name="lugar_o_enlace" class="form-control rounded-3 py-2" placeholder="Ej: Aula 204 o https://meet.google.com/..." value="<?= htmlspecialchars($_POST['lugar_o_enlace'] ?? '') ?>">
          </div>

          <!-- Observaciones / Temas -->
          <div class="col-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Temas o Preguntas a Tratar</label>
            <textarea name="observaciones" class="form-control rounded-3" rows="3" placeholder="Describe brevemente las dudas o temas puntuales que necesitas repasar con el tutor..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
          <a href="<?= ($_SESSION['rol'] === 'estudiante') ? '../views/estudiante/panel.php' : 'tutorias_listar.php' ?>" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-send-fill"></i>
            <span>Confirmar y Enviar Solicitud</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(() => {
  const materia = document.getElementById('id_materia');
  const tutor = document.getElementById('id_tutor');
  const horarios = document.getElementById('horarios-tutor');
  const fecha = document.getElementById('fecha');
  const nombreDia = document.getElementById('nombre-dia');
  const bloque = document.getElementById('id_bloque');
  const rangoBloque = document.getElementById('rango-bloque');
  let tutores = [];
  const dias = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
  const fechaMin = fecha ? fecha.getAttribute('min') : '';
  const fechaMax = fecha ? fecha.getAttribute('max') : '';
  const actualizarDia = () => { if (!fecha.value) return nombreDia.textContent = ''; const d = new Date(fecha.value + 'T12:00:00'); const dia = dias[d.getDay()]; nombreDia.textContent = 'Día: ' + dia; nombreDia.className = 'form-text mt-1' + (dia === 'Domingo' ? ' text-danger' : ''); };
  // Muestra el rango de horas del bloque seleccionado (las horas las define el admin).
  const mostrarBloque = () => {
    const opcion = bloque.options[bloque.selectedIndex];
    rangoBloque.textContent = (opcion && opcion.dataset.inicio)
      ? 'Horario: ' + opcion.dataset.inicio + ' - ' + opcion.dataset.fin
      : '';
  };
  // Impide elegir una fecha fuera del rango del periodo activo.
  const validarFechaRango = () => {
    if (!fecha.value) return;
    if ((fechaMin && fecha.value < fechaMin) || (fechaMax && fecha.value > fechaMax)) {
      alert('La fecha debe estar dentro del periodo activo: ' + fechaMin + ' al ' + fechaMax + '.');
      fecha.value = '';
      nombreDia.textContent = '';
    }
  };
  const mostrarHorarios = () => { const actual = tutores.find(t => String(t.id_tutor) === tutor.value); horarios.textContent = actual ? (actual.horarios || []).map(h => h.dia_semana + ' ' + String(h.hora_inicio).slice(0, 5) + '-' + String(h.hora_fin).slice(0, 5)).join(' · ') || 'Sin horarios registrados.' : ''; };
  const cargar = async () => {
    tutor.replaceChildren(); horarios.textContent = '';
    const inicial = document.createElement('option'); inicial.value = ''; inicial.textContent = 'Selecciona al tutor académico...'; inicial.disabled = true; inicial.selected = true; tutor.appendChild(inicial);
    if (!materia.value) return;
    try {
      const respuesta = await fetch('/controllers/api_tutores_por_materia.php?id_materia=' + encodeURIComponent(materia.value));
      if (!respuesta.ok) throw new Error();
      tutores = await respuesta.json();
      const seleccionado = tutor.dataset.seleccionado;
      tutores.forEach(t => { const o = document.createElement('option'); o.value = t.id_tutor; o.textContent = 'Prof. ' + t.nombre + ' ' + t.apellido + (t.especialidad ? ' — ' + t.especialidad : ''); o.selected = String(t.id_tutor) === seleccionado; tutor.appendChild(o); });
      mostrarHorarios();
    } catch (_) { horarios.textContent = 'No se pudieron cargar los tutores disponibles.'; }
  };
  materia.addEventListener('change', () => { tutor.dataset.seleccionado = ''; cargar(); });
  tutor.addEventListener('change', mostrarHorarios);
  fecha.addEventListener('change', () => { validarFechaRango(); actualizarDia(); });
  bloque.addEventListener('change', mostrarBloque);
  actualizarDia(); mostrarBloque(); if (materia.value) cargar();
})();
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
