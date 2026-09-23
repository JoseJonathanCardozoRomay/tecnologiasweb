<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4">
  <a href="tutorias_grupales_listar.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a sesiones</a>
  <span class="eyebrow d-block mt-3">Agenda institucional</span>
  <h1 class="page-title mb-1">Nueva tutoría grupal</h1>
  <p class="text-muted mb-0">Selecciona la materia, el docente, la fecha y un horario institucional realmente disponible.</p>
</div>
<?php if($errores): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errores as $err): ?><li><?=e($err)?></li><?php endforeach;?></ul></div>
<?php endif; ?>
<div class="card card-custom p-4">
  <form method="post" id="formTutoriasGrupal">
    <?=csrfField()?>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Materia</label>
        <select name="id_materia" id="id_materia" class="form-select tom-select" required>
          <option value="">Seleccionar materia</option>
          <?php foreach($materias as $materia): ?>
            <option value="<?=e($materia['id_materia'])?>" <?=((string)($datos['id_materia']??'')===(string)$materia['id_materia'])?'selected':''?>><?=e($materia['nombre_materia'])?> · <?=e($materia['nombre_carrera'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Docente / tutor</label>
        <select name="id_tutor" id="id_tutor" class="form-select tom-select" required>
          <option value="">Seleccionar docente</option>
          <?php foreach($tutores as $tutor): ?>
            <option value="<?=e($tutor['id_tutor'])?>" <?=((string)($datos['id_tutor']??'')===(string)$tutor['id_tutor'])?'selected':''?>><?=e(trim($tutor['nombre'].' '.$tutor['apellido']))?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Fecha de sesión</label>
        <input type="date" class="form-control" name="fecha" id="fecha" required value="<?=e($datos['fecha'])?>">
        <div class="form-text">La fecha debe corresponder a un día de lunes a viernes.</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Horario disponible</label>
        <select name="turno" id="bloque_horario" class="form-select" required disabled>
          <option value="">Selecciona materia, docente y fecha</option>
        </select>
        <input type="hidden" name="dia_semana" id="dia_semana" value="<?=e($datos['dia_semana'])?>">
        <div class="form-text" id="horarioAyuda">Solo aparecerán los bloques institucionales que el docente tenga disponibles para esa materia y día.</div>
      </div>
      <div class="col-12">
        <label class="form-label">Observaciones</label>
        <textarea class="form-control" name="observaciones" rows="4" maxlength="2000"><?=e($datos['observaciones'])?></textarea>
      </div>
    </div>
    <div class="alert alert-info border-0 mt-4 mb-0">
      <i class="bi bi-info-circle me-1"></i> El sistema comprueba nuevamente en el servidor que el tutor no tenga otro horario asignado en ese bloque y que no exista otra sesión para la fecha elegida.
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="tutorias_grupales_listar.php" class="btn btn-light">Cancelar</a>
      <button class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Agregar tutoría</button>
    </div>
  </form>
</div>
<script>
(function(){
  const materia = document.getElementById('id_materia');
  const tutor = document.getElementById('id_tutor');
  const fecha = document.getElementById('fecha');
  const select = document.getElementById('bloque_horario');
  const dia = document.getElementById('dia_semana');
  const ayuda = document.getElementById('horarioAyuda');
  if (!materia || !tutor || !fecha || !select || !dia) return;

  async function cargarHorarios(){
    select.innerHTML = '<option value="">Cargando horarios...</option>';
    select.disabled = true;
    dia.value = '';

    if (!materia.value || !tutor.value || !fecha.value) {
      select.innerHTML = '<option value="">Selecciona materia, docente y fecha</option>';
      ayuda.textContent = 'Solo aparecerán los bloques institucionales que el docente tenga disponibles para esa materia y día.';
      return;
    }

    try {
      const url = 'api_horarios_grupales_disponibles.php?id_materia=' + encodeURIComponent(materia.value)
        + '&id_tutor=' + encodeURIComponent(tutor.value)
        + '&fecha=' + encodeURIComponent(fecha.value);
      const respuesta = await fetch(url, {headers: {'Accept': 'application/json'}});
      const datosApi = await respuesta.json();

      if (!datosApi.ok) throw new Error(datosApi.message || 'No se pudieron consultar los horarios.');

      dia.value = datosApi.dia || '';
      select.innerHTML = '';
      const actual = <?=json_encode((string)($datos['turno'] ?? ''))?>;

      if (!datosApi.slots || datosApi.slots.length === 0) {
        select.innerHTML = '<option value="">No hay horarios disponibles</option>';
        ayuda.textContent = datosApi.message || 'No existe un bloque disponible para la combinación seleccionada.';
        return;
      }

      select.appendChild(new Option('Seleccionar horario', ''));
      datosApi.slots.forEach(function(slot){
        const texto = slot.hora_inicio + '–' + slot.hora_fin + ' · ' + slot.turno.charAt(0).toUpperCase() + slot.turno.slice(1);
        const option = new Option(texto, slot.turno);
        if (slot.turno === actual) option.selected = true;
        select.appendChild(option);
      });
      select.disabled = false;
      ayuda.textContent = 'Horarios libres para ' + datosApi.dia + '. Los bloques ocupados por otra materia del tutor no aparecen.';
    } catch (error) {
      select.innerHTML = '<option value="">No se pudieron cargar los horarios</option>';
      ayuda.textContent = error.message || 'No se pudieron consultar los horarios disponibles.';
    }
  }

  materia.addEventListener('change', cargarHorarios);
  tutor.addEventListener('change', cargarHorarios);
  fecha.addEventListener('change', cargarHorarios);
  cargarHorarios();
})();
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
