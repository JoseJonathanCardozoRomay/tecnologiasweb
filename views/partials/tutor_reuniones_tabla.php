<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3">
    <form method="GET" action="reuniones_registrar.php" class="row g-2 align-items-end" autocomplete="off">
      <div class="col-sm-7">
        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1">Historial de reuniones</label>
        <select name="id_tutoria" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
          <option value="0">Todas mis tutorías (<?= (int) ($totalReunionesTutor ?? 0) ?> reuniones)</option>
          <?php foreach (($tutorias ?? []) as $t): ?>
            <option value="<?= (int) $t['id_tutoria'] ?>" <?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'selected' : '' ?>>
              <?= htmlspecialchars(trim($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido'])) ?> — <?= htmlspecialchars($t['nombre_materia']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-5 d-flex gap-2">
        <?php if ((int) ($id_tutoria ?? 0) > 0): ?>
          <a href="reuniones_registrar.php" class="btn btn-sm btn-outline-secondary rounded-3">Ver todas</a>
        <?php endif; ?>
        <button type="submit" class="btn btn-sm btn-primary rounded-3"><i class="bi bi-funnel me-1"></i>Filtrar</button>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Reunión</th>
          <th>Fecha</th>
          <th>Horario</th>
          <th>Asistencia</th>
          <th>Evidencia</th>
          <th>Informe</th>
          <th>Temas / observaciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($reuniones)): ?>
          <tr>
            <td colspan="7" class="text-center py-5">
              <?php
              $emptyTitulo = 'Sin reuniones registradas';
              $emptyTexto = 'Registra tu primera reunión para que aparezca aquí automáticamente, sin agendar otra para refrescar.';
              include __DIR__ . '/empty_state.php';
              ?>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($reuniones as $r):
          $seg = $seguimientos[(int) $r['id_reunion']] ?? null;
          $asistenciaMostrada = $seg ? $seg['asistencia'] : $r['asistio_estudiante'];
        ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark">#<?= (int) $r['id_reunion'] ?></div>
              <small class="text-muted"><?= htmlspecialchars((string) ($r['estudiantes_nombres'] ?? 'Sin estudiante')) ?></small>
              <small class="d-block text-muted"><?= htmlspecialchars($r['nombre_materia'] ?? 'Sin materia') ?></small>
            </td>
            <td class="text-dark"><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
            <td class="text-muted"><?= substr((string) $r['hora_inicio'], 0, 5) ?> - <?= substr((string) $r['hora_fin'], 0, 5) ?> h</td>
            <td>
              <?php if ($seg): ?>
                <span class="badge border px-3 py-1 <?= ReunionModel::claseAsistencia($asistenciaMostrada) ?>">
                  <?= htmlspecialchars(ReunionModel::etiquetaAsistencia($asistenciaMostrada)) ?>
                </span>
                <span class="d-block mt-1 badge border px-3 py-1 <?= ReunionModel::claseCumplimiento($seg['cumplimiento']) ?>">
                  <?= htmlspecialchars(ReunionModel::etiquetaCumplimiento($seg['cumplimiento'])) ?>
                </span>
              <?php else: ?>
                <span class="badge border px-3 py-1 bg-secondary bg-opacity-10 text-secondary border-secondary-subtle"
                      title="La asistencia se registra en la pestaña Seguimiento, una vez realizada la reunión.">
                  <i class="bi bi-hourglass-bottom me-1"></i>Pendiente
                </span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($r['evidencia_url'])): ?>
                <a href="/controllers/reuniones_evidencia.php?id=<?= (int) $r['id_reunion'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="Ver evidencia"><i class="bi bi-paperclip me-1"></i>Ver</a>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($seg): ?>
                <div class="btn-group btn-group-sm" role="group" aria-label="Descargar informe de la reunión">
                  <a href="/controllers/reuniones_informe.php?id_reunion=<?= (int) $r['id_reunion'] ?>&formato=pdf" class="btn btn-outline-primary" title="Generar informe en PDF"><i class="bi bi-file-earmark-pdf"></i><span class="visually-hidden">PDF</span></a>
                  <a href="/controllers/reuniones_informe.php?id_reunion=<?= (int) $r['id_reunion'] ?>&formato=doc" class="btn btn-outline-primary" title="Generar informe en Word"><i class="bi bi-file-earmark-word"></i><span class="visually-hidden">DOC</span></a>
                </div>
              <?php else: ?>
                <span class="text-muted small" title="El informe se habilita al registrar el seguimiento.">—</span>
              <?php endif; ?>
            </td>
            <td class="text-muted small text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($r['observaciones'] ?? '') ?>"><?= htmlspecialchars($r['observaciones'] ?? '—') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
