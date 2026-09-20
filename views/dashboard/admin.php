<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
  <div><span class="eyebrow">Gestión institucional</span><h1 class="page-title mb-1">Panel administrativo</h1><p class="text-muted mb-0">Supervisa usuarios, oferta académica y actividad del sistema de tutorías.</p></div>
  <div class="d-flex gap-2 flex-wrap"><a href="/controllers/reportes.php" class="btn btn-outline-primary"><i class="bi bi-bar-chart-line me-1"></i>Reportes</a><a href="/controllers/usuarios_crear.php" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nuevo usuario</a></div>
</div>
<div class="row g-3 mb-4">
<?php $cards=[['usuarios','Usuarios','people-fill','primary'],['estudiantes','Estudiantes','mortarboard-fill','success'],['tutores','Tutores','person-video3','info'],['carreras','Carreras','building','warning'],['materias','Materias','journal-bookmark-fill','secondary'],['tutorias','Tutorías','calendar-check-fill','dark']]; foreach($cards as $c): ?>
<div class="col-6 col-xl-2"><div class="metric-card h-100"><div class="metric-icon text-<?=$c[3]?> bg-<?=$c[3]?>-subtle"><i class="bi bi-<?=$c[2]?>"></i></div><div><div class="metric-value"><?=e($resumen[$c[0]])?></div><div class="metric-label"><?=e($c[1])?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-4">
  <div class="col-xl-7"><div class="card card-custom h-100 p-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Estado de las tutorías</h5><p class="text-muted small mb-0">Resumen general registrado.</p></div><a href="/controllers/tutorias_listar.php" class="small text-decoration-none">Ver todas</a></div>
  <?php $total=max(1,(int)$resumen['tutorias']); $estados=[['pendientes','Pendientes','warning'],['confirmadas','Confirmadas','primary'],['realizadas','Realizadas','success'],['canceladas','Canceladas','secondary']]; foreach($estados as $x): $por=(int)round(($resumen[$x[0]]/$total)*100); ?>
  <div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span><?=$x[1]?></span><strong><?=$resumen[$x[0]]?></strong></div><div class="progress" style="height:9px"><div class="progress-bar bg-<?=$x[2]?>" style="width:<?=$por?>%"></div></div></div>
  <?php endforeach; ?></div></div>
  <div class="col-xl-5"><div class="card card-custom h-100 p-4"><div class="text-center py-3"><div class="display-5 fw-bold text-warning"><i class="bi bi-star-fill me-2"></i><?=number_format($resumen['promedio'],1)?></div><div class="text-muted">Promedio general de evaluación</div></div><hr><div class="row text-center"><div class="col-4"><div class="h4 fw-bold mb-0"><?=$resumen['pendientes']?></div><small class="text-muted">Pendientes</small></div><div class="col-4"><div class="h4 fw-bold mb-0"><?=$resumen['confirmadas']?></div><small class="text-muted">Confirmadas</small></div><div class="col-4"><div class="h4 fw-bold mb-0"><?=$resumen['realizadas']?></div><small class="text-muted">Realizadas</small></div></div></div></div>
</div>
<div class="row g-3 mt-1">
<?php $quick=[['Carreras','/controllers/carreras_listar.php','mortarboard'],['Materias','/controllers/materias_listar.php','journal-bookmark'],['Estudiantes','/controllers/estudiantes_listar.php','people'],['Tutores','/controllers/tutores_listar.php','person-video3'],['Horarios','/controllers/disponibilidad_listar.php','calendar-week'],['Evaluaciones','/controllers/evaluaciones_listar.php','star']]; foreach($quick as $q): ?><div class="col-6 col-md-4 col-xl-2"><a class="quick-link" href="<?=$q[1]?>"><i class="bi bi-<?=$q[2]?>"></i><span><?=$q[0]?></span><i class="bi bi-arrow-right ms-auto"></i></a></div><?php endforeach; ?>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
