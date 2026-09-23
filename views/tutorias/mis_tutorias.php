<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4">
    <span class="eyebrow">Agenda personal</span>
    <h1 class="page-title mb-1">Mis tutorías</h1>
    <p class="text-muted mb-0">Consulta por separado tus tutorías de materias y tus acompañamientos de proyectos de grado.</p>
</div>

<!-- Tutorías grupales de materias: se mantienen separadas porque usan el horario institucional. -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="p-4 border-bottom">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <span class="eyebrow">Modalidad grupal</span>
                <h2 class="h5 fw-bold mb-1">Tutorías de materias</h2>
                <p class="small text-muted mb-0">Sesiones grupales programadas según el horario oficial de la universidad.</p>
            </div>
            <?php if (esTutor()): ?>
                <a href="/controllers/tutorias_grupales_listar.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-people me-1"></i>Ver tutorías grupales
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha / hora</th>
                    <th>Materia</th>
                    <?php if (esTutor()): ?><th>Estudiantes</th><?php endif; ?>
                    <th><?=esTutor() ? 'Turno' : 'Tutor'?></th>
                    <th>Detalle</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grupales as $x): ?>
                <tr>
                    <td>
                        <strong><?=e(date('d/m/Y', strtotime($x['fecha'])))?></strong><br>
                        <small><?=e(substr((string)$x['hora_inicio'],0,5))?>–<?=e(substr((string)$x['hora_fin'],0,5))?></small>
                    </td>
                    <td>
                        <strong><?=e($x['nombre_materia'] ?? 'Materia')?></strong>
                        <?php if (!empty($x['turno'])): ?><div class="small text-muted">Turno <?=e(ucfirst((string)$x['turno']))?></div><?php endif; ?>
                    </td>
                    <?php if (esTutor()): ?>
                        <td>
                            <span class="badge text-bg-light border">
                                <i class="bi bi-people me-1"></i><?=e($x['total_estudiantes'] ?? 0)?>
                            </span>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if (esTutor()): ?>
                            <?=e(ucfirst((string)($x['turno'] ?? '—')))?></td>
                        <?php else: ?>
                            <?=e($x['tutor'] ?? '—')?></td>
                        <?php endif; ?>
                    <td><span class="small text-muted">Sesión grupal · <?=e(substr((string)$x['hora_inicio'],0,5))?>–<?=e(substr((string)$x['hora_fin'],0,5))?></span></td>
                    <td><span class="badge text-bg-<?=estadoBadge((string)$x['estado'])?>"><?=e(estadoEtiqueta((string)$x['estado']))?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$grupales): ?>
                <tr><td colspan="<?=esTutor() ? '6' : '5'?>" class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>No tienes tutorías grupales próximas.
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tutorías personales: requieren aceptación del tutor y confirmación administrativa. -->
<div class="card card-custom overflow-hidden">
    <div class="p-4 border-bottom">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <span class="eyebrow">Modalidad individual</span>
                <h2 class="h5 fw-bold mb-1">Tutorías de proyectos de grado</h2>
                <p class="small text-muted mb-0">Acompañamientos individuales para tesis, proyectos y trabajos de grado.</p>
            </div>
            <?php if (esEstudiante()): ?>
                <a href="/controllers/tutorias_personal_crear.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-mortarboard me-1"></i>Solicitar tutoría para proyecto
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha / hora</th>
                    <th>Proyecto / tesis</th>
                    <th><?=esTutor() ? 'Estudiante' : 'Tutor'?></th>
                    <th>Lugar / enlace</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($personales as $x): ?>
                <tr>
                    <td>
                        <strong><?=e(date('d/m/Y', strtotime($x['fecha'])))?></strong><br>
                        <small><?=e(substr((string)$x['hora_inicio'],0,5))?>–<?=e(substr((string)$x['hora_fin'],0,5))?></small>
                    </td>
                    <td><strong><?=e($x['nombre_materia'] ?? 'Proyecto de grado')?></strong></td>
                    <td><?=e(esTutor() ? ($x['estudiante'] ?? '—') : ($x['tutor'] ?? '—'))?></td>
                    <td>
                        <?php if (!empty($x['lugar_o_enlace'])): ?>
                            <?php if (($x['modalidad'] ?? '') === 'virtual' && filter_var((string)$x['lugar_o_enlace'], FILTER_VALIDATE_URL)): ?>
                                <a href="<?=e((string)$x['lugar_o_enlace'])?>" target="_blank" rel="noopener noreferrer" class="small">
                                    <i class="bi bi-camera-video me-1"></i>Abrir enlace
                                </a>
                            <?php else: ?>
                                <span class="small text-muted"><i class="bi bi-door-open me-1"></i><?=e((string)$x['lugar_o_enlace'])?></span>
                            <?php endif; ?>
                            <div class="small text-muted"><?=e(ucfirst((string)($x['modalidad'] ?? '')))?></div>
                        <?php else: ?>
                            <span class="small text-muted">Se asignará al programar</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="small text-muted">Acompañamiento individual</span></td>
                    <td><span class="badge text-bg-<?=estadoBadge((string)$x['estado'])?>"><?=e(estadoEtiqueta((string)$x['estado']))?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$personales): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>No tienes tutorías personales próximas.
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (esEstudiante()): ?>
<div class="row g-3 mt-3">
    <div class="col-md-6"><a href="/controllers/tutorias_grupales_listar.php" class="quick-link"><i class="bi bi-people text-primary"></i><span>Explorar tutorías de materias</span><i class="bi bi-arrow-right ms-auto"></i></a></div>
    <div class="col-md-6"><a href="/controllers/proyectos_grado_listar.php?seccion=solicitudes" class="quick-link"><i class="bi bi-mortarboard text-success"></i><span>Ver proyectos y solicitudes</span><i class="bi bi-arrow-right ms-auto"></i></a></div>
</div>
<?php endif; ?>
<?php include __DIR__.'/../layouts/footer.php'; ?>
