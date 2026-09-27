<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Defensas de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Programación de Defensas';
$descripcion = 'Agenda de defensas de MG con control anti-cruces (HU-029).';
$icono = 'bi-calendar2-check';
$contador = count($defensas);
$accion = ($cohorteId > 0)
    ? '<a href="/controllers/mg_defensas_programar.php?cohorte=' . $cohorteId . '" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Programar defensa</span></a>'
    : '';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" action="/controllers/mg_defensas_listar.php" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-df-cohorte">Cohorte</label>
        <select id="sel-df-cohorte" name="cohorte" class="form-select rounded-3" onchange="this.form.submit()">
          <?php foreach ($cohortes as $c): ?>
            <option value="<?= (int) $c['id_cohorte_mg'] ?>" <?= (int) $c['id_cohorte_mg'] === $cohorteId ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nombre_periodo']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-df-estado">Estado</label>
        <select id="sel-df-estado" name="estado" class="form-select rounded-3" onchange="this.form.submit()">
          <option value="">Todos</option>
          <option value="programada" <?= $estado === 'programada' ? 'selected' : '' ?>>Programadas</option>
          <option value="evaluada" <?= $estado === 'evaluada' ? 'selected' : '' ?>>Evaluadas</option>
        </select>
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-outline-primary px-3 rounded-3"><i class="bi bi-filter"></i> Ver</button>
      </div>
    </form>
    <?php if ($cohorteActual): ?>
      <div class="small text-muted mt-2">
        <?= htmlspecialchars($cohorteActual['fecha_inicio']) ?> al
        <?= $cohorteActual['fecha_fin'] ? htmlspecialchars($cohorteActual['fecha_fin']) : '—' ?>
        · <span class="fw-semibold"><?= htmlspecialchars(ucfirst($cohorteActual['estado'])) ?></span>
      </div>
    <?php endif; ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Fecha / Hora</th>
          <th>Estudiante</th>
          <th>Modalidad</th>
          <th>Ambiente</th>
          <th>Tribunales</th>
          <th>Correlativo citación</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($defensas as $d): ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= htmlspecialchars($d['fecha_defensa']) ?></div>
              <div class="small text-muted"><?= htmlspecialchars($d['hora_inicio']) ?> – <?= htmlspecialchars($d['hora_fin']) ?></div>
            </td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($d['estudiante_apellido'] . ' ' . $d['estudiante_nombre']) ?></div>
              <div class="small text-muted"><?= htmlspecialchars($d['registro_universitario']) ?></div>
            </td>
            <td class="small"><?= htmlspecialchars($d['modalidad_nombre']) ?></td>
            <td class="small">
              <?= $d['ambiente_nombre'] ? htmlspecialchars($d['ambiente_nombre']) : '<span class="text-muted">—</span>' ?>
            </td>
            <td>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><?= (int) $d['num_tribunales'] ?></span>
              <span class="small text-muted ms-1">tribunal(es)</span>
            </td>
            <td class="small">
              <?php if ($d['correlativo']): ?>
                <?= htmlspecialchars($d['correlativo']) ?>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <?= mgDefensaEstadoBadge($d['estado']) ?>
              <div class="btn-group ms-2" role="group">
                <a href="/controllers/mg_citacion_defensa.php?id=<?= (int) $d['id_defensa_mg'] ?>" class="btn btn-outline-secondary btn-sm rounded-start-2" title="Imprimir citación">
                  <i class="bi bi-envelope-paper"></i>
                </a>
                <?php if ($d['estado'] === 'programada'): ?>
                  <a href="/controllers/mg_defensa_evaluar.php?id=<?= (int) $d['id_defensa_mg'] ?>" class="btn btn-outline-success btn-sm" title="Evaluar defensa">
                    <i class="bi bi-clipboard2-check"></i>
                  </a>
                  <a href="/controllers/mg_defensas_programar.php?id=<?= (int) $d['id_defensa_mg'] ?>" class="btn btn-outline-primary btn-sm" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                  <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2"
                          onclick="confirmarEliminacion('/controllers/mg_defensas_eliminar.php?id=<?= (int) $d['id_defensa_mg'] ?>', 'Se eliminará la defensa de <?= htmlspecialchars($d['estudiante_apellido']) ?> <?= htmlspecialchars($d['estudiante_nombre']) ?>.')"
                          title="Eliminar">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                <?php else: ?>
                  <a href="/controllers/mg_defensa_evaluar.php?id=<?= (int) $d['id_defensa_mg'] ?>" class="btn btn-outline-success btn-sm rounded-end-2" title="Ver evaluación">
                    <i class="bi bi-eye"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($defensas)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-calendar2-x fs-1 d-block mb-2 text-secondary"></i>
              No hay defensas registradas para esta cohorte.
              <?php if ($cohorteId > 0): ?>
                <a href="/controllers/mg_defensas_programar.php?cohorte=<?= $cohorteId ?>">Programar la primera</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>