<?php
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['tutor']);
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/TutorModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../models/BloqueModel.php';
require_once __DIR__ . '/../../includes/lista_helper.php';

$tutorModel = new TutorModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);
$bloqueModel = new BloqueModel($pdo);

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$tutor = $tutorModel->obtenerPorUsuario($idUsuario);

if (!$tutor) {
    // Si no tiene registro en tutores, lo creamos automáticamente
    $pdo->prepare("INSERT INTO tutores (id_usuario, especialidad) VALUES (?, 'Docente UPDS')")->execute([$idUsuario]);
    $tutor = $tutorModel->obtenerPorUsuario($idUsuario);
}

$idTutor = $tutor['id_tutor'];
$totalRegistros = $tutoriaModel->contarPorTutor($idTutor);
$pag = paginacionCalcular($totalRegistros, paginacionParametros(10));
$misTutorias = $tutoriaModel->obtenerPorTutorPaginadas($idTutor, $pag['por_pagina'], $pag['offset']);
$metricasTutor = $tutoriaModel->obtenerMetricasPorTutor($idTutor);
$misMaterias = $tutorModel->obtenerMaterias($idTutor);
$misBloques = $tutorModel->obtenerBloquesSeleccionados($idTutor);

$pendientes = (int) ($metricasTutor['pendientes'] ?? 0);
$confirmadas = (int) ($metricasTutor['confirmadas'] ?? 0);
$realizadas = (int) ($metricasTutor['realizadas'] ?? 0);
$enProceso = (int) ($metricasTutor['en_proceso'] ?? 0);
$detenidas = (int) ($metricasTutor['detenido'] ?? 0);

$tituloPagina = 'Panel del Docente Tutor - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 text-white shadow" style="background: linear-gradient(135deg, var(--upds-navy) 0%, var(--upds-accent) 100%) !important;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h2 class="fw-bold mb-1">Bienvenido, Prof. <?= htmlspecialchars($_SESSION['nombre']) ?></h2>
          <p class="mb-0 text-white-50">Portal Docente de Tutorías Académicas &bull; <?= htmlspecialchars($tutor['especialidad'] ?? 'Docencia') ?></p>
        </div>
        <div class="d-flex gap-2">
          <a href="/controllers/tutores_disponibilidad.php?id=<?= $idTutor ?>" class="btn btn-light text-primary fw-semibold d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-clock-history"></i>
            <span>Mis Horarios y Materias</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Métricas en vivo -->
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center kpi-card kpi-warning">
      <div class="kpi-icon"><i class="bi bi-clock"></i></div>
      <div class="kpi-value"><?= $pendientes ?></div>
      <div class="kpi-label">Solicitudes Pendientes</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center kpi-card kpi-info">
      <div class="kpi-icon"><i class="bi bi-calendar-check"></i></div>
      <div class="kpi-value"><?= $confirmadas ?></div>
      <div class="kpi-label">Sesiones Confirmadas</div>
    </div>
  </div>
  <div class="col-md-4">
     <div class="card card-custom p-4 text-center kpi-card kpi-success">
       <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
       <div class="kpi-value"><?= $realizadas ?></div>
       <div class="kpi-label">Tutorías Realizadas</div>
     </div>
  </div>
  <div class="col-md-6">
     <div class="card card-custom p-4 text-center kpi-card kpi-academic">
       <div class="kpi-icon"><i class="bi bi-arrow-repeat"></i></div>
       <div class="kpi-value"><?= $enProceso ?></div>
       <div class="kpi-label">Sesiones En Proceso</div>
     </div>
  </div>
  <div class="col-md-6">
     <div class="card card-custom p-4 text-center kpi-card kpi-inactive">
       <div class="kpi-icon"><i class="bi bi-pause-circle"></i></div>
       <div class="kpi-value"><?= $detenidas ?></div>
       <div class="kpi-label">Sesiones Detenidas</div>
     </div>
  </div>

  <!-- Perfil Profesional -->
  <div class="col-12">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-person-lines-fill text-primary"></i>
          <span>Perfil Profesional</span>
        </h5>
        <a href="/controllers/tutores_disponibilidad.php?id=<?= $idTutor ?>" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-pencil me-1"></i> Editar Perfil
        </a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 text-center">
            <div class="profile-photo-container">
              <div class="profile-photo-wrapper" onclick="document.getElementById('profile-photo-input').click()">
                <?php if (!empty($tutor['foto_perfil'])): ?>
                  <img src="<?= htmlspecialchars($tutor['foto_perfil']) ?>" alt="Foto de perfil">
                <?php else: ?>
                  <div class="profile-photo-placeholder">
                    <?= strtoupper(substr($tutor['nombre'], 0, 1) . substr($tutor['apellido'], 0, 1)) ?>
                  </div>
                <?php endif; ?>
                <div class="profile-photo-overlay">
                  <i class="bi bi-camera me-1"></i> Cambiar foto
                </div>
              </div>
              <input type="file" id="profile-photo-input" class="profile-photo-input" accept="image/*" onchange="handleProfilePhoto(this)">
              <input type="hidden" name="foto_perfil" id="foto_perfil_hidden" value="<?= htmlspecialchars($tutor['foto_perfil'] ?? '') ?>">
              <div class="d-flex gap-2">
                <button type="button" class="profile-photo-button" onclick="document.getElementById('profile-photo-input').click()">
                  <i class="bi bi-upload"></i> Subir foto
                </button>
                <?php if (!empty($tutor['foto_perfil'])): ?>
                  <button type="button" class="profile-photo-button profile-photo-remove" onclick="removeProfilePhoto()">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                <?php endif; ?>
              </div>
            </div>
            <h6 class="fw-bold mb-0 mt-3"><?= htmlspecialchars($tutor['nombre'] . ' ' . $tutor['apellido']) ?></h6>
            <small class="text-muted"><?= htmlspecialchars($tutor['correo']) ?></small>
          </div>
          <div class="col-md-9">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="small text-muted text-uppercase fw-semibold">Especialidad</label>
                <div class="fw-medium"><?= htmlspecialchars($tutor['especialidad'] ?? 'No especificada') ?></div>
              </div>
              <?php if (!empty($tutor['perfil_linkedin'])): ?>
                <div class="col-md-6 mb-3">
                  <label class="small text-muted text-uppercase fw-semibold">LinkedIn</label>
                  <div>
                    <a href="<?= htmlspecialchars($tutor['perfil_linkedin']) ?>" target="_blank" class="text-primary text-decoration-none">
                      <i class="bi bi-linkedin me-1"></i> Ver perfil
                    </a>
                  </div>
                </div>
              <?php endif; ?>
              <?php if (!empty($tutor['biografia'])): ?>
                <div class="col-12 mb-3">
                  <label class="small text-muted text-uppercase fw-semibold">Biografía</label>
                  <div class="text-muted"><?= nl2br(htmlspecialchars($tutor['biografia'])) ?></div>
                </div>
              <?php endif; ?>
              <?php if (!empty($tutor['certificaciones'])): ?>
                <div class="col-12 mb-3">
                  <label class="small text-muted text-uppercase fw-semibold">Certificaciones / Títulos</label>
                  <div class="text-muted"><?= nl2br(htmlspecialchars($tutor['certificaciones'])) ?></div>
                </div>
              <?php endif; ?>
              <?php if (!empty($tutor['areas_expertise'])): ?>
                <div class="col-12 mb-3">
                  <label class="small text-muted text-uppercase fw-semibold">Áreas de Expertise</label>
                  <div>
                    <?php foreach (explode(',', $tutor['areas_expertise']) as $area): ?>
                      <span class="badge bg-light text-dark border me-1 mb-1"><?= htmlspecialchars(trim($area)) ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bloques Horarios Seleccionados -->
  <div class="col-12">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-clock-history text-primary"></i>
          <span>Mis Bloques Horarios</span>
        </h5>
        <a href="/controllers/tutores_disponibilidad.php?id=<?= $idTutor ?>" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-pencil me-1"></i> Modificar Selección
        </a>
      </div>
      <div class="card-body">
        <?php if (!empty($misBloques)): ?>
          <div class="row">
            <?php foreach ($misBloques as $bloque): ?>
              <div class="col-md-3 mb-3">
                <div class="p-3 bg-light rounded-3 border">
                  <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($bloque['nombre_bloque']) ?></div>
                  <div class="small text-muted"><?= substr($bloque['hora_inicio'], 0, 5) ?> - <?= substr($bloque['hora_fin'], 0, 5) ?></div>
                  <?php if (!empty($bloque['descripcion'])): ?>
                    <div class="small text-muted mt-1"><?= htmlspecialchars($bloque['descripcion']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center text-muted py-3">
            <i class="bi bi-clock fs-1 d-block mb-2 text-secondary"></i>
            No has seleccionado bloques horarios. 
            <a href="/controllers/tutores_disponibilidad.php?id=<?= $idTutor ?>" class="text-primary">Seleccionar bloques</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

   <!-- Listado de tutorías asignadas -->
  <div class="col-12">
    <div class="card card-custom shadow-sm overflow-hidden">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-calendar-week text-primary"></i>
          <span>Mis Sesiones de Tutoría</span>
        </h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Fecha y Horario</th>
              <th>Materia</th>
              <th>Estudiante</th>
              <th>Modalidad</th>
              <th>Estado</th>
              <th>Seguimiento</th>
              <th class="text-end pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($misTutorias as $t): ?>
              <?php
                $badgeEstado = 'status-pending';
                if ($t['estado'] === 'confirmada') $badgeEstado = 'status-confirmed';
                if ($t['estado'] === 'realizada') $badgeEstado = 'status-completed';
                if ($t['estado'] === 'cancelada') $badgeEstado = 'status-cancelled';
                if ($t['estado'] === 'en_proceso') $badgeEstado = 'status-pending';
                if ($t['estado'] === 'detenido') $badgeEstado = 'status-inactive';
                $etiquetaEstado = [
                    'pendiente'  => 'Pendiente',
                    'confirmada' => 'Confirmada',
                    'realizada'  => 'Realizada',
                    'cancelada'  => 'Cancelada',
                    'en_proceso' => 'En Proceso',
                    'detenido'   => 'Detenido',
                ][$t['estado']] ?? ucfirst($t['estado']);
                $iconoEstado = [
                    'en_proceso' => '<i class="bi bi-arrow-repeat me-1"></i>',
                    'detenido'   => '<i class="bi bi-pause-fill me-1"></i>',
                ][$t['estado']] ?? '';
              ?>
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                  <small class="text-muted"><?= substr($t['hora_inicio'], 0, 5) ?> - <?= substr($t['hora_fin'], 0, 5) ?></small>
                </td>
                <td>
                  <div class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></div>
                  <?php if (!empty($t['observaciones'])): ?>
                    <small class="text-muted text-truncate d-block" style="max-width: 200px;" title="<?= htmlspecialchars($t['observaciones']) ?>">
                      Obs: <?= htmlspecialchars($t['observaciones']) ?>
                    </small>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-medium text-dark"><?= htmlspecialchars($t['est_nombre'] . ' ' . $t['est_apellido']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($t['est_correo']) ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <?= ucfirst($t['modalidad']) ?>
                  </span>
                  <?php if (!empty($t['lugar_o_enlace'])): ?>
                    <div class="small text-muted text-truncate" style="max-width: 140px;"><?= htmlspecialchars($t['lugar_o_enlace']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge rounded-pill px-3 py-1 <?= $badgeEstado ?>">
                    <?= $iconoEstado ?><?= $etiquetaEstado ?>
                  </span>
                  <?php if (!empty($t['calificacion'])): ?>
                    <div class="text-warning small mt-1">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= $t['calificacion'] ? '-fill' : '' ?>"></i>
                      <?php endfor; ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($t['asistio'])): ?>
                    <span class="badge rounded-pill px-3 py-1 <?= $t['asistio'] === 'si' ? 'bg-success text-white' : 'bg-secondary text-white' ?>">
                      <i class="bi <?= $t['asistio'] === 'si' ? 'bi-check2' : 'bi-x' ?> me-1"></i><?= $t['asistio'] === 'si' ? 'Asistió' : 'No asistió' ?>
                    </span>
                    <?php if (!empty($t['avance'])): ?>
                      <div class="small text-muted mt-1">Avance: <?= htmlspecialchars(str_replace('_', ' ', $t['avance'])) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($t['temas_tratados'])): ?>
                      <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($t['temas_tratados']) ?>"><?= htmlspecialchars($t['temas_tratados']) ?></div>
                    <?php endif; ?>
                  <?php elseif ($t['estado'] === 'realizada'): ?>
                    <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#modalSeguimiento_<?= $t['id_tutoria'] ?>">
                      <i class="bi bi-clipboard-plus"></i> Registrar seguimiento
                    </button>

                    <div class="modal fade text-start" id="modalSeguimiento_<?= $t['id_tutoria'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                          <form action="/controllers/tutorias_seguimiento.php" method="POST">
                            <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
                            <input type="hidden" name="id_tutoria" value="<?= $t['id_tutoria'] ?>">
                            <div class="modal-header border-0 pb-0">
                              <h5 class="modal-title fw-bold">Seguimiento de <?= htmlspecialchars($t['nombre_materia']) ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-secondary">Asistencia *</label>
                                <select name="asistio" class="form-select" required>
                                  <option value="si">Sí asistió</option>
                                  <option value="no">No asistió</option>
                                </select>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-secondary">Temas tratados *</label>
                                <textarea name="temas_tratados" class="form-control" rows="3" minlength="10" maxlength="1000" placeholder="Describe los temas tratados en la sesión"></textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-secondary">Avance *</label>
                                <select name="avance" class="form-select">
                                  <option value="">Selecciona el avance</option>
                                  <option value="sin_avance">Sin avance</option>
                                  <option value="parcial">Parcial</option>
                                  <option value="logrado">Logrado</option>
                                </select>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-secondary">Recomendaciones</label>
                                <textarea name="recomendaciones" class="form-control" rows="3" maxlength="1000" placeholder="Sugerencias para el estudiante (opcional)"></textarea>
                              </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                              <button type="submit" class="btn btn-success">Guardar seguimiento</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php elseif ($t['estado'] === 'cancelada' && !empty($t['motivo_cancelacion'])): ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border-danger-subtle"><i class="bi bi-info-circle me-1"></i>Motivo</span>
                    <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($t['motivo_cancelacion']) ?>"><?= htmlspecialchars($t['motivo_cancelacion']) ?></div>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group" role="group">
                    <?php if ($t['estado'] === 'pendiente'): ?>
                       <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=confirmada" 
                         class="btn btn-sm btn-success d-flex align-items-center gap-1" onclick="enviarPostSeguro(this.href); return false;" title="Aceptar y confirmar">
                        <i class="bi bi-check-circle"></i> Aceptar
                      </a>
                       <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" 
                         class="btn btn-sm btn-outline-danger" onclick="confirmarCancelacion(this.href); return false;" title="Rechazar">
                        <i class="bi bi-x-circle"></i>
                      </a>
                    <?php elseif ($t['estado'] === 'confirmada'): ?>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=en_proceso"
                         class="btn btn-sm btn-warning text-dark d-flex align-items-center gap-1" onclick="confirmarTransicion(this.href, '¿Iniciar esta sesión? Esta acción registra el inicio de la tutoría.'); return false;" title="Iniciar la sesión">
                        <i class="bi bi-play-circle"></i> Iniciar Sesión
                      </a>
                    <?php elseif ($t['estado'] === 'en_proceso'): ?>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=realizada"
                         class="btn btn-sm btn-success d-flex align-items-center gap-1" onclick="confirmarTransicion(this.href, '¿Finalizar esta sesión como realizada?'); return false;" title="Finalizar con éxito">
                        <i class="bi bi-check2-all"></i> Finalizar con Éxito
                      </a>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=detenido"
                         class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="confirmarTransicion(this.href, '¿Detener esta sesión? El estudiante será notificado.', 'warning'); return false;" title="Detener la sesión">
                        <i class="bi bi-pause-circle"></i> Detener Sesión
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($misTutorias)): ?>
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-calendar-check fs-1 d-block mb-2 text-secondary"></i>
                  No tiene solicitudes de tutorías asignadas en este momento.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <?php $mostrarSelector = false; include __DIR__ . '/../partials/paginacion.php'; ?>
    </div>
  </div>
</div>

<script>
function handleProfilePhoto(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    
    // Validar que sea una imagen
    if (!file.type.startsWith('image/')) {
      alert('Por favor selecciona un archivo de imagen válido.');
      input.value = '';
      return;
    }
    
    // Validar tamaño (máximo 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('La imagen no debe superar los 2MB.');
      input.value = '';
      return;
    }
    
    // Previsualizar imagen antes de subir
    const reader = new FileReader();
    reader.onload = function(e) {
      const wrapper = document.querySelector('.profile-photo-wrapper');
      const existingImg = wrapper.querySelector('img');
      const existingPlaceholder = wrapper.querySelector('.profile-photo-placeholder');
      
      if (existingImg) {
        existingImg.src = e.target.result;
      } else if (existingPlaceholder) {
        existingPlaceholder.remove();
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = 'Foto de perfil';
        wrapper.appendChild(img);
      }
    };
    reader.readAsDataURL(file);
    
    // Subir archivo al servidor
    const formData = new FormData();
    formData.append('foto_file', file);
    
    fetch('/controllers/tutor_foto_subir.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.ok) {
        document.getElementById('foto_perfil_hidden').value = data.ruta;
        
        // Mostrar botón de eliminar
        const removeBtn = document.querySelector('.profile-photo-remove');
        if (!removeBtn) {
          const btnContainer = document.querySelector('.profile-photo-container .d-flex');
          const removeButton = document.createElement('button');
          removeButton.type = 'button';
          removeButton.className = 'profile-photo-button profile-photo-remove';
          removeButton.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
          removeButton.onclick = removeProfilePhoto;
          btnContainer.appendChild(removeButton);
        }
      } else {
        alert('Error al subir la foto: ' + data.error);
        input.value = '';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al subir la foto. Por favor intenta nuevamente.');
      input.value = '';
    });
  }
}

function removeProfilePhoto() {
  if (confirm('¿Estás seguro de que deseas eliminar tu foto de perfil?')) {
    const wrapper = document.querySelector('.profile-photo-wrapper');
    const existingImg = wrapper.querySelector('img');
    
    if (existingImg) {
      existingImg.remove();
      const placeholder = document.createElement('div');
      placeholder.className = 'profile-photo-placeholder';
      placeholder.textContent = '<?= strtoupper(substr($tutor['nombre'], 0, 1) . substr($tutor['apellido'], 0, 1)) ?>';
      wrapper.appendChild(placeholder);
    }
    
    document.getElementById('foto_perfil_hidden').value = '';
    document.getElementById('profile-photo-input').value = '';
    
    // Eliminar botón de eliminar
    const removeBtn = document.querySelector('.profile-photo-remove');
    if (removeBtn) {
      removeBtn.remove();
    }
  }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
