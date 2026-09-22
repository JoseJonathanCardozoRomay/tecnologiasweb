<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
        <a href="tutorias_grupales_listar.php" class="text-decoration-none">&larr; Volver a tutorías grupales</a>
        <div class="eyebrow mt-3">Participantes</div>
        <h1 class="page-title mb-1">Estudiantes inscritos</h1>
        <p class="text-muted mb-0">
            <?=e($sesion['nombre_materia'])?> · <?=e($sesion['dia_semana'])?> ·
            <?=e(substr($sesion['hora_inicio'],0,5))?>–<?=e(substr($sesion['hora_fin'],0,5))?>
        </p>
    </div>
    <span class="badge text-bg-<?=match($sesion['estado']){'programada'=>'primary','en_curso'=>'warning','realizada'=>'success','cancelada'=>'danger',default=>'secondary'}?> align-self-start fs-6">
        <?=e(ucwords(str_replace('_',' ',$sesion['estado'])))?>
    </span>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card card-custom overflow-hidden">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h5 class="fw-bold mb-1">Participantes de la sesión</h5><p class="small text-muted mb-0">Los registros conservan su estado para mantener el historial.</p></div>
                    <span class="badge text-bg-light border fs-6"><i class="bi bi-people me-1"></i><?=count(array_filter($estudiantes, fn($x) => in_array($x['estado'], ['inscrito','asistio'], true)))?></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Estudiante</th><th>Registro</th><th>Carrera</th><th>Semestre</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                    <?php foreach($estudiantes as $e): ?>
                        <tr>
                            <td><strong><?=e(trim($e['apellido'].' '.$e['nombre']))?></strong><div class="small text-muted"><?=e($e['correo'])?></div></td>
                            <td><?=e($e['registro_universitario'] ?: '—')?></td>
                            <td><?=e($e['nombre_carrera'])?></td>
                            <td><?=e($e['semestre'])?></td>
                            <td><span class="badge text-bg-<?=match($e['estado']){'inscrito'=>'primary','asistio'=>'success','no_asistio'=>'danger','retirado'=>'secondary',default=>'light'}?>"><?=e(ucwords(str_replace('_',' ',$e['estado'])))?></span></td>
                            <td class="text-end">
                                <?php if(esAdministrador() && $sesion['estado']==='programada' && $e['estado']==='inscrito'): ?>
                                    <form method="post" action="tutorias_grupales_estudiante_admin_accion.php" onsubmit="return confirm('¿Retirar a este estudiante de la sesión?');">
                                        <?=csrfField()?>
                                        <input type="hidden" name="id_tutoria_grupal" value="<?=e($id)?>">
                                        <input type="hidden" name="id_estudiante" value="<?=e($e['id_estudiante'])?>">
                                        <button class="btn btn-sm btn-outline-danger" name="accion" value="retirar">Retirar</button>
                                    </form>
                                <?php elseif(esTutor() && in_array($sesion['estado'],['en_curso','realizada'],true) && in_array($e['estado'],['inscrito','asistio'],true)): ?>
                                    <form method="post" action="tutorias_grupales_asistencia.php" class="d-inline">
                                        <?=csrfField()?>
                                        <input type="hidden" name="id_tutoria_grupal" value="<?=e($id)?>">
                                        <input type="hidden" name="id_estudiante" value="<?=e($e['id_estudiante'])?>">
                                        <button class="btn btn-sm btn-outline-success" name="estado" value="asistio">Asistió</button>
                                        <button class="btn btn-sm btn-outline-danger" name="estado" value="no_asistio">No asistió</button>
                                    </form>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(!$estudiantes): ?><tr><td colspan="6" class="text-center py-5 text-muted">Todavía no hay estudiantes inscritos.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if(esAdministrador() && $sesion['estado']==='programada'): ?>
    <div class="col-xl-4">
        <div class="card card-custom p-4">
            <h5 class="fw-bold">Agregar estudiante</h5>
            <p class="small text-muted">Solo aparecen estudiantes activos que no estén inscritos y no tengan otro grupo en conflicto.</p>
            <form method="post" action="tutorias_grupales_estudiante_admin_accion.php" class="d-grid gap-3">
                <?=csrfField()?>
                <input type="hidden" name="id_tutoria_grupal" value="<?=e($id)?>">
                <select name="id_estudiante" class="form-select" required>
                    <option value="">Seleccionar estudiante</option>
                    <?php foreach($disponibles as $d): ?>
                        <option value="<?=e($d['id_estudiante'])?>"><?=e($d['apellido'].' '.$d['nombre'].' — '.$d['registro_universitario'])?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" name="accion" value="inscribir"><i class="bi bi-person-plus me-1"></i>Inscribir estudiante</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
