<?php
require_once __DIR__.'/../../includes/verificar_sesion.php';
require_once __DIR__.'/../../includes/funciones.php';
requireRole(['estudiante']);
require_once __DIR__.'/../../config/conexion.php';
require_once __DIR__.'/../../models/EstudianteModel.php';
require_once __DIR__.'/../../models/DashboardModel.php';

$e = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
$dashboard = new DashboardModel($pdo);
$resumen = $e ? $dashboard->resumenEstudiante((int)$e['id_estudiante']) : ['pendientes'=>0,'programadas'=>0,'realizadas'=>0,'evaluadas'=>0,'grupales'=>0];
$proximasPersonales = $e ? $dashboard->proximasTutoriasEstudiante((int)$e['id_estudiante']) : [];
$proximasGrupales = $e ? $dashboard->proximasGrupalesEstudiante((int)$e['id_estudiante']) : [];
$agenda = array_merge($proximasPersonales, $proximasGrupales);
usort($agenda, static fn(array $a,array $b): int => (strtotime($a['fecha'].' '.$a['hora_inicio']) ?: PHP_INT_MAX) <=> (strtotime($b['fecha'].' '.$b['hora_inicio']) ?: PHP_INT_MAX));
$agenda = array_slice($agenda, 0, 8);
$tituloPagina='Panel del Estudiante - Sistema de Tutorías';
require __DIR__.'/../layouts/header.php'; mostrarFlash();
?>
<div class="mb-4">
  <div><span class="eyebrow">Espacio del estudiante</span><h1 class="page-title mb-1">Hola, <?=e($_SESSION['nombre'])?></h1><p class="text-muted mb-0">Organiza tu apoyo académico y consulta tus sesiones desde un solo lugar.</p></div>
</div>
<?php if ($e): ?>
<div class="card card-custom p-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-auto"><div class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-mortarboard-fill"></i></div></div>
    <div class="col"><div class="small text-muted">Información académica</div><strong><?=e($e['nombre_carrera'] ?? 'Carrera no registrada')?></strong><div class="small text-muted">Semestre <?=e($e['semestre'])?> · Registro <?=e($e['registro_universitario'] ?: 'Sin registrar')?></div></div>
    <div class="col-auto"><a href="/controllers/perfil.php" class="btn btn-outline-secondary">Ver perfil</a></div>
  </div>
</div>
<?php endif; ?>
<div class="row g-3 mb-4">
<?php foreach([['pendientes','Pendientes','clock','warning'],['programadas','Programadas','calendar-check','primary'],['realizadas','Realizadas','check2-circle','success'],['grupales','Inscripciones grupales','people','info']] as $c): ?>
<div class="col-6 col-lg-3"><div class="metric-card h-100"><div class="metric-icon text-<?=$c[3]?> bg-<?=$c[3]?>-subtle"><i class="bi bi-<?=$c[2]?>"></i></div><div><div class="metric-value"><?=e($resumen[$c[0]])?></div><div class="metric-label"><?=e($c[1])?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-4">
  <div class="col-xl-8">
    <div class="card card-custom p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Próximas tutorías</h5><p class="small text-muted mb-0">Materias grupales y sesiones individuales.</p></div><a href="/controllers/mis_tutorias.php" class="small text-decoration-none">Ver agenda</a></div>
      <?php foreach($agenda as $x): ?>
        <div class="d-flex gap-3 align-items-center py-3 border-top">
          <div class="text-center" style="min-width:70px"><div class="fw-bold text-primary"><?=date('d',strtotime($x['fecha']))?></div><small class="text-muted"><?=date('M',strtotime($x['fecha']))?></small></div>
          <div class="flex-grow-1"><strong><?=e($x['nombre_materia'] ?? 'Proyecto de grado')?></strong><div class="small text-muted"><?=($x['tipo'] ?? '')==='grupal' ? 'Tutoría de materias · '.e(ucfirst((string)$x['turno'])) : 'Tutoría personal · '.e($x['tutor'] ?? 'Tutor')?> · <?=e(substr($x['hora_inicio'],0,5))?>–<?=e(substr($x['hora_fin'],0,5))?></div></div>
          <span class="badge text-bg-<?=estadoBadge((string)$x['estado'])?>"><?=e(estadoEtiqueta((string)$x['estado']))?></span>
        </div>
      <?php endforeach; ?>
      <?php if(!$agenda): ?><div class="text-center py-5 text-muted">Aún no tienes próximas tutorías.</div><?php endif; ?>
    </div>
  </div>
  <div class="col-xl-4"><div class="card card-custom p-4 h-100"><h5 class="fw-bold">Acciones rápidas</h5><div class="d-grid gap-2 mt-3">
    <a href="/controllers/tutorias_solicitar.php" class="quick-link"><i class="bi bi-calendar-plus text-primary"></i><span>Solicitar tutoría</span><i class="bi bi-arrow-right ms-auto"></i></a>
    <a href="/controllers/tutorias_grupales_listar.php" class="quick-link"><i class="bi bi-people text-success"></i><span>Ver materias grupales</span><i class="bi bi-arrow-right ms-auto"></i></a>
    <a href="/controllers/evaluaciones_listar.php" class="quick-link"><i class="bi bi-star text-warning"></i><span>Mis evaluaciones</span><i class="bi bi-arrow-right ms-auto"></i></a>
  </div></div></div>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
