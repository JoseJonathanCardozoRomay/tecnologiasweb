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

    <!-- Perfil docente PROFESIONAL -->
    <div class="card card-custom p-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <span>Perfil Profesional</span>
      </h5>
      <form method="POST" enctype="multipart/form-data">
        <?php echo csrf_campo(); ?>
        <input type="hidden" name="accion" value="actualizar_perfil_completo">

        <!-- Avatar + foto -->
        <div class="mb-4">
          <div class="d-flex justify-content-center mb-3">
            <div class="avatar-tutor-container" style="position: relative;">
              <?php if (!empty($tutor['foto_perfil'])): ?>
                <img src="<?= htmlspecialchars($tutor['foto_perfil']) ?>" 
                     alt="Foto de perfil" 
                     class="avatar-img rounded-circle" 
                     style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--upds-navy);">
              <?php else: ?>
                <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--upds-navy) 0%, var(--upds-accent-dark) 100%); color: white; font-size: 2rem; font-weight: 700;">
                  <?= strtoupper(substr($tutor['nombre'] ?? 'T', 0, 1) . substr($tutor['apellido'] ?? '', 0, 1)) ?>
                </div>
              <?php endif; ?>
              <button type="button" class="btn-change-photo" 
                      style="position: absolute; bottom: -4px; right: -4px; width: 36px; height: 36px; border-radius: 50%; background: var(--upds-navy); color: white; border: 2px solid white; display: flex; align-items-center; justify-content: center;"
                      title="Cambiar foto de perfil">
                <i class="bi bi-camera-fill"></i>
              </button>
            </div>
          </div>
          <input type="file" id="fotoInput" class="d-none" accept="image/jpeg,image/png,image/webp">
          <input type="hidden" name="foto_perfil" id="fotoHidden" value="<?= htmlspecialchars($tutor['foto_perfil'] ?? '') ?>">
          <div class="text-center small text-muted mt-1">
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-1" id="btnCambiarFoto">
              <i class="bi bi-camera me-1"></i> Cambiar foto
            </button>
            <span id="fotoStatus" class="d-block mt-1 text-success" style="display:none;">Foto listada ✓</span>
          </div>
        </div>

        <!-- Campos del perfil -->
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label small text-muted">Especialidad principal</label>
            <input type="text" name="especialidad" class="form-control form-control-sm" 
                   value="<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>" 
                   placeholder="Ej: Desarrollo Web, Bases de Datos, Redes...">
          </div>
          <div class="col-12">
            <label class="form-label small text-muted">Perfil de LinkedIn</label>
            <input type="url" name="perfil_linkedin" class="form-control form-control-sm" 
                   value="<?= htmlspecialchars($tutor['perfil_linkedin'] ?? '') ?>" 
                   placeholder="https://www.linkedin.com/in/...">
          </div>
          <div class="col-12">
            <label class="form-label small text-muted">Biografía / Presentación</label>
            <textarea name="biografia" class="form-control form-control-sm" rows="3" 
                      placeholder="Cuéntanos sobre tu trayectoria académica y profesional..."><?= htmlspecialchars($tutor['biografia'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small text-muted">Certificaciones y títulos</label>
            <textarea name="certificaciones" class="form-control form-control-sm" rows="2" 
                      placeholder="Ej: Ingeniero de Sistemas UPDS (2020), AWS Cloud Practitioner (2023)..."><?= htmlspecialchars($tutor['certificaciones'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small text-muted">Áreas de expertise</label>
            <input type="text" name="areas_expertise" class="form-control form-control-sm" 
                   value="<?= htmlspecialchars($tutor['areas_expertise'] ?? '') ?>" 
                   placeholder="Ej: Desarrollo Web, Bases de Datos, Inteligencia Artificial (separar con comas)">
            <div class="form-text">Separa cada área con comas. Estos temas aparecerán como badges en tu perfil.</div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-top">
          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i class="bi bi-check2-circle me-1"></i> Actualizar Perfil Profesional
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JavaScript para manejo de foto -->
<script>
(() => {
  const btnCambiarFoto = document.getElementById('btnCambiarFoto');
  const fotoInput = document.getElementById('fotoInput');
  const fotoHidden = document.getElementById('fotoHidden');
  const fotoStatus = document.getElementById('fotoStatus');

  btnCambiarFoto.addEventListener('click', () => fotoInput.click());

  fotoInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Validación cliente
    if (file.size > 2 * 1024 * 1024) {
      alert('La foto no puede superar 2MB.');
      fotoInput.value = '';
      return;
    }
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
      alert('Usa JPG, PNG o WebP.');
      fotoInput.value = '';
      return;
    }

    // Preview del avatar
    const reader = new FileReader();
    reader.onload = (ev) => {
      // Reemplazar o actualizar el avatar
      const container = document.querySelector('.avatar-tutor-container');
      const img = document.createElement('img');
      img.src = ev.target.result;
      img.alt = 'Foto de perfil';
      img.className = 'avatar-img rounded-circle';
      img.style.cssText = 'width:100px;height:100px;object-fit:cover;border:3px solid var(--upds-navy);';
      container.innerHTML = '';
      container.appendChild(img);

      // Agregar botón de cambio de nuevo
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn-change-photo';
      btn.style.cssText = 'position:absolute;bottom:-4px;right:-4px;width:36px;height:36px;border-radius:50%;background:var(--upds-navy);color:white;border:2px solid white;display:flex;align-items:center;justify-content:center;';
      btn.title = 'Cambiar foto de perfil';
      btn.innerHTML = '<i class="bi bi-camera-fill"></i>';
      btn.addEventListener('click', () => fotoInput.click());
      container.appendChild(btn);

      fotoStatus.style.display = 'block';
      fotoStatus.textContent = 'Subiendo foto...';
      
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
          fotoHidden.value = data.ruta;
          fotoStatus.textContent = 'Foto subida correctamente ✓';
          fotoStatus.className = 'd-block mt-1 text-success';
        } else {
          fotoStatus.textContent = 'Error: ' + data.error;
          fotoStatus.className = 'd-block mt-1 text-danger';
          fotoInput.value = '';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        fotoStatus.textContent = 'Error al subir la foto';
        fotoStatus.className = 'd-block mt-1 text-danger';
        fotoInput.value = '';
      });
    };
    reader.readAsDataURL(file);
  });
})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
