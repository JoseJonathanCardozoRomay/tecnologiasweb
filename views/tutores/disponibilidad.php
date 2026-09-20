<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Tutor y Horarios - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-calendar-range text-primary"></i>
      <span>Horarios y Especialidades: Prof. <?= htmlspecialchars($tutor['nombre'] . ' ' . $tutor['apellido']) ?></span>
    </h2>
    <p class="text-muted mb-0">Configura la disponibilidad horaria semanal y las materias que domina este docente.</p>
  </div>
  <a href="<?= ($_SESSION['rol'] === 'tutor') ? '../views/tutor/panel.php' : 'tutores_listar.php' ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
    <i class="bi bi-arrow-left"></i> Volver
  </a>
</div>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger py-2 px-3 rounded-3 mb-4">
    <ul class="mb-0 ps-3 small">
      <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Columna 1: Bloques Horarios Predefinidos -->
  <div class="col-lg-6">
    <div class="card card-custom p-4 h-100">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <span>Bloques Horarios Disponibles</span>
      </h5>
      <p class="text-muted small mb-3">Selecciona los bloques horarios definidos por coordinación que corresponden a tu disponibilidad.</p>

      <!-- Lista de bloques seleccionados -->
      <div class="mb-4">
        <?php if (!empty($bloquesSeleccionados)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($bloquesSeleccionados as $b): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                  <span class="badge bg-primary px-2 py-1 me-2"><?= htmlspecialchars($b['nombre_bloque']) ?></span>
                  <span class="fw-semibold text-dark"><?= substr($b['hora_inicio'], 0, 5) ?> - <?= substr($b['hora_fin'], 0, 5) ?></span>
                  <?php if (!empty($b['descripcion'])): ?>
                    <small class="text-muted d-block"><?= htmlspecialchars($b['descripcion']) ?></small>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="p-3 bg-light rounded-3 text-center text-muted small">
            No has seleccionado bloques horarios. Selecciona uno o más a continuación.
          </div>
        <?php endif; ?>
      </div>

      <!-- Formulario para seleccionar bloques -->
      <div class="p-3 bg-light rounded-3 border">
        <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Seleccionar Bloques Disponibles</h6>
        <?php if (!empty($bloquesDisponibles)): ?>
          <form method="POST">
            <?php echo csrf_campo(); ?>
            <input type="hidden" name="accion" value="guardar_bloques">
            <div class="mb-3" style="max-height: 220px; overflow-y: auto;">
              <?php foreach ($bloquesDisponibles as $bloque): ?>
                <div class="form-check py-1">
                  <input class="form-check-input" type="checkbox" name="bloques[]" value="<?= $bloque['id_bloque'] ?>" id="bloque_<?= $bloque['id_bloque'] ?>"
                    <?= in_array($bloque['id_bloque'], $idsBloquesSeleccionados) ? 'checked' : '' ?>>
                  <label class="form-check-label small fw-medium" for="bloque_<?= $bloque['id_bloque'] ?>">
                    <?= htmlspecialchars($bloque['nombre_bloque']) ?> (<?= substr($bloque['hora_inicio'], 0, 5) ?> - <?= substr($bloque['hora_fin'], 0, 5) ?>)
                    <?php if (!empty($bloque['descripcion'])): ?>
                      <span class="text-muted">— <?= htmlspecialchars($bloque['descripcion']) ?></span>
                    <?php endif; ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="bi bi-check2 me-1"></i> Guardar Selección de Bloques
            </button>
          </form>
        <?php else: ?>
          <div class="text-center text-muted small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            No hay bloques horarios configurados por coordinación. Contacte a su administrador.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Columna 2: Materias que domina y Perfil -->
  <div class="col-lg-6">
    <div class="card card-custom p-4 mb-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-journal-check text-primary"></i>
        <span>Materias que Imparte</span>
      </h5>
      <form method="POST">
        <?php echo csrf_campo(); ?>
        <input type="hidden" name="accion" value="guardar_materias">
        <div class="mb-3" style="max-height: 220px; overflow-y: auto;">
          <?php foreach ($todasMaterias as $mat): ?>
            <div class="form-check py-1">
              <input class="form-check-input" type="checkbox" name="materias[]" value="<?= $mat['id_materia'] ?>" id="mat_<?= $mat['id_materia'] ?>"
                <?= in_array($mat['id_materia'], $idsMateriasAsignadas) ? 'checked' : '' ?>>
              <label class="form-check-label small fw-medium" for="mat_<?= $mat['id_materia'] ?>">
                <?= htmlspecialchars($mat['nombre_materia']) ?>
                <span class="text-muted">(<?= htmlspecialchars($mat['nombre_carrera'] ?? 'General') ?>)</span>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
          <i class="bi bi-save me-1"></i> Guardar Materias Asignadas
        </button>
      </form>
    </div>

    <!-- Perfil docente -->
    <div class="card card-custom p-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <span>Perfil Profesional</span>
      </h5>
      <form method="POST">
        <?php echo csrf_campo(); ?>
        <input type="hidden" name="accion" value="actualizar_perfil">
        <div class="mb-3">
          <label class="form-label small text-muted">Especialidad</label>
          <input type="text" name="especialidad" class="form-control form-control-sm" value="<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>" placeholder="Ej: Bases de Datos, Redes...">
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">Biografía / Presentación</label>
          <textarea name="biografia" class="form-control form-control-sm" rows="2" placeholder="Breve descripción profesional..."><?= htmlspecialchars($tutor['biografia'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">Foto de Perfil</label>
          <div class="profile-photo-container mb-2">
            <div class="profile-photo-wrapper" style="width: 80px; height: 80px;" onclick="document.getElementById('edit-profile-photo-input').click()">
              <?php if (!empty($tutor['foto_perfil'])): ?>
                <img src="<?= htmlspecialchars($tutor['foto_perfil']) ?>" alt="Foto de perfil">
              <?php else: ?>
                <div class="profile-photo-placeholder" style="font-size: 1.5rem;">
                  <?= strtoupper(substr($tutor['nombre'], 0, 1) . substr($tutor['apellido'], 0, 1)) ?>
                </div>
              <?php endif; ?>
              <div class="profile-photo-overlay">
                <i class="bi bi-camera"></i>
              </div>
            </div>
            <input type="file" id="edit-profile-photo-input" class="profile-photo-input" accept="image/*" onchange="handleEditProfilePhoto(this)">
            <input type="hidden" name="foto_perfil" id="edit-foto_perfil_hidden" value="<?= htmlspecialchars($tutor['foto_perfil'] ?? '') ?>">
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('edit-profile-photo-input').click()">
              <i class="bi bi-upload me-1"></i> Subir foto
            </button>
            <?php if (!empty($tutor['foto_perfil'])): ?>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEditProfilePhoto()">
                <i class="bi bi-trash me-1"></i> Eliminar
              </button>
            <?php endif; ?>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">LinkedIn / Perfil Profesional</label>
          <input type="text" name="perfil_linkedin" class="form-control form-control-sm" value="<?= htmlspecialchars($tutor['perfil_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/tu-perfil">
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">Certificaciones / Títulos</label>
          <textarea name="certificaciones" class="form-control form-control-sm" rows="2" placeholder="Ej: PhD en Ciencias de la Computación, Certificación AWS..."><?= htmlspecialchars($tutor['certificaciones'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">Áreas de Expertise (separadas por comas)</label>
          <input type="text" name="areas_expertise" class="form-control form-control-sm" value="<?= htmlspecialchars($tutor['areas_expertise'] ?? '') ?>" placeholder="Ej: Machine Learning, Bases de Datos, Programación Web">
        </div>
        <button type="submit" class="btn btn-light btn-sm w-100 border">
          <i class="bi bi-check2 me-1"></i> Actualizar Perfil Profesional
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function handleEditProfilePhoto(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    
    if (!file.type.startsWith('image/')) {
      alert('Por favor selecciona un archivo de imagen válido.');
      input.value = '';
      return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
      alert('La imagen no debe superar los 5MB.');
      input.value = '';
      return;
    }
    
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
      
      document.getElementById('edit-foto_perfil_hidden').value = e.target.result;
      
      const removeBtn = document.querySelector('.btn-outline-danger');
      if (!removeBtn) {
        const btnContainer = document.querySelector('.d-flex.gap-2');
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-sm btn-outline-danger';
        removeButton.innerHTML = '<i class="bi bi-trash me-1"></i> Eliminar';
        removeButton.onclick = removeEditProfilePhoto;
        btnContainer.appendChild(removeButton);
      }
    };
    reader.readAsDataURL(file);
  }
}

function removeEditProfilePhoto() {
  if (confirm('¿Estás seguro de que deseas eliminar tu foto de perfil?')) {
    const wrapper = document.querySelector('.profile-photo-wrapper');
    const existingImg = wrapper.querySelector('img');
    
    if (existingImg) {
      existingImg.remove();
      const placeholder = document.createElement('div');
      placeholder.className = 'profile-photo-placeholder';
      placeholder.style.fontSize = '1.5rem';
      placeholder.textContent = '<?= strtoupper(substr($tutor['nombre'], 0, 1) . substr($tutor['apellido'], 0, 1)) ?>';
      wrapper.appendChild(placeholder);
    }
    
    document.getElementById('edit-foto_perfil_hidden').value = '';
    document.getElementById('edit-profile-photo-input').value = '';
    
    const removeBtn = document.querySelector('.btn-outline-danger');
    if (removeBtn) {
      removeBtn.remove();
    }
  }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
