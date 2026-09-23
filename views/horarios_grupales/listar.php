<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Oferta oficial</span>
        <h1 class="page-title mb-1">Horarios grupales</h1>
        <p class="text-muted mb-0">Bloques definidos por la universidad para tutorías de materias.</p>
    </div>
    <?php if(esAdministrador()): ?>
        <a href="horarios_grupales_crear.php" class="btn btn-primary page-header-compact-action align-self-start"><i class="bi bi-calendar-plus me-1"></i>Nuevo horario</a>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($turnos as $turnoKey => $turno): ?>
        <div class="col-md-4">
            <div class="card card-custom p-3 h-100">
                <span class="badge text-bg-<?=e($turno['clase'])?> mb-2 align-self-start"><?=e($turno['titulo'])?></span>
                <strong><?=e($turno['horario'])?></strong>
                <small class="text-muted">Lunes a viernes</small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card card-custom p-3 mb-4">
    <form class="row g-2 align-items-end" method="get">
        <div class="col-lg-6">
            <label class="form-label small text-muted mb-1" for="buscarMateria">Materia</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input id="buscarMateria" class="form-control" type="search" name="buscar" value="<?=e($buscar)?>" placeholder="Buscar materia..." autocomplete="off">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label small text-muted mb-1" for="filtroEstado">Estado</label>
            <select id="filtroEstado" class="form-select" name="estado">
                <option value="">Todos los estados</option>
                <option value="activo" <?=($estado ?? '')==='activo'?'selected':''?>>Activos</option>
                <option value="inactivo" <?=($estado ?? '')==='inactivo'?'selected':''?>>Inactivos</option>
            </select>
        </div>
        <div class="col-lg-auto d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            <?php if (($estado ?? '') !== '' || ($buscar ?? '') !== ''): ?>
                <a class="btn btn-light" href="horarios_grupales_listar.php">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php foreach ($turnos as $turnoKey => $turno): ?>
    <section class="mb-4" aria-labelledby="turno-<?=e($turnoKey)?>">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <div>
                <h2 id="turno-<?=e($turnoKey)?>" class="h5 mb-1 text-primary"><?=e($turno['titulo'])?></h2>
                <p class="small text-muted mb-0"><?=e($turno['horario'])?> · Lunes a viernes</p>
            </div>
            <span class="badge bg-light text-primary border"><?=count($registrosPorTurno[$turnoKey])?> horario(s)</span>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Día</th>
                            <th>Materia</th>
                            <th>Tutor</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($registrosPorTurno[$turnoKey] as $h): ?>
                        <tr>
                            <td>
                                <strong><?=e($h['dia_semana'])?></strong>
                                <div class="small text-muted"><?=e(substr($h['hora_inicio'],0,5))?> – <?=e(substr($h['hora_fin'],0,5))?></div>
                            </td>
                            <td>
                                <?=e($h['nombre_materia'])?>
                                <div class="small text-muted"><?=e($h['nombre_carrera'])?></div>
                            </td>
                            <td><?=e(trim($h['tutor_nombre'].' '.$h['tutor_apellido']))?></td>
                            <td><span class="badge text-bg-<?=$h['estado']==='activo'?'success':'secondary'?>"><?=e(ucfirst($h['estado']))?></span></td>
                            <td class="text-end">
                                <?php if(esAdministrador()): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="horarios_grupales_editar.php?id=<?=e($h['id_horario'])?>" aria-label="Editar horario"><i class="bi bi-pencil"></i></a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    <?php if(!$registrosPorTurno[$turnoKey]): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay materias disponibles en este turno con los filtros seleccionados.</td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<?php include __DIR__.'/../layouts/footer.php'; ?>
