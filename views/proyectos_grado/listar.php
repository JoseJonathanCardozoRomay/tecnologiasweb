<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Trabajo de titulación</span>
        <h1 class="page-title mb-1">Proyectos de grado</h1>
        <p class="text-muted mb-0">Aquí se gestionan los proyectos y el acompañamiento personal solicitado a los tutores.</p>
    </div>
    <?php if (esEstudiante()): ?>
        <a href="proyectos_grado_crear.php" class="btn btn-sm btn-primary align-self-start page-header-compact-action"><i class="bi bi-plus-circle me-1"></i>Nuevo proyecto</a>
    <?php endif; ?>
</div>

<?php if (esAdministrador()): ?>
<div class="alert alert-info border-0 shadow-sm mb-3">
    <i class="bi bi-shield-check me-2"></i>
    Los proyectos son registrados por los estudiantes. Administración no crea proyectos: confirma las solicitudes de tutoría que el tutor acepta y, al programarlas, asigna el aula o enlace.
</div>
<?php elseif (esTutor()): ?>
<div class="alert alert-light border shadow-sm mb-3">
    <i class="bi bi-person-check text-primary me-2"></i>
    Aquí puedes consultar los proyectos relacionados con tus solicitudes de tutoría personal y responder las solicitudes pendientes.
</div>
<?php else: ?>
<div class="alert alert-light border shadow-sm mb-3">
    <i class="bi bi-info-circle text-primary me-2"></i>
    Registra tu proyecto y solicita una tutoría personal seleccionando la disponibilidad real del docente. La solicitud permanece pendiente hasta que el tutor la acepte y administración la confirme.
</div>
<?php endif; ?>

<div class="card card-custom p-3 mb-3">
    <form method="get" class="row g-2 align-items-end">
        <input type="hidden" name="seccion" value="<?=e($seccion)?>">
        <div class="col-lg-6">
            <label class="form-label small text-muted">Buscar</label>
            <input class="form-control" name="q" value="<?=e($busqueda ?? '')?>" placeholder="Proyecto, estudiante, tutor o carrera">
        </div>
        <div class="col-lg-3">
            <label class="form-label small text-muted">Estado del proyecto</label>
            <select class="form-select" name="estado">
                <option value="">Todos</option>
                <?php
                    // Los filtros usan las etiquetas funcionales actuales del sistema.
                    // `en_proceso` y `finalizado` son valores internos heredados,
                    // pero se presentan al usuario como "En curso" y "Concluido".
                    $estadosProyectoFiltro = [
                        'propuesto'  => 'Propuesto',
                        'en_proceso' => 'En curso',
                        'finalizado' => 'Concluido',
                        'cancelado'  => 'Cancelado',
                    ];
                ?>
                <?php foreach($estadosProyectoFiltro as $valorEstado => $etiquetaEstado): ?>
                    <option value="<?=e($valorEstado)?>" <?=($estadoProyecto??'')===$valorEstado?'selected':''?>><?=e($etiquetaEstado)?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-3 d-flex gap-2">
            <button class="btn btn-outline-primary flex-grow-1"><i class="bi bi-search me-1"></i>Filtrar</button>
            <a class="btn btn-light" href="proyectos_grado_listar.php?seccion=<?=e($seccion)?>">Limpiar</a>
        </div>
    </form>
</div>

<div class="d-flex gap-2 mb-3" role="tablist" aria-label="Secciones de proyectos de grado">
    <a href="proyectos_grado_listar.php?seccion=proyectos" class="btn btn-sm <?= $seccion === 'proyectos' ? 'btn-primary' : 'btn-outline-primary' ?>">
        <i class="bi bi-file-earmark-text me-1"></i>Proyectos de grado
        <span class="badge text-bg-light ms-1"><?=count($registros)?></span>
    </a>
    <a href="proyectos_grado_listar.php?seccion=solicitudes" class="btn btn-sm <?= $seccion === 'solicitudes' ? 'btn-primary' : 'btn-outline-primary' ?>">
        <i class="bi bi-person-workspace me-1"></i>Solicitudes de tutoría
        <span class="badge text-bg-light ms-1"><?=count($solicitudes)?></span>
    </a>
</div>

<?php if ($seccion === 'proyectos'): ?>
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Proyecto</th>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Tutorías personales</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($registros as $p): ?>
                <?php
                    $sols = $solicitudesPorProyecto[(int)$p['id_proyecto']] ?? [];
                    // En estudiantes, una tutoría programada/en curso bloquea
                    // edición y nuevas solicitudes hasta que sea realizada.
                    $proyectoBloqueado = esEstudiante() && $pm->tieneTutoriaActiva((int)$p['id_proyecto']);
                ?>
                <tr>
                    <td>
                        <strong><?=e($p['titulo'])?></strong>
                        <?php if(!empty($p['descripcion'])): ?><div class="small text-muted text-truncate" style="max-width:320px"><?=e($p['descripcion'])?></div><?php endif; ?>
                    </td>
                    <td><?=e(trim(($p['nombre']??'').' '.($p['apellido']??'')))?><div class="small text-muted"><?=e($p['registro_universitario']??'')?></div></td>
                    <td><?=e($p['nombre_carrera'])?></td>
                    <td>
                        <?php if ($sols): ?>
                            <span class="badge text-bg-light border"><i class="bi bi-person-workspace me-1"></i><?=count($sols)?></span>
                            <div class="small text-muted mt-1">
                                <?php foreach(array_slice($sols,0,2) as $sol): ?>
                                    <span class="d-block"><?=e($sol['tutor'] ?? 'Tutor')?> · <?=e(estadoEtiqueta((string)$sol['estado']))?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="small text-muted">Sin solicitudes</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge text-bg-<?=match($p['estado']){'propuesto'=>'secondary','en_proceso'=>'primary','finalizado'=>'success','cancelado'=>'danger',default=>'dark'}?>"><?=e(estadoProyectoEtiqueta((string)$p['estado']))?></span></td>
                    <td><?=e(date('d/m/Y',strtotime($p['fecha_registro'])))?></td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                            <?php if (esEstudiante()): ?>
                                <?php if ($p['estado'] === 'finalizado'): ?>
                                    <span class="badge text-bg-light border text-muted align-self-center"><i class="bi bi-lock-fill me-1"></i>Proyecto concluido</span>
                                <?php elseif ($proyectoBloqueado): ?>
                                    <span class="badge text-bg-light border text-muted align-self-center"><i class="bi bi-lock me-1"></i>Bloqueado hasta finalizar tutoría</span>
                                <?php else: ?>
                                    <?php /* Solo el estudiante propietario puede editar su proyecto; tutor y administración nunca muestran este control. */ ?>
                                    <a class="btn btn-sm btn-outline-primary" href="proyectos_grado_editar.php?id=<?=e($p['id_proyecto'])?>" title="Editar proyecto"><i class="bi bi-pencil"></i></a>
                                    <?php if ($p['estado'] !== 'cancelado'): ?>
                                        <a class="btn btn-sm btn-outline-success" href="/controllers/tutorias_personal_crear.php?id_proyecto=<?=e($p['id_proyecto'])?>"><i class="bi bi-person-plus me-1"></i>Solicitar tutoría</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php elseif (esAdministrador()): ?>
                                <?php if ($p['estado'] === 'en_proceso'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#defensaProyectoModal" data-proyecto-id="<?=e($p['id_proyecto'])?>" data-proyecto-titulo="<?=e($p['titulo'])?>"><i class="bi bi-mortarboard me-1"></i>Defensa</button>
                                <?php elseif ($p['estado'] === 'finalizado'): ?>
                                    <span class="small text-muted align-self-center"><i class="bi bi-lock-fill me-1"></i>Sin acciones</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="small text-muted">Sin acciones</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(!$registros): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-journal-x fs-1 d-block mb-2"></i>No hay proyectos para mostrar.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Proyecto / tesis</th>
                <th><?=esTutor() ? 'Estudiante' : 'Tutor'?></th>
                <th>Fecha / hora</th>
                <th>Lugar / enlace</th>
                <th>Estado</th>
                <th>Respuesta del tutor</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($solicitudes as $r): ?>
                <tr>
                    <td>
                        <strong><?=e($r['proyecto_grado'] ?? 'Proyecto de grado')?></strong>
                        <div class="small text-muted"><?=e($r['nombre_carrera'] ?? '')?></div>
                    </td>
                    <td><?=e(esTutor() ? ($r['estudiante'] ?? '—') : ($r['tutor'] ?? '—'))?></td>
                    <td>
                        <strong><?=e(date('d/m/Y', strtotime($r['fecha'])))?></strong><br>
                        <small><?=e(substr((string)$r['hora_inicio'],0,5))?>–<?=e(substr((string)$r['hora_fin'],0,5))?></small>
                    </td>
                    <td>
                        <?php if (!empty($r['lugar_o_enlace'])): ?>
                            <?php if (($r['modalidad'] ?? '') === 'virtual' && filter_var((string)$r['lugar_o_enlace'], FILTER_VALIDATE_URL)): ?>
                                <a href="<?=e((string)$r['lugar_o_enlace'])?>" target="_blank" rel="noopener noreferrer" class="small">
                                    <i class="bi bi-camera-video me-1"></i>Abrir enlace
                                </a>
                            <?php else: ?>
                                <span class="small text-muted"><i class="bi bi-door-open me-1"></i><?=e((string)$r['lugar_o_enlace'])?></span>
                            <?php endif; ?>
                            <div class="small text-muted"><?=e(ucfirst((string)($r['modalidad'] ?? '')))?></div>
                        <?php else: ?>
                            <span class="small text-muted">Pendiente de programación</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge text-bg-<?=estadoBadge((string)$r['estado'])?>"><?=e(estadoEtiqueta((string)$r['estado']))?></span>
                        <?php if ($r['estado'] === 'pendiente' && ($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                            <div class="small text-muted mt-1">Tutor aceptó · pendiente de confirmación administrativa</div>
                        <?php elseif ($r['estado'] === 'pendiente'): ?>
                            <div class="small text-muted mt-1">Pendiente de respuesta</div>
                        <?php endif; ?>
                        <?php if ($r['estado'] === 'cancelada' && !empty($r['motivo_cancelacion'])): ?>
                            <div class="small text-muted mt-1"><strong>Motivo:</strong> <?=e($r['motivo_cancelacion'])?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                            <span class="small text-success"><i class="bi bi-check-circle me-1"></i>Docente aceptó</span>
                        <?php elseif (($r['estado_tutor'] ?? 'pendiente') === 'rechazada'): ?>
                            <span class="small text-danger"><i class="bi bi-x-circle me-1"></i>Docente rechazó</span>
                        <?php else: ?>
                            <span class="small text-muted"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                            <?php if (esEstudiante() && $r['estado'] === 'realizada' && empty($r['id_evaluacion'])): ?>
                                <a href="/controllers/evaluaciones_crear.php?id_tutoria=<?=e($r['id_tutoria'])?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-star me-1"></i>Evaluar</a>
                            <?php elseif (esEstudiante() && $r['estado'] === 'realizada' && !empty($r['id_evaluacion'])): ?>
                                <span class="badge text-bg-light border text-dark align-self-center"><i class="bi bi-check-circle me-1"></i>Evaluada</span>
                            <?php endif; ?>

                            <?php if (esTutor() && $r['estado'] === 'pendiente' && ($r['estado_tutor'] ?? 'pendiente') === 'pendiente'): ?>
                                <form method="post" action="/controllers/tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-success" name="accion" value="aceptar"><i class="bi bi-check-lg me-1"></i>Aceptar</button></form>
                                <form method="post" action="/controllers/tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-danger" name="accion" value="rechazar"><i class="bi bi-x-lg me-1"></i>Rechazar</button></form>
                            <?php endif; ?>

                            <?php if (esAdministrador() && $r['estado'] === 'pendiente' && ($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#confirmarPersonalModal" data-tutoria-id="<?=e($r['id_tutoria'])?>" data-estudiante="<?=e($r['estudiante'] ?? 'Estudiante')?>" data-fecha="<?=e(date('d/m/Y', strtotime($r['fecha'])))?>" data-horario="<?=e(substr($r['hora_inicio'],0,5).'–'.substr($r['hora_fin'],0,5))?>"><i class="bi bi-calendar-check me-1"></i>Confirmar</button>
                            <?php endif; ?>

                            <?php if (esTutor() && $r['estado'] === 'programada'): ?>
                                <form method="post" action="/controllers/tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-success" name="accion" value="realizar"><i class="bi bi-check2-circle me-1"></i>Finalizar</button></form>
                            <?php endif; ?>

                            <?php if (esAdministrador() && in_array($r['estado'], ['pendiente','programada','en_proceso'], true)): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelarPersonalModal" data-tutoria-id="<?=e($r['id_tutoria'])?>" data-resumen="<?=e(($r['proyecto_grado'] ?? 'Proyecto de grado').' · '.($r['estudiante'] ?? 'Estudiante'))?>"><i class="bi bi-slash-circle me-1"></i>Cancelar</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$solicitudes): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No hay solicitudes de tutoría personal para mostrar.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (esAdministrador()): ?>
<div class="modal fade" id="defensaProyectoModal" tabindex="-1" aria-labelledby="defensaProyectoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><div><span class="eyebrow">Resultado de defensa</span><h5 class="modal-title mb-0" id="defensaProyectoModalLabel">Registrar defensa del proyecto</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <form method="post" action="/controllers/proyectos_grado_defensa.php">
                <?=csrfField()?>
                <input type="hidden" name="id_proyecto" id="defensaProyectoId">
                <div class="modal-body">
                    <div class="alert alert-light border mb-3" id="defensaProyectoResumen">Selecciona el resultado de la defensa.</div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="resultado_defensa" value="aprobado" class="btn btn-outline-success"><i class="bi bi-check-circle me-1"></i>Defensa aprobada — concluir proyecto</button>
                        <button type="submit" name="resultado_defensa" value="reprobado" class="btn btn-outline-warning"><i class="bi bi-arrow-repeat me-1"></i>Defensa reprobada — mantener En curso</button>
                    </div>
                    <div class="form-text mt-3">Al aprobar, el proyecto queda concluido y ya no admite edición ni nuevas solicitudes. Al reprobar, permanece En curso.</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Volver</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmarPersonalModal" tabindex="-1" aria-labelledby="confirmarPersonalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><div><span class="eyebrow">Confirmación administrativa</span><h5 class="modal-title mb-0" id="confirmarPersonalModalLabel">Programar tutoría personal</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <form method="post" action="/controllers/tutorias_accion.php" id="formConfirmarPersonal">
                <?=csrfField()?><input type="hidden" name="id_tutoria" id="confirmarTutoriaId"><input type="hidden" name="accion" value="confirmar_programacion">
                <div class="modal-body">
                    <div class="alert alert-light border mb-3" id="confirmarResumen">Selecciona una solicitud aceptada por el tutor.</div>
                    <label class="form-label" for="modalidadConfirmacion">Modalidad definitiva</label>
                    <select class="form-select mb-3" name="modalidad_confirmacion" id="modalidadConfirmacion" required><option value="presencial">Presencial</option><option value="virtual">Virtual</option></select>
                    <label class="form-label" for="lugarAdmin">Aula o enlace</label>
                    <input type="text" class="form-control" name="lugar_o_enlace_admin" id="lugarAdmin" maxlength="200" required placeholder="Ej.: Aula 204">
                    <div class="form-text">Este dato lo define administración y será visible al pasar a Programada.</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Volver</button><button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check me-1"></i>Confirmar y programar</button></div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelarPersonalModal" tabindex="-1" aria-labelledby="cancelarPersonalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><div><span class="eyebrow">Cancelación administrativa</span><h5 class="modal-title mb-0" id="cancelarPersonalModalLabel">Cancelar tutoría personal</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <form method="post" action="/controllers/tutorias_accion.php" id="formCancelarPersonal">
                <?=csrfField()?><input type="hidden" name="id_tutoria" id="cancelarTutoriaId"><input type="hidden" name="accion" value="cancelar">
                <div class="modal-body"><div class="alert alert-light border" id="cancelarResumen">Esta tutoría será cancelada.</div><label class="form-label" for="motivoCancelacion">Motivo de cancelación</label><textarea class="form-control" name="motivo_cancelacion" id="motivoCancelacion" rows="4" minlength="5" maxlength="500" required placeholder="Indica el motivo de la cancelación..."></textarea></div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Volver</button><button type="submit" class="btn btn-outline-danger"><i class="bi bi-slash-circle me-1"></i>Confirmar cancelación</button></div>
            </form>
        </div>
    </div>
</div>
<script>
const defensaProyectoModal = document.getElementById('defensaProyectoModal');
if (defensaProyectoModal) {
    defensaProyectoModal.addEventListener('show.bs.modal', function(event) {
        const b = event.relatedTarget;
        document.getElementById('defensaProyectoId').value = b.dataset.proyectoId || '';
        document.getElementById('defensaProyectoResumen').innerHTML = '<strong>' + (b.dataset.proyectoTitulo || 'Proyecto de grado') + '</strong><br>Selecciona el resultado de la defensa.';
    });
}

const confirmarPersonalModal = document.getElementById('confirmarPersonalModal');
if (confirmarPersonalModal) {
    confirmarPersonalModal.addEventListener('show.bs.modal', function(event) {
        const b = event.relatedTarget;
        document.getElementById('confirmarTutoriaId').value = b.dataset.tutoriaId || '';
        document.getElementById('confirmarResumen').innerHTML = '<strong>'+ (b.dataset.estudiante || 'Estudiante') + '</strong><br>' + (b.dataset.fecha || '') + ' · ' + (b.dataset.horario || '');
        document.getElementById('lugarAdmin').value = '';
    });
    document.getElementById('modalidadConfirmacion').addEventListener('change', function(){
        document.getElementById('lugarAdmin').placeholder = this.value === 'virtual' ? 'https://meet.google.com/...' : 'Ej.: Aula 204';
    });
}
const cancelarPersonalModal = document.getElementById('cancelarPersonalModal');
if (cancelarPersonalModal) {
    cancelarPersonalModal.addEventListener('show.bs.modal', function(event) {
        const b = event.relatedTarget;
        document.getElementById('cancelarTutoriaId').value = b.dataset.tutoriaId || '';
        document.getElementById('cancelarResumen').textContent = b.dataset.resumen || 'Esta tutoría será cancelada.';
        document.getElementById('motivoCancelacion').value = '';
    });
}
</script>
<?php endif; ?>
<?php include __DIR__.'/../layouts/footer.php'; ?>
