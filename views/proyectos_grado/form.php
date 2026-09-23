<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4"><a href="proyectos_grado_listar.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a proyectos</a><span class="eyebrow d-block mt-3">Proyecto de grado</span><h1 class="page-title mb-1"><?=str_contains($tituloPagina,'Editar')?'Editar':'Nuevo'?> proyecto de grado</h1><p class="text-muted mb-0">El proyecto es la referencia académica de las tutorías personales.</p></div>
<?php if($errores): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errores as $err): ?><li><?=e($err)?></li><?php endforeach;?></ul></div><?php endif; ?>
<div class="card card-custom p-4"><form method="post">
<?=csrfField()?>
<div class="alert alert-light border">
  <strong>Estudiante:</strong> <?=e(trim(($estudiantes[0]['nombre']??'').' '.($estudiantes[0]['apellido']??'')))?>
  · <strong>Carrera:</strong> <?=e($carreras[0]['nombre_carrera']??'')?>
  <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>El proyecto inicia como <strong>Propuesto</strong>. Pasará a <strong>En curso</strong> cuando un tutor acepte la solicitud y administración confirme la programación.</div>
</div>
<input type="hidden" name="id_estudiante" value="<?=e($datos['id_estudiante'])?>">
<input type="hidden" name="id_carrera" value="<?=e($datos['id_carrera'])?>">
<div class="row g-3 mt-1"><div class="col-12"><label class="form-label">Título</label><input class="form-control" name="titulo" maxlength="200" required value="<?=e($datos['titulo'])?>"></div><div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="5" maxlength="3000" placeholder="Describe brevemente el proyecto..."><?=e($datos['descripcion'])?></textarea></div><?php if(esAdministrador() && isset($estados)): ?><div class="col-md-4"><label class="form-label">Estado</label><select class="form-select" name="estado"><?php foreach($estados as $s): ?><option value="<?=e($s)?>" <?=($datos['estado']??'')===$s?'selected':''?>><?=e(ucwords(str_replace('_',' ',$s)))?></option><?php endforeach;?></select></div><?php endif;?></div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="proyectos_grado_listar.php" class="btn btn-light">Cancelar</a><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Guardar proyecto</button></div></form></div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
