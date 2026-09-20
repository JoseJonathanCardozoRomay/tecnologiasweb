<?php require_once __DIR__.'/../layouts/header.php';$edit=isset($actual); ?>
<div class="row justify-content-center"><div class="col-xxl-9"><div class="mb-4"><span class="eyebrow"><?= $edit?'Administración':'Solicitud académica' ?></span><h1 class="page-title mb-1"><?= $edit?'Editar tutoría':'Solicitar una tutoría' ?></h1><p class="text-muted mb-0"><?= $edit?'Actualiza una sesión desde administración.':'Selecciona una materia, tutor y horario realmente disponible.' ?></p></div><?php if($errores):?><div class="alert alert-danger"><strong>No se pudo guardar:</strong><ul class="mb-0 mt-2"><?php foreach($errores as $x):?><li><?=e($x)?></li><?php endforeach;?></ul></div><?php endif;?><div class="card card-custom p-4"><form method="POST" id="tutoriaForm"><?=csrfField()?><input type="hidden" name="id_tutoria" value="<?=e($actual['id_tutoria']??'')?>"><div class="row g-3"><?php if(esAdministrador()):?><div class="col-md-4"><label class="form-label fw-semibold">Estudiante *</label><select name="id_estudiante" id="id_estudiante" class="form-select" required><option value="">Seleccionar...</option><?php foreach($estudiantes as $x):?><option value="<?=$x['id_estudiante']?>" data-carrera="<?=$x['id_carrera']??''?>" <?=((string)($datos['id_estudiante']??'')===(string)$x['id_estudiante'])?'selected':''?>><?=e(($x['nombre']??'').' '.($x['apellido']??''))?></option><?php endforeach;?></select></div><?php else:?><input type="hidden" name="id_estudiante" value="<?=e($datos['id_estudiante']??'')?>"><div class="col-md-4"><label class="form-label fw-semibold">Estudiante</label><div class="form-control bg-light"><?=e(($estudiantes[0]['nombre']??$_SESSION['nombre']??'').' '.($estudiantes[0]['apellido']??''))?></div></div><?php endif;?><div class="col-md-4"><label class="form-label fw-semibold">Materia *</label><select name="id_materia" id="id_materia" class="form-select" required><option value="">Seleccionar...</option><?php foreach($materias as $x):?><option value="<?=$x['id_materia']?>" <?=((string)($datos['id_materia']??'')===(string)$x['id_materia'])?'selected':''?>><?=e($x['nombre_materia'])?></option><?php endforeach;?></select></div><div class="col-md-4"><label class="form-label fw-semibold">Tutor *</label><select name="id_tutor" id="id_tutor" class="form-select" required><option value="">Primero selecciona una materia...</option><?php foreach($tutores as $x):?><option value="<?=$x['id_tutor']?>" <?=((string)($datos['id_tutor']??'')===(string)$x['id_tutor'])?'selected':''?>><?=e(($x['nombre']??$x['nombre']??'').' '.($x['apellido']??''))?></option><?php endforeach;?></select><div id="tutorHelp" class="form-text">Solo aparecerán tutores asignados a la materia.</div></div><div class="col-md-4"><label class="form-label fw-semibold">Fecha *</label><input type="date" name="fecha" id="fecha" class="form-control" min="<?=date('Y-m-d')?>" value="<?=e($datos['fecha']??'')?>" required></div><div class="col-md-4"><label class="form-label fw-semibold">Horario disponible *</label><select id="slot" class="form-select" required><option value="">Selecciona tutor y fecha...</option></select><input type="hidden" name="hora_inicio" id="hora_inicio" value="<?=e($datos['hora_inicio']??'')?>"><input type="hidden" name="hora_fin" id="hora_fin" value="<?=e($datos['hora_fin']??'')?>"><div id="slotHelp" class="form-text">Las sesiones se generan en bloques de 60 minutos.</div></div><div class="col-md-4"><label class="form-label fw-semibold">Modalidad *</label><select name="modalidad" id="modalidad" class="form-select"><option value="presencial" <?=($datos['modalidad']??'presencial')==='presencial'?'selected':''?>>Presencial</option><option value="virtual" <?=($datos['modalidad']??'')==='virtual'?'selected':''?>>Virtual</option></select></div><div class="col-md-8"><label class="form-label fw-semibold" id="lugarLabel">Lugar o enlace</label><input name="lugar_o_enlace" id="lugar_o_enlace" maxlength="200" class="form-control" value="<?=e($datos['lugar_o_enlace']??'')?>" placeholder="Ej.: Aula 204 o https://meet.google.com/..."><div class="form-text" id="lugarHelp">En presencial escribe el aula o lugar. En virtual puedes colocar el enlace.</div></div><div class="col-12"><label class="form-label fw-semibold">Observaciones</label><textarea name="observaciones" maxlength="1000" rows="4" class="form-control" placeholder="Tema que deseas reforzar, dudas o indicaciones adicionales."><?=e($datos['observaciones']??'')?></textarea></div></div><div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"><a href="tutorias_listar.php" class="btn btn-light">Cancelar</a><button class="btn btn-primary"><i class="bi bi-send me-1"></i><?= $edit?'Actualizar tutoría':'Enviar solicitud' ?></button></div></form></div></div></div>
<script>
const materia=document.getElementById('id_materia'), tutor=document.getElementById('id_tutor'), fecha=document.getElementById('fecha'), slot=document.getElementById('slot'), hi=document.getElementById('hora_inicio'), hf=document.getElementById('hora_fin');
// Los tutores ya fueron cargados por PHP y se filtran en el navegador por materia.
// Así no dependemos de una llamada AJAX que pueda devolver una página de error.
const tutoresPorMateria=<?=json_encode($tutoresPorMateria??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const tutorInicial='<?=e((string)($datos['id_tutor']??''))?>';
function resetSlots(msg='Selecciona tutor y fecha...'){slot.innerHTML='<option value="">'+msg+'</option>';hi.value='';hf.value='';}
function cargarTutores(){
    const id=String(materia.value||'');
    tutor.innerHTML='<option value="">Seleccionar tutor...</option>';
    resetSlots();
    if(!id){
        tutor.innerHTML='<option value="">Primero selecciona una materia...</option>';
        return;
    }
    const lista=tutoresPorMateria[id]||[];
    if(!lista.length){
        tutor.innerHTML='<option value="">No hay tutores disponibles</option>';
        return;
    }
    lista.forEach(x=>{
        const o=document.createElement('option');
        o.value=String(x.id_tutor);
        o.textContent=x.nombre;
        if(tutorInicial===String(x.id_tutor))o.selected=true;
        tutor.appendChild(o);
    });
    if(tutor.value&&fecha.value)cargarSlots();
}
async function cargarSlots(){
    if(!tutor.value||!fecha.value){resetSlots();return;}
    resetSlots('Consultando disponibilidad...');
    try{
        const url='/controllers/api_horarios_disponibles.php?id_tutor='+encodeURIComponent(tutor.value)+'&id_materia='+encodeURIComponent(materia.value)+'&fecha='+encodeURIComponent(fecha.value);
        const r=await fetch(url,{credentials:'same-origin',headers:{'Accept':'application/json'},cache:'no-store'});
        const texto=await r.text();
        let j;
        try{j=JSON.parse(texto);}catch(_){throw new Error('Respuesta inválida del servidor (HTTP '+r.status+').');}
        if(!j.ok){resetSlots(j.message||'No disponible');return;}
        if(!j.slots.length){resetSlots('No hay horarios disponibles');return;}
        slot.innerHTML='<option value="">Seleccionar horario...</option>';
        j.slots.forEach(x=>{const o=document.createElement('option');o.value=x.hora_inicio+'|'+x.hora_fin;o.textContent=x.etiqueta;if(hi.value===x.hora_inicio&&hf.value===x.hora_fin)o.selected=true;slot.appendChild(o);});
        if(!slot.value&&hi.value&&hf.value){const o=document.createElement('option');o.value=hi.value+'|'+hf.value;o.textContent=hi.value.slice(0,5)+' - '+hf.value.slice(0,5);o.selected=true;slot.appendChild(o);}
    }catch(e){resetSlots('No se pudo consultar disponibilidad');console.error('Error consultando horarios:',e);}
}
materia?.addEventListener('change',()=>{cargarTutores();});
tutor?.addEventListener('change',cargarSlots);
fecha?.addEventListener('change',cargarSlots);
slot?.addEventListener('change',()=>{const v=slot.value.split('|');hi.value=v[0]||'';hf.value=v[1]||'';});
document.addEventListener('DOMContentLoaded',()=>{if(materia.value)cargarTutores();});
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
