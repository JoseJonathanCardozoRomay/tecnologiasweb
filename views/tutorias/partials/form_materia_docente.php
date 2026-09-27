<?php /* Parcial: selección de materia y docente tutor (usado en la pestaña Apoyo). */ ?>
<h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-ul me-1 text-primary"></i>Materia y docente</h5>
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-materia">Materia *</label>
    <select id="select-materia" class="form-select rounded-3 py-2 select2-enabled" data-placeholder="Selecciona una materia">
      <option value="">Selecciona una materia</option>
      <?php foreach ($materias as $materia): ?>
        <option value="<?= $materia['id_materia'] ?>" <?= (($_POST['id_materia'] ?? '') == $materia['id_materia']) ? 'selected' : '' ?>><?= htmlspecialchars($materia['nombre_materia']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="select-tutor">Docente Tutor *</label>
    <select id="select-tutor" class="form-select rounded-3 py-2" data-placeholder="Primero elige la materia" disabled>
      <option value="">Primero elige una materia</option>
    </select>
    <small class="text-muted">Solo aparecen tutores de tu carrera que imparten la materia.</small>
  </div>
</div>