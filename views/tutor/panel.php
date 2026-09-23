<?php
require_once __DIR__.'/../../includes/verificar_sesion.php';
require_once __DIR__.'/../../includes/funciones.php';
requireRole(['tutor']);
require_once __DIR__.'/../../config/conexion.php';
require_once __DIR__.'/../../models/TutorModel.php';
require_once __DIR__.'/../../models/DashboardModel.php';

$t = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
$dashboard = new DashboardModel($pdo);
$resumen = $t ? $dashboard->resumenTutor((int)$t['id_tutor']) : ['materias'=>0,'dias'=>0,'pendientes'=>0,'programadas'=>0,'realizadas'=>0,'grupales'=>0,'estudiantes'=>0,'promedio'=>0];
$personales = $t ? $dashboard->proximasTutoriasTutor((int)$t['id_tutor']) : [];
$grupales = $t ? $dashboard->proximasGrupalesTutor((int)$t['id_tutor']) : [];
$agenda = array_merge($personales,$grupales);
usort($agenda, static fn(array $a,array $b): int => (strtotime($a['fecha'].' '.$a['hora_inicio']) ?: PHP_INT_MAX) <=> (strtotime($b['fecha'].' '.$b['hora_inicio']) ?: PHP_INT_MAX));
$agenda = array_slice($agenda, 0, 8);
$tituloPagina='Panel del Tutor - Sistema de Tutorías';
require __DIR__.'/../layouts/header.php'; mostrarFlash();
?>
<div class="mb-4">
  <div><span class="eyebrow">Espacio del tutor</span><h1 class="page-title mb-1">Bienvenido, <?=e($_SESSION['nombre'])?></h1><p class="text-muted mb-0">Gestiona tus solicitudes, sesiones, estudiantes y disponibilidad.</p></div>
</div>
<div class="row g-3 mb-4">
<?php foreach([['dias','Días con disponibilidad','calendar-week','primary'],['materias','Materias asignadas','journal-bookmark','success'],['estudiantes','Estudiantes atendidos','people','info'],['promedio','Calificación promedio','star-fill','warning']] as $c): ?>
<div class="col-6 col-lg-3"><div class="metric-card h-100"><div class="metric-icon text-<?=$c[3]?> bg-<?=$c[3]?>-subtle"><i class="bi bi-<?=$c[2]?>"></i></div><div><div class="metric-value"><?=$c[0]==='promedio'?number_format((float)$resumen[$c[0]],1):e($resumen[$c[0]])?></div><div class="metric-label"><?=e($c[1])?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-4"><div class="col-xl-8"><div class="card card-custom p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Próximas sesiones</h5><p class="small text-muted mb-0">Tutorías personales y grupales asignadas a ti.</p></div><a href="/controllers/mis_tutorias.php" class="small text-decoration-none">Ver agenda</a></div>
<?php foreach($agenda as $x): ?><div class="d-flex gap-3 align-items-center py-3 border-top"><div class="text-center" style="min-width:70px"><div class="fw-bold text-primary"><?=date('d',strtotime($x['fecha']))?></div><small class="text-muted"><?=date('M',strtotime($x['fecha']))?></small></div><div class="flex-grow-1"><strong><?=e($x['nombre_materia'] ?? 'Proyecto de grado')?></strong><div class="small text-muted"><?=($x['tipo']??'')==='grupal' ? 'Materias grupales · '.e($x['total_estudiantes'] ?? 0).' estudiantes · '.e(ucfirst((string)$x['turno'])) : 'Tutoría personal · '.e($x['estudiante'] ?? 'Estudiante')?> · <?=e(substr($x['hora_inicio'],0,5))?>–<?=e(substr($x['hora_fin'],0,5))?></div></div><span class="badge text-bg-<?=estadoBadge((string)$x['estado'])?>"><?=e(estadoEtiqueta((string)$x['estado']))?></span></div><?php endforeach; ?>
<?php if(!$agenda): ?><div class="text-center py-5 text-muted">No tienes próximas tutorías registradas.</div><?php endif; ?></div></div>
<div class="col-xl-4"><div class="card card-custom p-4 h-100"><h5 class="fw-bold">Accesos rápidos</h5><div class="d-grid gap-2 mt-3"><a href="/controllers/tutorias_grupales_listar.php" class="quick-link"><i class="bi bi-people text-primary"></i><span>Tutorías de materias</span><i class="bi bi-arrow-right ms-auto"></i></a><a href="/controllers/proyectos_grado_listar.php" class="quick-link"><i class="bi bi-file-earmark-text text-success"></i><span>Proyectos de grado</span><i class="bi bi-arrow-right ms-auto"></i></a><a href="/controllers/mis_tutorias.php" class="quick-link"><i class="bi bi-calendar3 text-secondary"></i><span>Mis tutorías</span><i class="bi bi-arrow-right ms-auto"></i></a><a href="/controllers/disponibilidad_listar.php" class="quick-link"><i class="bi bi-calendar-week text-warning"></i><span>Mi disponibilidad</span><i class="bi bi-arrow-right ms-auto"></i></a></div><hr><div class="small text-muted">Solicitudes pendientes: <strong><?=e($resumen['pendientes'])?></strong><br>Sesiones grupales: <strong><?=e($resumen['grupales'])?></strong></div></div></div></div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
