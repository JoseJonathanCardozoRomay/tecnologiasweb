<?php /* Parcial: agenda libre (fecha + modalidad + horas de inicio/fin). Req 5: horarios acordados dinámicamente. */ ?>
<h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-event me-1 text-primary"></i>Agenda</h5>
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="fecha">Fecha de la sesión *</label>
    <input type="date" name="fecha" id="fecha" class="form-control rounded-3 py-2" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" required>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="hora_inicio">Hora de inicio *</label>
    <input type="time" name="hora_inicio" id="hora_inicio" class="form-control rounded-3 py-2" step="300" value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '') ?>" required>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="hora_fin">Hora de fin *</label>
    <input type="time" name="hora_fin" id="hora_fin" class="form-control rounded-3 py-2" step="300" value="<?= htmlspecialchars($_POST['hora_fin'] ?? '') ?>" required>
  </div>
  <div class="col-md-2">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="modalidad">Modalidad *</label>
    <select name="modalidad" id="modalidad" class="form-select rounded-3 py-2" required>
      <option value="virtual" <?= (($_POST['modalidad'] ?? 'virtual') === 'virtual') ? 'selected' : '' ?>>Virtual</option>
      <option value="presencial" <?= (($_POST['modalidad'] ?? 'virtual') === 'presencial') ? 'selected' : '' ?>>Presencial</option>
    </select>
  </div>
</div>
<div class="row g-3 mt-1">
  <div class="col-12">
    <label class="form-label fw-semibold text-secondary small text-uppercase" for="observaciones">Observaciones para el docente</label>
    <textarea name="observaciones" id="observaciones" class="form-control rounded-3" rows="3" maxlength="1000" placeholder="Contextualizá tu consulta, el parcial que necesitás reforzar o cualquier acuerdo previo."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
    <div class="form-text">Opcional, hasta 1000 caracteres.</div>
  </div>
</div>
<p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Podés proponer cualquier horario: el docente o administración coordinará contigo la confirmación final.</p>