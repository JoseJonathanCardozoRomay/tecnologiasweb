<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="mb-4">
  <div><span class="eyebrow">Gestión institucional</span><h1 class="page-title mb-1">Panel administrativo</h1><p class="text-muted mb-0">Supervisa usuarios, oferta académica, horarios y las dos modalidades de tutoría.</p></div>
</div>

<div class="row g-3 mb-4">
<?php $cards=[['usuarios','Usuarios','people-fill','primary'],['estudiantes','Estudiantes','mortarboard-fill','success'],['tutores','Tutores','person-video3','info'],['carreras','Carreras','building','warning'],['materias','Materias','journal-bookmark-fill','secondary'],['tutorias','Tutorías','calendar-check-fill','dark'],['tutorias_grupales','Sesiones grupales','people','primary'],['tutorias_personales','Tutorías personales','person-workspace','success']]; foreach($cards as $c): ?>
<div class="col-6 col-md-4 col-xl-3 "><div class="metric-card h-100"><div class="metric-icon text-<?=$c[3]?> bg-<?=$c[3]?>-subtle"><i class="bi bi-<?=$c[2]?>"></i></div><div><div class="metric-value"><?=e($resumen[$c[0]])?></div><div class="metric-label"><?=e($c[1])?></div></div></div></div>
<?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-xl-5">
    <div class="card card-custom h-100 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Modalidades</h5><p class="text-muted small mb-0">Sesiones registradas por tipo.</p></div></div>
      <div style="height:280px"><canvas id="chartTipos" aria-label="Tutorías por tipo"></canvas></div>
      <div class="row text-center mt-3"><div class="col-6"><div class="h4 fw-bold mb-0"><?=e($resumen['tutorias_personales'])?></div><small class="text-muted">Tutorías personales</small></div><div class="col-6"><div class="h4 fw-bold mb-0"><?=e($resumen['tutorias_grupales'])?></div><small class="text-muted">Materias grupales</small></div></div>
    </div>
  </div>
  <div class="col-xl-7">
    <div class="card card-custom h-100 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Estado de tutorías</h5><p class="text-muted small mb-0">Incluye sesiones personales y grupales.</p></div><span class="badge text-bg-light border">Total: <?=e($resumen['tutorias'])?></span></div>
      <div style="height:280px"><canvas id="chartEstados" aria-label="Tutorías por estado"></canvas></div>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <div class="col-xl-7"><div class="card card-custom p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Actividad por materia</h5><p class="text-muted small mb-0">Sesiones personales y grupales acumuladas.</p></div><a href="/controllers/materias_listar.php" class="small text-decoration-none">Gestionar materias</a></div><div style="height:320px"><canvas id="chartMaterias" aria-label="Tutorías por materia"></canvas></div></div></div>
  <div class="col-xl-5"><div class="card card-custom p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Actividad por tutor</h5><p class="text-muted small mb-0">Sesiones asociadas a cada tutor.</p></div><a href="/controllers/tutores_listar.php" class="small text-decoration-none">Ver tutores</a></div><div style="height:320px"><canvas id="chartTutores" aria-label="Tutorías por tutor"></canvas></div></div></div>
</div>

<div class="row g-3 mt-1">
<?php $quick=[['Horarios de materias','/controllers/horarios_grupales_listar.php','clock'],['Tutorías grupales','/controllers/tutorias_grupales_listar.php','people'],['Proyectos de grado','/controllers/proyectos_grado_listar.php','file-earmark-text'],['Evaluaciones','/controllers/evaluaciones_listar.php','star'],['Auditoría','/controllers/auditoria_listar.php','shield-check']]; foreach($quick as $q): ?><div class="col-6 col-md-4 col-xl-2"><a class="quick-link" href="<?=$q[1]?>"><i class="bi bi-<?=$q[2]?>"></i><span><?=$q[0]?></span><i class="bi bi-arrow-right ms-auto"></i></a></div><?php endforeach; ?>
</div>

<script>
(function(){
    const makeChart = (id, config) => { const el=document.getElementById(id); if(el && window.Chart) new Chart(el, config); };
    const common = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } };
    makeChart('chartTipos',{type:'doughnut',data:{labels:<?=json_encode($graficoTipos['labels'],JSON_UNESCAPED_UNICODE)?>,datasets:[{data:<?=json_encode($graficoTipos['data'])?>}]},options:{...common,cutout:'62%'}});
    makeChart('chartEstados',{type:'bar',data:{labels:<?=json_encode($graficoEstados['labels'],JSON_UNESCAPED_UNICODE)?>,datasets:[{label:'Sesiones',data:<?=json_encode($graficoEstados['data'])?>}]},options:{...common,scales:{y:{beginAtZero:true,ticks:{precision:0}}},plugins:{legend:{display:false}}}});
    makeChart('chartMaterias',{type:'bar',data:{labels:<?=json_encode($graficoMaterias['labels'],JSON_UNESCAPED_UNICODE)?>,datasets:[{label:'Tutorías',data:<?=json_encode($graficoMaterias['data'])?>}]},options:{...common,indexAxis:'y',scales:{x:{beginAtZero:true,ticks:{precision:0}}},plugins:{legend:{display:false}}}});
    makeChart('chartTutores',{type:'bar',data:{labels:<?=json_encode($graficoTutores['labels'],JSON_UNESCAPED_UNICODE)?>,datasets:[{label:'Sesiones',data:<?=json_encode($graficoTutores['data'])?>}]},options:{...common,scales:{y:{beginAtZero:true,ticks:{precision:0}}},plugins:{legend:{display:false}}}});
})();
</script>
<?php include __DIR__.'/../layouts/footer.php'; ?>
