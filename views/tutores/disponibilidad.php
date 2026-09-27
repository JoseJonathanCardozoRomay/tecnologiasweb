<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../models/SolicitudActualizacionModel.php';
$tituloPagina = ($esAdmin ? 'Gestión del Tutor' : 'Mis Horarios y Materias') . ' - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [];
if ($esAdmin) {
    $migas[] = ['texto' => 'Tutores', 'url' => '/controllers/tutores_listar.php'];
    $migas[] = ['texto' => 'Horarios y materias de ' . $tutor['nombre'] . ' ' . $tutor['apellido'], 'actual' => true];
} else {
    $migas[] = ['texto' => 'Mi panel', 'url' => '/views/tutor/panel.php'];
    $migas[] = ['texto' => 'Horarios y materias', 'actual' => true];
}
$titulo = $esAdmin
    ? 'Gestionar Tutor: ' . $tutor['nombre'] . ' ' . $tutor['apellido']
    : 'Mis horas disponibles y materias';
$descripcion = $esAdmin
    ? 'Asigna las carreras, materias y bloques horarios del tutor.'
    : 'Administra tus bloques semanales. Las carreras y materias las asigna únicamente el administrador.';
$icono = 'bi-clock-history';
$accion = $esAdmin
    ? '<a href="/controllers/tutores_listar.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3"><i class="bi bi-arrow-left"></i>Volver</a>'
    : '<a href="/views/tutor/panel.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3"><i class="bi bi-arrow-left"></i>Volver a mi panel</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo guardar:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if (!$esAdmin): ?>
  <div class="alert alert-info d-flex align-items-start gap-3 rounded-3 border-info-subtle mb-4" role="alert">
    <i class="bi bi-shield-lock-fill text-primary fs-4 flex-shrink-0" aria-hidden="true"></i>
    <div>
      <h6 class="alert-heading fw-bold mb-1">Carreras y materias asignadas por el administrador</h6>
      <p class="mb-0 small">
        Estas secciones son de solo lectura para ti. Si necesitas agregar o quitar una materia, una carrera o cambiar tu
        horario, usa <strong>Solicitar actualización</strong>: tu requerimiento queda pendiente de aprobación en el panel del administrador.
      </p>
    </div>
  </div>
<?php endif; ?>

<form method="POST" action="<?= $esAdmin ? "../controllers/tutores_disponibilidad.php?id=$id_tutor" : '../controllers/tutores_disponibilidad.php' ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="accion" value="guardar">

  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between gap-2">
      <h5 class="fw-bold text-dark mb-0">
        <i class="bi bi-mortarboard me-1 text-primary"></i>Carreras en las que imparte
        <?php if (!$puedeAsignar): ?><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle ms-2">Solo administrador</span><?php endif; ?>
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="row g-2">
        <?php foreach ($carreras as $carrera): ?>
          <div class="col-md-4 col-lg-3">
            <label class="form-check materia-check rounded-3 border p-3 w-100 d-block<?= $puedeAsignar ? ' cursor-pointer' : ' opacity-75' ?>">
              <input class="form-check-input me-2" type="checkbox" name="id_carreras[]" value="<?= $carrera['id_carrera'] ?>"
                     <?= in_array($carrera['id_carrera'], $carrerasTutor, true) ? 'checked' : '' ?>
                     <?= $puedeAsignar ? '' : 'disabled' ?>>
              <span class="fw-medium"><?= htmlspecialchars($carrera['nombre_carrera']) ?></span>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="text-muted small mt-3 mb-0">
        <?= $esAdmin
            ? 'Los estudiantes solo verán a este tutor si la carrera seleccionada coincide con la suya.'
            : 'Los estudiantes solo verán a este tutor si la carrera seleccionada coincide con la suya. Para cambiarlas, envía una solicitud.' ?>
      </p>
    </div>
  </div>

  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between gap-2">
      <h5 class="fw-bold text-dark mb-0">
        <i class="bi bi-journal-bookmark me-1 text-primary"></i>Materias que imparte
        <?php if (!$puedeAsignar): ?><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle ms-2">Solo administrador</span><?php endif; ?>
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="row g-2">
        <?php foreach ($materias as $materia): ?>
          <div class="col-md-4 col-lg-3">
            <label class="form-check materia-check rounded-3 border p-3 w-100 d-block<?= $puedeAsignar ? ' cursor-pointer' : ' opacity-75' ?>">
              <input class="form-check-input me-2" type="checkbox" name="id_materias[]" value="<?= $materia['id_materia'] ?>"
                     <?= in_array($materia['id_materia'], $materiasTutor, true) ? 'checked' : '' ?>
                     <?= $puedeAsignar ? '' : 'disabled' ?>>
              <span class="fw-medium"><?= htmlspecialchars($materia['nombre_materia']) ?></span>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="text-muted small mt-3 mb-0">
        <?= $esAdmin
            ? 'Selecciona al menos una materia. Un tutor sin materias asignadas no podrá agendar tutorías.'
            : 'Un tutor sin materias asignadas no podrá agendar tutorías. Para cambiar esta lista, envía una solicitud.' ?>
      </p>
    </div>
  </div>

  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-1 text-primary"></i>Bloques horarios disponibles</h5>
      <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-bloque"><i class="bi bi-plus-lg me-1"></i>Agregar bloque</button>
    </div>
    <div class="card-body p-4">
      <div id="bloques-horarios">
        <?php if (!empty($disponibilidad)): ?>
          <?php foreach ($disponibilidad as $i => $bloque): ?>
            <div class="bloque-horario row g-2 align-items-end mb-3">
              <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Día</label>
                <select name="dia_semana[]" class="form-select rounded-3 py-2">
                  <?php foreach (DIAS_SEMANA as $dia): ?>
                    <option value="<?= htmlspecialchars($dia) ?>" <?= ($bloque['dia_semana'] ?? '') === $dia ? 'selected' : '' ?>><?= htmlspecialchars($dia) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Inicio</label>
                <input type="time" name="hora_inicio[]" class="form-control rounded-3 py-2" value="<?= htmlspecialchars(substr($bloque['hora_inicio'] ?? '', -8, 5)) ?>" required>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Fin</label>
                <input type="time" name="hora_fin[]" class="form-control rounded-3 py-2" value="<?= htmlspecialchars(substr($bloque['hora_fin'] ?? '', -8, 5)) ?>" required>
              </div>
              <div class="col-md-3 d-flex align-items-end gap-2 pb-1">
                <input type="hidden" name="id_disponibilidad[]" value="<?= $bloque['id_disponibilidad'] ?? '' ?>">
                <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-bloque"><i class="bi bi-trash"></i></button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div id="sin-bloques" class="text-center text-muted py-4">
            <i class="bi bi-clock fs-1 d-block mb-2"></i>
            Aún no hay bloques horarios configurados. Agrega los días y horas en los que atenderás.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square me-1 text-primary"></i>Especialidad</h5>
    </div>
    <div class="card-body p-4">
      <input type="text" name="especialidad" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>" maxlength="100" placeholder="Ej: Docencia Universitaria">
      <p class="text-muted small mt-2 mb-0">Podrás modificarla desde aquí en cualquier momento.</p>
    </div>
  </div>

  <div class="d-flex gap-2 justify-content-end mb-4">
    <a href="<?= $esAdmin ? '../controllers/tutores_listar.php' : '/views/tutor/panel.php' ?>" class="btn btn-outline-secondary rounded-3 px-3">Cancelar</a>
    <button type="submit" class="btn btn-primary rounded-3 px-4"><i class="bi bi-check-lg me-1"></i><?= $esAdmin ? 'Guardar cambios' : 'Guardar mis horarios' ?></button>
  </div>
</form>

<?php if (!$esAdmin): ?>
  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="fw-bold text-dark mb-0"><i class="bi bi-send me-1 text-primary"></i>Solicitar actualización de materias u horarios</h5>
    </div>
    <div class="card-body p-4">
      <?php
      $solicitudPendiente = null;
      foreach ($solicitudes as $s) {
          if ($s['estado'] === 'pendiente') {
              $solicitudPendiente = $s;
              break;
          }
      }
      ?>
      <?php if ($solicitudPendiente): ?>
        <div class="alert alert-warning d-flex align-items-start gap-3 rounded-3 mb-0" role="alert">
          <i class="bi bi-hourglass-split text-warning fs-4 flex-shrink-0" aria-hidden="true"></i>
          <div>
            <h6 class="alert-heading fw-bold mb-1">Ya tienes una solicitud en curso</h6>
            <p class="mb-0 small">
              Solicitud #<?= (int) $solicitudPendiente['id_solicitud'] ?> de
              <strong><?= htmlspecialchars(SolicitudActualizacionModel::etiquetaTipo($solicitudPendiente['tipo'])) ?></strong>,
              enviada el <?= date('d/m/Y H:i', strtotime($solicitudPendiente['fecha_solicitud'])) ?>.
              Espera la respuesta del administrador antes de enviar otra.
            </p>
          </div>
        </div>
      <?php else: ?>
        <form method="POST" action="/controllers/tutores_disponibilidad.php" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="accion" value="solicitar">

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase" for="tipoSolicitud">¿Qué necesitas actualizar? *</label>
            <select name="tipo" id="tipoSolicitud" class="form-select rounded-3 py-2" required>
              <option value="materias">Agregar o quitar materias</option>
              <option value="carreras">Agregar o quitar carreras</option>
              <option value="horarios">Ajustar mis bloques horarios</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase" for="detalleSolicitud">Detalle de la solicitud *</label>
            <textarea name="detalle" id="detalleSolicitud" class="form-control rounded-3 py-2" rows="4" maxlength="1000" required
                      placeholder="Ej.: Necesito impartir Bases de Datos (plan 2026) en lugar de Introducción a la Programación."></textarea>
            <div class="form-text">Quedará registrada en el panel del administrador para su aprobación.</div>
          </div>

          <button type="submit" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-send me-1"></i>Enviar solicitud
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php if (!$esAdmin && !empty($solicitudes)): ?>
  <div class="card card-custom shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-1 text-primary"></i>Mis solicitudes</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4">#</th>
            <th>Tipo</th>
            <th>Detalle</th>
            <th>Enviada</th>
            <th>Estado</th>
            <th class="pe-4">Respuesta</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($solicitudes as $s): ?>
            <tr>
              <td class="ps-4 fw-bold text-dark">#<?= (int) $s['id_solicitud'] ?></td>
              <td><span class="badge border px-3 py-1 border-primary-subtle bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars(SolicitudActualizacionModel::etiquetaTipo($s['tipo'])) ?></span></td>
              <td class="text-muted small text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($s['detalle']) ?>"><?= htmlspecialchars($s['detalle']) ?></td>
              <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?></td>
              <td><span class="<?= SolicitudActualizacionModel::claseEstado($s['estado']) ?>"><?= htmlspecialchars(ucfirst($s['estado'])) ?></span></td>
              <td class="pe-4 text-muted small"><?= !empty($s['respuesta']) ? htmlspecialchars($s['respuesta']) : '—' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<template id="plantilla-bloque">
  <div class="bloque-horario row g-2 align-items-end mb-3">
    <div class="col-md-3">
      <label class="form-label fw-semibold text-secondary small text-uppercase">Día</label>
      <select name="dia_semana[]" class="form-select rounded-3 py-2">
        <?php foreach (DIAS_SEMANA as $dia): ?><option value="<?= htmlspecialchars($dia) ?>"><?= htmlspecialchars($dia) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold text-secondary small text-uppercase">Inicio</label>
      <input type="time" name="hora_inicio[]" class="form-control rounded-3 py-2" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold text-secondary small text-uppercase">Fin</label>
      <input type="time" name="hora_fin[]" class="form-control rounded-3 py-2" required>
    </div>
    <div class="col-md-3 d-flex align-items-end gap-2 pb-1">
      <input type="hidden" name="id_disponibilidad[]" value="">
      <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-bloque"><i class="bi bi-trash"></i></button>
    </div>
  </div>
</template>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('bloques-horarios');
    const plantilla = document.getElementById('plantilla-bloque');
    const botonAgregar = document.getElementById('btn-agregar-bloque');

    const actualizarMensajeVacio = () => {
      let vacio = document.getElementById('sin-bloques');
      const tieneFilas = contenedor.querySelectorAll('.bloque-horario').length > 0;
      if (tieneFilas) { vacio?.remove(); return; }
      if (!vacio) {
        vacio = document.createElement('div');
        vacio.id = 'sin-bloques';
        vacio.className = 'text-center text-muted py-4';
        vacio.innerHTML = '<i class="bi bi-clock fs-1 d-block mb-2"></i>Aún no hay bloques horarios configurados.';
        contenedor.appendChild(vacio);
      }
    };

    botonAgregar?.addEventListener('click', () => {
      const fila = plantilla.content.cloneNode(true);
      contenedor.appendChild(fila);
      actualizarMensajeVacio();
    });

    contenedor.addEventListener('click', (e) => {
      const boton = e.target.closest('.btn-quitar-bloque');
      if (boton) { boton.closest('.bloque-horario').remove(); actualizarMensajeVacio(); }
    });
  });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>