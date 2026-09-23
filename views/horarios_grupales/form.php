<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4"><a href="horarios_grupales_listar.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a horarios</a><span class="eyebrow d-block mt-3">Administración universitaria</span><h1 class="page-title mb-1"><?=str_contains($tituloPagina,'Editar')?'Editar':'Nuevo'?> horario grupal</h1><p class="text-muted mb-0">El sistema solo permite los tres bloques oficiales de lunes a viernes.</p></div>
<?php if($errores): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errores as $err): ?><li><?=e($err)?></li><?php endforeach;?></ul></div><?php endif; ?>
<div class="card card-custom p-4">
  <form method="post" id="formHorarioGrupal"><?=csrfField()?>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Materia</label>
        <select name="id_materia" class="form-select tom-select" required>
          <option value="">Seleccionar materia</option>
          <?php foreach($materias as $m): ?><option value="<?=e($m['id_materia'])?>" <?=((string)($datos['id_materia']??'')===(string)$m['id_materia'])?'selected':''?>><?=e($m['nombre_materia'])?> — <?=e($m['nombre_carrera'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Tutor</label>
        <select name="id_tutor" id="id_tutor" class="form-select tom-select" required>
          <option value="">Seleccionar tutor</option>
          <?php foreach($tutores as $t): ?><option value="<?=e($t['id_tutor'])?>" <?=((string)($datos['id_tutor']??'')===(string)$t['id_tutor'])?'selected':''?>><?=e(trim($t['apellido'].' '.$t['nombre']))?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Día</label>
        <select name="dia_semana" id="dia_semana" class="form-select" required>
          <option value="">Seleccionar</option>
          <?php foreach($dias as $d): ?><option value="<?=e($d)?>" <?=($datos['dia_semana']??'')===$d?'selected':''?>><?=e($d)?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Bloque universitario</label>
        <select name="turno" id="turno" class="form-select" required data-inicial="<?=e($datos['turno']??'')?>">
          <option value=""><?=($datos['id_tutor'] ?? '') && ($datos['dia_semana'] ?? '') ? 'Seleccionar bloque disponible' : 'Selecciona tutor y día primero'?></option>
          <?php foreach($turnosDisponibles as $k=>$t): ?>
            <?php
              $etiquetasTurno = ['manana'=>'Mañana','tarde'=>'Tarde','noche'=>'Noche'];
              $etiquetaTurno = ($etiquetasTurno[$k] ?? ucfirst($k)) . ' (' . substr((string)$t['inicio'], 0, 5) . ' - ' . substr((string)$t['fin'], 0, 5) . ')';
            ?>
            <option value="<?=e($k)?>" <?=($datos['turno']??'')===$k?'selected':''?>><?=e($etiquetaTurno)?></option>
          <?php endforeach; ?>
        </select>
        <div class="form-text" id="ayudaTurno">El sistema mostrará únicamente los bloques que el tutor todavía tenga libres ese día.</div>
      </div>
      <?php if(str_contains($tituloPagina,'Editar')):?><div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><option value="activo" <?=($datos['estado']??'')==='activo'?'selected':''?>>Activo</option><option value="inactivo" <?=($datos['estado']??'')==='inactivo'?'selected':''?>>Inactivo</option></select></div><?php endif;?>
    </div>
    <div class="alert alert-info border-0 mt-4 mb-0"><i class="bi bi-info-circle me-2"></i>Después de seleccionar tutor y día, solo aparecerán los bloques oficiales que estén libres para ese docente.</div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="horarios_grupales_listar.php" class="btn btn-light">Cancelar</a><button class="btn btn-primary">Guardar horario</button></div>
  </form>
</div>
<script>
(function(){
  const tutor = document.getElementById('id_tutor');
  const dia = document.getElementById('dia_semana');
  const turno = document.getElementById('turno');
  const ayuda = document.getElementById('ayudaTurno');
  if (!tutor || !dia || !turno) return;

  const bloques = {
    manana: {inicio:'07:00', fin:'10:00', etiqueta:'Mañana (07:00 - 10:00)'},
    tarde:  {inicio:'15:00', fin:'18:00', etiqueta:'Tarde (15:00 - 18:00)'},
    noche:  {inicio:'19:00', fin:'22:00', etiqueta:'Noche (19:00 - 22:00)'}
  };
  const ocupados = <?=json_encode($horariosOcupados ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
  const turnoInicial = turno.dataset.inicial || '';
  const esEdicion = <?=str_contains($tituloPagina,'Editar') ? 'true' : 'false'?>;
  const idActual = <?=isset($id) ? (int)$id : 'null'?>;

  function obtenerOcupados(idTutor, diaSemana){
    const conjunto = new Set();
    ocupados.forEach(function(row){
      if (String(row.id_tutor) === String(idTutor) && row.dia_semana === diaSemana) {
        if (esEdicion && idActual !== null && String(row.id_horario) === String(idActual)) return;
        conjunto.add(row.turno);
      }
    });
    return conjunto;
  }

  function refrescarTurnos(){
    const valorAnterior = turno.value || turnoInicial;
    const idTutor = tutor.value;
    const diaSemana = dia.value;
    turno.innerHTML = '';

    if (!idTutor || !diaSemana) {
      turno.add(new Option('Selecciona tutor y día primero', ''));
      turno.value = '';
      ayuda.textContent = 'Selecciona un tutor y un día para consultar sus bloques libres.';
      return;
    }

    const ocupadosDia = obtenerOcupados(idTutor, diaSemana);
    let cantidad = 0;

    Object.entries(bloques).forEach(function([clave, bloque]){
      if (ocupadosDia.has(clave)) return;
      const option = new Option(bloque.etiqueta, clave);
      if (clave === valorAnterior) option.selected = true;
      turno.add(option);
      cantidad++;
    });

    if (cantidad === 0) {
      turno.add(new Option('No hay bloques libres para este día', ''));
      turno.value = '';
      ayuda.textContent = 'El tutor ya tiene ocupados los tres bloques oficiales en este día.';
      return;
    }

    if (![...turno.options].some(o => o.value === turno.value && o.value !== '')) turno.selectedIndex = 1;
    ayuda.textContent = cantidad === 1
      ? 'Hay 1 bloque oficial disponible para este tutor en el día seleccionado.'
      : 'Se muestran únicamente los ' + cantidad + ' bloques oficiales que el tutor tiene libres.';
  }

  tutor.addEventListener('change', refrescarTurnos);
  dia.addEventListener('change', refrescarTurnos);
  refrescarTurnos();
})();
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
