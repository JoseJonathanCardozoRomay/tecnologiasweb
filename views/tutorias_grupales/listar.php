<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
  <div><span class="eyebrow">Tutoría de materias</span><h1 class="page-title mb-1">Tutorías grupales</h1><p class="text-muted mb-0">Sesiones basadas exclusivamente en el horario oficial de la universidad.</p></div>
  <?php if(esAdministrador()): ?><a href="tutorias_grupales_crear.php" class="btn btn-sm btn-outline-secondary page-header-compact-action"><i class="bi bi-calendar-plus me-1"></i>Agregar tutoría</a><?php endif;?>
</div>
<div class="card card-custom p-3 mb-3">
  <form method="get" class="d-flex gap-2">
    <select class="form-select" name="estado" style="max-width:250px"><option value="">Todos los estados</option><?php foreach(['programada','en_curso','realizada','cancelada'] as $s): ?><option value="<?=e($s)?>" <?=($estado??'')===$s?'selected':''?>><?=e(ucwords(str_replace('_',' ',$s)))?></option><?php endforeach;?></select>
    <button class="btn btn-outline-primary">Filtrar</button>
  </form>
</div>
<div class="card card-custom overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Fecha</th><th>Materia</th><th>Tutor</th><th>Horario</th><th>Estudiantes</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody><?php foreach($registros as $r): ?><tr><td><strong><?=e(date('d/m/Y',strtotime($r['fecha'])))?></strong></td><td><?=e($r['nombre_materia'])?><div class="small text-muted"><?=e($r['nombre_carrera'])?></div></td><td><?=e(trim($r['tutor_nombre'].' '.$r['tutor_apellido']))?></td><td><?=e($r['dia_semana'])?><div class="small text-muted"><?=e(substr($r['hora_inicio'],0,5))?>–<?=e(substr($r['hora_fin'],0,5))?> · <?=e(ucfirst($r['turno']))?></div></td><td><span class="badge text-bg-light border"><i class="bi bi-people me-1"></i><?=e($r['total_estudiantes'])?></span></td><td><?php if(esEstudiante()): $participacion=$participaciones[(int)$r['id_tutoria_grupal']]??null; $estadoMostrar=$participacion!==null?$participacion:$r['estado']; ?><span class="badge text-bg-<?=estadoBadge((string)$estadoMostrar)?>"><?=e(estadoEtiqueta((string)$estadoMostrar))?></span><?php if($participacion!==null && $participacion==='pendiente'): ?><div class="small text-muted mt-1">No programada para ti</div><?php endif; ?><?php else: ?><span class="badge text-bg-<?=estadoBadge((string)$r['estado'])?>"><?=e(estadoEtiqueta((string)$r['estado']))?></span><?php endif; ?></td><td class="text-end"><div class="d-flex justify-content-end gap-1"><?php if(esEstudiante()): $participacion=$participaciones[(int)$r['id_tutoria_grupal']]??null; ?><div class="d-flex justify-content-end align-items-center gap-2"><?php if($participacion==='pendiente'): ?><form method="post" action="tutorias_grupales_estudiante_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button name="accion" value="retirarse" class="btn btn-sm btn-outline-danger">Retirarme</button></form><?php elseif(in_array($participacion,['aprobada','inscrito','asistio'],true)): ?><span class="small text-muted">Sin acciones</span><?php elseif($participacion==='retirado' && $r['estado']==='programada' && $r['estado_horario']==='activo'): ?><form method="post" action="tutorias_grupales_estudiante_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button name="accion" value="inscribirse" class="btn btn-sm btn-outline-primary">Inscribirme</button></form><?php elseif($r['estado']==='programada' && $r['estado_horario']==='activo'): ?><form method="post" action="tutorias_grupales_estudiante_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button name="accion" value="inscribirse" class="btn btn-sm btn-outline-primary">Inscribirme</button></form><?php else: ?><span class="small text-muted">Sin acciones</span><?php endif; ?></div><?php else: ?><a href="tutorias_grupales_estudiantes.php?id=<?=e($r['id_tutoria_grupal'])?>" class="btn btn-sm btn-outline-secondary" title="Ver estudiantes"><i class="bi bi-people"></i><span class="d-none d-xl-inline ms-1">Estudiantes</span></a><?php if(esTutor() && $r['estado']==='programada'): ?><form method="post" action="tutorias_grupales_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button name="accion" value="iniciar" class="btn btn-sm btn-outline-primary">Iniciar</button></form><?php endif;?><?php if(esTutor() && $r['estado']==='en_curso'): ?><form method="post" action="tutorias_grupales_accion.php"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button name="accion" value="realizar" class="btn btn-sm btn-outline-success">Finalizar</button></form><?php endif;?><?php if(esAdministrador() && in_array($r['estado'],['programada','en_curso'],true)): ?><form method="post" action="tutorias_grupales_accion.php" class="d-inline cancel-grupal-form"><?=csrfField()?><input type="hidden" name="id_tutoria_grupal" value="<?=e($r['id_tutoria_grupal'])?>"><button type="button" class="btn btn-sm btn-outline-danger" title="Cancelar tutoría" onclick="confirmarCancelacionGrupal(this.form)"><i class="bi bi-slash-circle"></i></button></form><?php endif;?><?php endif;?></div></td></tr><?php endforeach;?><?php if(!$registros): ?><tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>No hay sesiones grupales para mostrar.</td></tr><?php endif;?></tbody></table></div></div>
<script>
function confirmarCancelacionGrupal(form){
  if (!window.Swal) { form.submit(); return; }
  Swal.fire({
    title: 'Cancelar tutoría',
    text: 'La cancelación quedará registrada en el historial.',
    input: 'textarea',
    inputLabel: 'Motivo de cancelación',
    inputPlaceholder: 'Escribe el motivo (mínimo 5 caracteres)',
    inputAttributes: {maxlength: 500, 'aria-label': 'Motivo de cancelación'},
    showCancelButton: true,
    confirmButtonText: 'Cancelar tutoría',
    cancelButtonText: 'Volver',
    reverseButtons: true,
    customClass: {popup:'swal-upds-popup'},
    inputValidator: value => !value || value.trim().length < 5 ? 'El motivo es obligatorio (mínimo 5 caracteres).' : undefined
  }).then(result => {
    if (!result.isConfirmed) return;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'motivo_cancelacion';
    input.value = result.value.trim();
    form.appendChild(input);
    const submit = document.createElement('input');
    submit.type = 'hidden';
    submit.name = 'accion';
    submit.value = 'cancelar';
    form.appendChild(submit);
    form.submit();
  });
}
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
