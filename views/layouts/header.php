<?php
require_once __DIR__ . '/../../includes/funciones.php';
iniciarSesion();
require_once __DIR__ . '/../../includes/vista_helpers.php';
$rolSesion = $_SESSION['rol'] ?? '';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
$apellidoSesion = $_SESSION['apellido'] ?? '';
$tituloSeccion = $tituloSeccion ?? ($tituloPagina ?? 'Sistema de Tutorías');
$esEstudiante = $rolSesion === 'estudiante';

// Las notificaciones son auxiliares: si la BD falla, la interfaz sigue funcionando.
$notificacionesNoLeidas = 0;
if (!empty($_SESSION['id_usuario'])) {
    try {
        require_once __DIR__ . '/../../config/conexion.php';
        require_once __DIR__ . '/../../models/NotificacionModel.php';
        $notificacionesNoLeidas = (new NotificacionModel($pdo))->contarNoLeidas((int)$_SESSION['id_usuario']);
    } catch (Throwable $e) {
        error_log('No se pudo contar notificaciones: ' . $e->getMessage());
    }
}
$mensajesFlash = flash_get();

function campanaNotificaciones(int $noLeidas): string
{
    $badge = $noLeidas > 0
        ? '<span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:.60rem;">' . $noLeidas . '</span>'
        : '';
    return '<a href="/controllers/notificaciones_listar.php" class="campana-notif position-relative d-inline-flex align-items-center justify-content-center p-2 text-decoration-none" aria-label="Notificaciones" title="Notificaciones"><i class="bi bi-bell" aria-hidden="true"></i>' . $badge . '</a>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#002b49">
  <title><?= e($tituloPagina ?? 'Sistema de Tutorías - UPDS') ?></title>
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <!-- Tom Select se usa únicamente en formularios donde facilita búsquedas largas. -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css">
  <link rel="stylesheet" href="/assets/css/upds-theme.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body>
<?php if ($esEstudiante): ?>
  <div class="student-main">
    <nav class="navbar navbar-expand-lg student-navbar" aria-label="Navegación principal">
      <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/views/estudiante/panel.php">
          <img class="brand-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
          <span><strong>UPDS</strong><small class="brand-campus">Sede Tarija</small></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav" aria-controls="studentNav" aria-label="Abrir menú"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="studentNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link <?= menuActivo('estudiante/panel') ? 'active' : '' ?>" href="/views/estudiante/panel.php"><i class="bi bi-grid-1x2 me-1"></i>Inicio</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?= menuActivo('tutorias') || menuActivo('tutorias_personales') || menuActivo('proyectos_grado') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown"><i class="bi bi-calendar2-check me-1"></i>Tutorías</a>
              <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item" href="/controllers/tutorias_grupales_listar.php"><i class="bi bi-people me-2"></i>Tutorías de materias</a></li>
                <li><a class="dropdown-item" href="/controllers/proyectos_grado_listar.php"><i class="bi bi-file-earmark-text me-2"></i>Proyectos de grado</a></li>
                <li><a class="dropdown-item" href="/controllers/tutorias_solicitar.php"><i class="bi bi-plus-circle me-2"></i>Solicitar tutoría</a></li>
                <li><a class="dropdown-item" href="/controllers/mis_tutorias.php"><i class="bi bi-list-check me-2"></i>Mis tutorías</a></li>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link <?= menuActivo('evaluaciones') ? 'active' : '' ?>" href="/controllers/evaluaciones_listar.php"><i class="bi bi-star me-1"></i>Evaluaciones</a></li>
          </ul>
          <div class="topbar-user">
            <span class="text-white"><?= campanaNotificaciones($notificacionesNoLeidas) ?></span>
            <a href="/controllers/perfil.php" class="d-flex align-items-center gap-2 text-decoration-none" title="Ver mi perfil"><?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?><div><div class="topbar-user-name"><?= e($nombreSesion . ' ' . $apellidoSesion) ?></div><div class="topbar-user-role">Estudiante</div></div></a>
            <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right me-1"></i>Salir</a>
          </div>
        </div>
      </div>
    </nav>
<?php else: ?>
  <div class="app-shell">
    <aside class="app-sidebar" aria-label="Navegación principal">
      <a class="sidebar-brand" href="<?= $rolSesion === 'tutor' ? '/views/tutor/panel.php' : '/controllers/dashboard.php' ?>">
        <img class="brand-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
        <span class="brand-name">Sistema de Tutorías<small class="brand-campus">Universidad Privada Domingo Savio</small></span>
      </a>
      <nav class="sidebar-nav">
        <?php if ($rolSesion === 'administrador'): ?>
          <div class="sidebar-section">Gestión académica</div>
          <a class="sidebar-link <?= menuActivo('dashboard') ? 'active' : '' ?>" href="/controllers/dashboard.php"><i class="bi bi-grid-1x2"></i>Inicio</a>
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people"></i>Usuarios</a>
          <a class="sidebar-link <?= menuActivo('estudiantes') ? 'active' : '' ?>" href="/controllers/estudiantes_listar.php"><i class="bi bi-person-vcard"></i>Estudiantes</a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3"></i>Tutores</a>
          <a class="sidebar-link <?= menuActivo('carreras') ? 'active' : '' ?>" href="/controllers/carreras_listar.php"><i class="bi bi-mortarboard"></i>Carreras</a>
          <a class="sidebar-link <?= menuActivo('materias') ? 'active' : '' ?>" href="/controllers/materias_listar.php"><i class="bi bi-journal-bookmark"></i>Materias</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('horarios_grupales') ? 'active' : '' ?>" href="/controllers/horarios_grupales_listar.php"><i class="bi bi-clock"></i>Horarios de materias</a>
          <a class="sidebar-link <?= menuActivo('tutorias_grupales') ? 'active' : '' ?>" href="/controllers/tutorias_grupales_listar.php"><i class="bi bi-people"></i>Tutorías de materias</a>
          <a class="sidebar-link <?= menuActivo('proyectos_grado') ? 'active' : '' ?>" href="/controllers/proyectos_grado_listar.php"><i class="bi bi-file-earmark-text"></i>Proyectos de grado</a>
          <a class="sidebar-link <?= menuActivo('evaluaciones') ? 'active' : '' ?>" href="/controllers/evaluaciones_listar.php"><i class="bi bi-star"></i>Evaluaciones</a>
          <div class="sidebar-section mt-4">Análisis</div>
          <a class="sidebar-link <?= menuActivo('reportes') ? 'active' : '' ?>" href="/controllers/reportes.php"><i class="bi bi-bar-chart-line"></i>Reportes</a>
          <a class="sidebar-link <?= menuActivo('auditoria') ? 'active' : '' ?>" href="/controllers/auditoria_listar.php"><i class="bi bi-shield-check"></i>Auditoría</a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <div class="sidebar-section">Mi espacio</div>
          <a class="sidebar-link <?= menuActivo('tutor/panel') ? 'active' : '' ?>" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2"></i>Mi panel</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias_grupales') ? 'active' : '' ?>" href="/controllers/tutorias_grupales_listar.php"><i class="bi bi-people"></i>Tutorías de materias</a>
          <a class="sidebar-link <?= menuActivo('proyectos_grado') ? 'active' : '' ?>" href="/controllers/proyectos_grado_listar.php"><i class="bi bi-file-earmark-text"></i>Proyectos de grado</a>
          <a class="sidebar-link <?= menuActivo('mis_tutorias') ? 'active' : '' ?>" href="/controllers/mis_tutorias.php"><i class="bi bi-calendar3"></i>Mis tutorías</a>
          <div class="sidebar-section mt-4">Configuración</div>
          <a class="sidebar-link <?= menuActivo('disponibilidad') ? 'active' : '' ?>" href="/controllers/disponibilidad_listar.php"><i class="bi bi-calendar-week"></i>Mi disponibilidad</a>
          <a class="sidebar-link <?= menuActivo('tutor_mis_materias') ? 'active' : '' ?>" href="/controllers/tutor_mis_materias.php"><i class="bi bi-journals"></i>Mis materias</a>
        <?php endif; ?>
        <div class="sidebar-section mt-4">Cuenta</div>
        <a class="sidebar-link <?= menuActivo('perfil') ? 'active' : '' ?>" href="/controllers/perfil.php"><i class="bi bi-person-circle"></i>Mi perfil</a>
        <a class="sidebar-link <?= menuActivo('notificaciones') ? 'active' : '' ?>" href="/controllers/notificaciones_listar.php"><i class="bi bi-bell"></i>Notificaciones<?php if($notificacionesNoLeidas): ?><span class="badge bg-danger ms-auto"><?= $notificacionesNoLeidas ?></span><?php endif; ?></a>
      </nav>
      <div class="sidebar-user d-flex align-items-center gap-2">
        <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
        <div class="flex-grow-1"><div class="sidebar-user-name"><?= e($nombreSesion) ?></div><div class="sidebar-user-role"><?= e(estadoEtiqueta($rolSesion)) ?></div></div>
        <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-link text-white p-1" aria-label="Cerrar sesión" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </aside>
    <div class="offcanvas offcanvas-start mobile-offcanvas" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header border-bottom border-light border-opacity-10"><span id="mobileSidebarLabel" class="text-white fw-semibold">Menú principal</span><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button></div>
      <div class="offcanvas-body sidebar-nav">
        <?php if ($rolSesion === 'administrador'): ?>
          <a class="sidebar-link" href="/controllers/dashboard.php"><i class="bi bi-grid-1x2"></i>Inicio</a>
          <a class="sidebar-link" href="/controllers/usuarios_listar.php"><i class="bi bi-people"></i>Usuarios</a>
          <a class="sidebar-link" href="/controllers/estudiantes_listar.php"><i class="bi bi-person-vcard"></i>Estudiantes</a>
          <a class="sidebar-link" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3"></i>Tutores</a>
          <a class="sidebar-link" href="/controllers/carreras_listar.php"><i class="bi bi-mortarboard"></i>Carreras</a>
          <a class="sidebar-link" href="/controllers/materias_listar.php"><i class="bi bi-journal-bookmark"></i>Materias</a>
          <a class="sidebar-link" href="/controllers/horarios_grupales_listar.php"><i class="bi bi-clock"></i>Horarios de materias</a>
          <a class="sidebar-link" href="/controllers/tutorias_grupales_listar.php"><i class="bi bi-people"></i>Tutorías de materias</a>
          <a class="sidebar-link" href="/controllers/proyectos_grado_listar.php"><i class="bi bi-file-earmark-text"></i>Proyectos de grado</a>
          <a class="sidebar-link" href="/controllers/reportes.php"><i class="bi bi-bar-chart-line"></i>Reportes</a>
          <a class="sidebar-link" href="/controllers/auditoria_listar.php"><i class="bi bi-shield-check"></i>Auditoría</a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <a class="sidebar-link" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2"></i>Mi panel</a>
          <a class="sidebar-link" href="/controllers/tutorias_grupales_listar.php"><i class="bi bi-people"></i>Tutorías de materias</a>
          <a class="sidebar-link" href="/controllers/mis_tutorias.php"><i class="bi bi-calendar3"></i>Mis tutorías</a>
          <a class="sidebar-link" href="/controllers/disponibilidad_listar.php"><i class="bi bi-calendar-week"></i>Mi disponibilidad</a>
          <a class="sidebar-link" href="/controllers/tutor_mis_materias.php"><i class="bi bi-journals"></i>Mis materias</a>
        <?php endif; ?>
        <a class="sidebar-link" href="/controllers/perfil.php"><i class="bi bi-person-circle"></i>Mi perfil</a>
        <a class="sidebar-link" href="/controllers/notificaciones_listar.php"><i class="bi bi-bell"></i>Notificaciones</a>
      </div>
    </div>
    <div class="app-main">
      <div class="mobile-menu-bar mobile-topbar d-lg-none d-flex align-items-center justify-content-between px-3 py-2 bg-white border-bottom">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Abrir menú"><i class="bi bi-list"></i></button>
        <span class="fw-bold text-primary">Sistema de Tutorías</span>
        <span><?= campanaNotificaciones($notificacionesNoLeidas) ?></span>
      </div>
<?php endif; ?>

<?php if (!empty($mensajesFlash)): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
    <?php foreach ($mensajesFlash as $mensaje): ?>
      <?php $tipo=(string)($mensaje['tipo'] ?? 'info'); $bs=in_array($tipo,['success','danger','warning','info','primary','secondary'],true)?$tipo:'info'; ?>
      <div class="toast align-items-center border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
        <div class="d-flex"><div class="toast-body"><i class="bi bi-<?= $bs==='success'?'check-circle':($bs==='danger'?'exclamation-triangle':'info-circle') ?> me-2"></i><?= e($mensaje['mensaje'] ?? '') ?></div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
