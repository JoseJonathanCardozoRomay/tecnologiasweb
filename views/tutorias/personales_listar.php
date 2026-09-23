<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Tutoría individual</span>
        <h1 class="page-title mb-1"><?= esEstudiante() ? 'Mis tutorías de proyectos de grado' : 'Tutorías de proyectos de grado' ?></h1>
        <p class="text-muted mb-0">Tesis, proyectos de grado y trabajos de culminación con seguimiento individual.</p>
    </div>
</div>

<div class="card card-custom p-3 mb-3">
    <form method="get" class="row g-2 align-items-center">
        <div class="col-auto"><label class="small text-muted" for="estado">Estado</label></div>
        <div class="col-md-3">
            <select name="estado" id="estado" class="form-select">
                <option value="">Todos</option>
                <?php foreach (['pendiente','programada','rechazada','realizada','cancelada','en_proceso'] as $s): ?>
                    <option value="<?=e($s)?>" <?=$filtro === $s ? 'selected' : ''?>><?=e(estadoEtiqueta($s))?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filtrar</button></div>
        <div class="col-auto"><a href="tutorias_personales_listar.php" class="btn btn-light">Limpiar</a></div>
    </form>
</div>

<?php if (esAdministrador()): ?>
<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-shield-check me-2"></i>
    La administración supervisa el flujo: primero el tutor acepta o rechaza, después administración confirma la programación y asigna el aula o enlace. La cancelación corresponde exclusivamente a administración.
</div>
<?php endif; ?>

<?php if (esEstudiante()): ?>
<div class="alert alert-light border shadow-sm mb-3">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-info-circle text-primary fs-5"></i>
        <div>
            <strong>Seguimiento de solicitudes</strong>
            <div class="small text-muted mt-1">Aquí puedes ver qué docentes respondieron a cada solicitud de tu proyecto. Cuando uno acepta, las solicitudes que seguían pendientes con otros docentes se cierran automáticamente; las respuestas de rechazo que ya hayan ocurrido permanecen visibles como historial.</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Fecha / hora</th>
                <th>Estudiante</th>
                <th>Carrera</th>
                <th>Proyecto / tesis</th>
                <th>Tutor</th>
                <th>Lugar / enlace</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td>
                        <strong><?=e(date('d/m/Y', strtotime($r['fecha'])))?></strong><br>
                        <small><?=e(substr($r['hora_inicio'],0,5))?>–<?=e(substr($r['hora_fin'],0,5))?></small>
                    </td>
                    <td><?=e($r['estudiante'] ?? '—')?></td>
                    <td><?=e($r['nombre_carrera'] ?? '—')?></td>
                    <td><strong><?=e($r['proyecto_grado'] ?? 'Proyecto de grado')?></strong><div class="small text-muted">Acompañamiento individual</div></td>
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
                            <span class="small text-muted">Se asignará al programar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?=e($r['tutor'] ?? '—')?></div>
                        <?php if (esEstudiante()): ?>
                            <?php if (($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                                <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i>Este docente aceptó tu solicitud</div>
                            <?php elseif (($r['estado_tutor'] ?? 'pendiente') === 'rechazada'): ?>
                                <div class="small text-danger mt-1"><i class="bi bi-x-circle me-1"></i>Este docente rechazó tu solicitud</div>
                            <?php else: ?>
                                <div class="small text-muted mt-1"><i class="bi bi-hourglass-split me-1"></i>Pendiente de respuesta</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge text-bg-<?=estadoBadge((string)$r['estado'])?>"><?=e(estadoEtiqueta((string)$r['estado']))?></span>
                        <?php if ($r['estado'] === 'pendiente'): ?>
                            <?php if (($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                                <div class="small text-muted mt-1"><i class="bi bi-check2-circle text-success me-1"></i>Tutor aceptó · pendiente de confirmación administrativa</div>
                            <?php elseif (($r['estado_tutor'] ?? 'pendiente') === 'rechazada'): ?>
                                <div class="small text-muted mt-1">El tutor rechazó la solicitud.</div>
                            <?php else: ?>
                                <div class="small text-muted mt-1">Pendiente de respuesta del tutor</div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($r['estado'] === 'cancelada' && !empty($r['motivo_cancelacion'])): ?>
                            <div class="small text-muted mt-1"><strong>Motivo:</strong> <?=e($r['motivo_cancelacion'])?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                            <?php if (esEstudiante() && $r['estado'] === 'realizada' && empty($r['id_evaluacion'])): ?>
                                <a href="evaluaciones_crear.php?id_tutoria=<?=e($r['id_tutoria'])?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-star me-1"></i>Evaluar</a>
                            <?php elseif (esEstudiante() && $r['estado'] === 'realizada' && !empty($r['id_evaluacion'])): ?>
                                <span class="badge text-bg-light border text-dark align-self-center"><i class="bi bi-check-circle me-1"></i>Evaluada</span>
                            <?php endif; ?>

                            <?php if (esTutor() && $r['estado'] === 'pendiente' && ($r['estado_tutor'] ?? 'pendiente') === 'pendiente'): ?>
                                <form method="post" action="tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-success" name="accion" value="aceptar" title="Aceptar solicitud"><i class="bi bi-check-lg me-1"></i>Aceptar</button></form>
                                <form method="post" action="tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-danger" name="accion" value="rechazar" title="Rechazar solicitud"><i class="bi bi-x-lg me-1"></i>Rechazar</button></form>
                            <?php endif; ?>

                            <?php if (esAdministrador() && $r['estado'] === 'pendiente' && ($r['estado_tutor'] ?? 'pendiente') === 'aceptada'): ?>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#confirmarPersonalModal" data-tutoria-id="<?=e($r['id_tutoria'])?>" data-estudiante="<?=e($r['estudiante'] ?? 'Estudiante')?>" data-fecha="<?=e(date('d/m/Y', strtotime($r['fecha'])))?>" data-horario="<?=e(substr($r['hora_inicio'],0,5).'–'.substr($r['hora_fin'],0,5))?>"><i class="bi bi-calendar-check me-1"></i>Confirmar</button>
                            <?php endif; ?>

                            <?php if (esTutor() && $r['estado'] === 'programada'): ?>
                                <form method="post" action="tutorias_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>"><button class="btn btn-sm btn-outline-success" name="accion" value="realizar" title="Marcar realizada"><i class="bi bi-check2-circle me-1"></i>Finalizar</button></form>
                            <?php endif; ?>

                            <?php if (esAdministrador() && in_array($r['estado'], ['pendiente','programada','en_proceso'], true)): ?>
                                <form method="post" action="tutorias_accion.php" class="d-flex align-items-center gap-1" onsubmit="return validarMotivoCancelacion(this);">
                                    <?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($r['id_tutoria'])?>">
                                    <input type="text" name="motivo_cancelacion" class="form-control form-control-sm" style="max-width:180px" minlength="5" maxlength="500" placeholder="Motivo" required>
                                    <button class="btn btn-sm btn-outline-danger" name="accion" value="cancelar" title="Cancelar"><i class="bi bi-slash-circle"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$registros): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-mortarboard fs-1 d-block mb-2"></i>No hay tutorías de proyectos de grado para mostrar.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (esAdministrador()): ?>
<div class="modal fade" id="confirmarPersonalModal" tabindex="-1" aria-labelledby="confirmarPersonalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Confirmación administrativa</span>
                    <h5 class="modal-title mb-0" id="confirmarPersonalModalLabel">Programar tutoría personal</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="tutorias_accion.php" id="formConfirmarPersonal">
                <?=csrfField()?>
                <input type="hidden" name="id_tutoria" id="confirmarTutoriaId">
                <input type="hidden" name="accion" value="confirmar_programacion">
                <div class="modal-body">
                    <div class="alert alert-light border mb-3" id="confirmarResumen">Selecciona una solicitud aceptada por el tutor.</div>
                    <label class="form-label" for="modalidadConfirmacion">Modalidad definitiva</label>
                    <select class="form-select mb-3" name="modalidad_confirmacion" id="modalidadConfirmacion" required>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                    <label class="form-label" for="lugarAdmin">Aula o enlace</label>
                    <input type="text" class="form-control" name="lugar_o_enlace_admin" id="lugarAdmin" maxlength="200" required placeholder="Ej.: Aula 204 o https://meet.google.com/...">
                    <div class="form-text">Este dato será visible para el estudiante y el tutor cuando la tutoría pase a Programada.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check me-1"></i>Confirmar y programar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
const confirmarModal = document.getElementById('confirmarPersonalModal');
if (confirmarModal) {
    confirmarModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('confirmarTutoriaId').value = button.dataset.tutoriaId || '';
        document.getElementById('confirmarResumen').innerHTML = '<strong>' + (button.dataset.estudiante || 'Estudiante') + '</strong><br>' + (button.dataset.fecha || '') + ' · ' + (button.dataset.horario || '');
        document.getElementById('modalidadConfirmacion').value = 'presencial';
        document.getElementById('lugarAdmin').value = '';
    });

    document.getElementById('modalidadConfirmacion').addEventListener('change', function () {
        const input = document.getElementById('lugarAdmin');
        input.placeholder = this.value === 'virtual' ? 'https://meet.google.com/...' : 'Ej.: Aula 204';
    });
}
</script>
<?php endif; ?>

<script>
function validarMotivoCancelacion(formulario) {
    const campo = formulario.querySelector('[name="motivo_cancelacion"]');
    const motivo = campo ? campo.value.trim() : '';
    if (motivo.length < 5) { alert('Indica un motivo de cancelación de al menos 5 caracteres.'); campo?.focus(); return false; }
    return confirm('La tutoría quedará registrada como cancelada. ¿Deseas continuar?');
}
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
