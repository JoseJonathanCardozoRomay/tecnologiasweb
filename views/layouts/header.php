<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/lista_helper.php';
require_once __DIR__ . '/../../includes/vista_helpers.php';
$mensajesFlash = flash_get();
$rolSesion = $_SESSION['rol'] ?? '';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
$apellidoSesion = $_SESSION['apellido'] ?? '';
$tituloSeccion = $tituloSeccion ?? ($tituloPagina ?? 'Sistema de Tutorías');
$esEstudiante = $rolSesion === 'estudiante';

// Insignia de notificaciones: un fallo de base de datos no debe romper la página.
$notificacionesNoLeidas = 0;
if (!empty($_SESSION['id_usuario'])) {
    try {
        require_once __DIR__ . '/../../config/conexion.php';
        require_once __DIR__ . '/../../models/NotificacionModel.php';
        $notificacionesNoLeidas = (new NotificacionModel($pdo))->contarNoLeidas($_SESSION['id_usuario']);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $notificacionesNoLeidas = 0;
    }
}
function campanaNotificaciones($noLeidas) {
    $noLeidas = (int) $noLeidas;
    $insignia = $noLeidas > 0
        ? '<span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: .6rem;">' . $noLeidas . '</span>'
        : '';
    // .campana-notif fija un color propio: sin él, la campana heredaba el color de
    // enlace (azul marino) y desaparecía sobre los fondos azul oscuro del navbar.
    $clases = 'campana-notif position-relative d-inline-flex align-items-center justify-content-center p-2 text-decoration-none';
    if ($noLeidas > 0) {
        $clases .= ' campana-activa';
    }
    return '<a href="/controllers/notificaciones_listar.php" class="' . $clases . '" aria-label="Notificaciones' . ($noLeidas > 0 ? ': ' . $noLeidas . ' sin leer' : '') . '" title="Notificaciones">'
        . '<i class="bi bi-bell" aria-hidden="true"></i>' . $insignia . '</a>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#002b49">
  <title><?= htmlspecialchars($tituloPagina ?? 'Sistema de Tutorías - UPDS') ?></title>
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav" aria-controls="studentNav" aria-label="Abrir menú">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="studentNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link <?= menuActivo('estudiante/panel') ? 'active' : '' ?>" href="/views/estudiante/panel.php"><i class="bi bi-calendar2-check me-1" aria-hidden="true"></i>Mis Tutorías</a></li>
            <li class="nav-item"><a class="nav-link <?= menuActivo('solicitar') ? 'active' : '' ?>" href="/controllers/tutorias_solicitar.php"><i class="bi bi-calendar-plus me-1" aria-hidden="true"></i>Solicitar Tutoría</a></li>
            <li class="nav-item"><a class="nav-link <?= menuActivo('estudiante/perfil') ? 'active' : '' ?>" href="/views/estudiante/perfil.php"><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Mi Perfil</a></li>
          </ul>
          <div class="topbar-user">
            <span class="text-white"><?= campanaNotificaciones($notificacionesNoLeidas) ?></span>
            <a href="/views/estudiante/perfil.php" class="d-flex align-items-center gap-2 text-decoration-none" title="Ver mi perfil" aria-label="Ver mi perfil">
              <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
              <div><div class="topbar-user-name"><?= htmlspecialchars("{$nombreSesion} {$apellidoSesion}") ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
            </a>
            <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Salir</a>
          </div>
        </div>
      </div>
    </nav>
<?php else: ?>
  <div class="app-shell">
    <aside class="app-sidebar" aria-label="Navegación principal">
      <a class="sidebar-brand" href="<?= $rolSesion === 'tutor' ? '/views/tutor/panel.php' : '/controllers/usuarios_listar.php' ?>">
        <img class="brand-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
        <span class="brand-name">Sistema de Tutorías<small class="brand-campus">Universidad Privada Domingo Savio</small></span>
      </a>
      <nav class="sidebar-nav">
        <?php if ($rolSesion === 'administrador'): ?>
          <div class="sidebar-section">Gestión académica</div>
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people" aria-hidden="true"></i>Usuarios</a>
          <a class="sidebar-link <?= menuActivo('carreras') ? 'active' : '' ?>" href="/controllers/carreras_listar.php"><i class="bi bi-mortarboard" aria-hidden="true"></i>Carreras</a>
          <a class="sidebar-link <?= menuActivo('materias') ? 'active' : '' ?>" href="/controllers/materias_listar.php"><i class="bi bi-journal-bookmark" aria-hidden="true"></i>Materias</a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3" aria-hidden="true"></i>Tutores</a>
          <a class="sidebar-link <?= menuActivo('estudiantes') ? 'active' : '' ?>" href="/controllers/estudiantes_listar.php"><i class="bi bi-person-vcard" aria-hidden="true"></i>Estudiantes</a>
          <a class="sidebar-link <?= menuActivo('admin_periodos') ? 'active' : '' ?>" href="/controllers/admin_periodos.php"><i class="bi bi-calendar-range" aria-hidden="true"></i>Periodos</a>
          <a class="sidebar-link <?= menuActivo('admin_bloques') ? 'active' : '' ?>" href="/controllers/admin_bloques.php"><i class="bi bi-clock" aria-hidden="true"></i>Bloques Horarios</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i>Tutorías</a>
          <a class="sidebar-link <?= menuActivo('reportes') ? 'active' : '' ?>" href="/controllers/reportes.php"><i class="bi bi-bar-chart" aria-hidden="true"></i>Reportes</a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <div class="sidebar-section">Mi espacio</div>
          <a class="sidebar-link <?= menuActivo('tutor/panel') ? 'active' : '' ?>" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i>Mi panel</a>
          <a class="sidebar-link <?= menuActivo('disponibilidad') ? 'active' : '' ?>" href="/controllers/tutores_disponibilidad.php"><i class="bi bi-clock-history" aria-hidden="true"></i>Horarios y materias</a>
        <?php endif; ?>
      </nav>
      <div class="sidebar-user d-flex align-items-center gap-2">
        <?php if ($rolSesion === 'tutor'): ?>
          <a href="/views/tutor/panel.php#perfil" class="d-flex align-items-center gap-2 flex-grow-1 text-decoration-none" title="Ir a mi perfil profesional">
            <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
            <div class="flex-grow-1"><div class="sidebar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="sidebar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
          </a>
        <?php else: ?>
          <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
          <div class="flex-grow-1"><div class="sidebar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="sidebar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
        <?php endif; ?>
        <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-link text-white p-1" aria-label="Cerrar sesión" title="Cerrar sesión"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></a>
      </div>
    </aside>
    <div class="offcanvas offcanvas-start mobile-offcanvas" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header border-bottom border-light border-opacity-10">
        <span id="mobileSidebarLabel" class="text-white fw-semibold">Menú principal</span><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
      </div>
      <div class="offcanvas-body sidebar-nav">
        <?php if ($rolSesion === 'administrador'): ?>
          <div class="sidebar-section">Gestión académica</div>
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people" aria-hidden="true"></i>Usuarios</a>
          <a class="sidebar-link <?= menuActivo('carreras') ? 'active' : '' ?>" href="/controllers/carreras_listar.php"><i class="bi bi-mortarboard" aria-hidden="true"></i>Carreras</a>
          <a class="sidebar-link <?= menuActivo('materias') ? 'active' : '' ?>" href="/controllers/materias_listar.php"><i class="bi bi-journal-bookmark" aria-hidden="true"></i>Materias</a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3" aria-hidden="true"></i>Tutores</a>
          <a class="sidebar-link <?= menuActivo('estudiantes') ? 'active' : '' ?>" href="/controllers/estudiantes_listar.php"><i class="bi bi-person-vcard" aria-hidden="true"></i>Estudiantes</a>
          <a class="sidebar-link <?= menuActivo('admin_periodos') ? 'active' : '' ?>" href="/controllers/admin_periodos.php"><i class="bi bi-calendar-range" aria-hidden="true"></i>Periodos</a>
          <a class="sidebar-link <?= menuActivo('admin_bloques') ? 'active' : '' ?>" href="/controllers/admin_bloques.php"><i class="bi bi-clock" aria-hidden="true"></i>Bloques Horarios</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i>Tutorías</a>
          <a class="sidebar-link <?= menuActivo('reportes') ? 'active' : '' ?>" href="/controllers/reportes.php"><i class="bi bi-bar-chart" aria-hidden="true"></i>Reportes</a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <div class="sidebar-section">Mi espacio</div>
          <a class="sidebar-link <?= menuActivo('tutor/panel') ? 'active' : '' ?>" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i>Mi panel</a>
          <a class="sidebar-link <?= menuActivo('disponibilidad') ? 'active' : '' ?>" href="/controllers/tutores_disponibilidad.php"><i class="bi bi-clock-history" aria-hidden="true"></i>Horarios y materias</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="app-main">
      <div class="mobile-menu-bar">
        <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Abrir menú"><i class="bi bi-list fs-3" aria-hidden="true"></i></button>
        <img class="brand-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
        <div class="d-flex align-items-center gap-1 text-white">
          <?= campanaNotificaciones($notificacionesNoLeidas) ?>
          <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-link text-white p-0" aria-label="Cerrar sesión"><i class="bi bi-box-arrow-right fs-5" aria-hidden="true"></i></a>
        </div>
      </div>
      <header class="app-topbar d-flex justify-content-between align-items-center">
        <div class="topbar-title"><?= htmlspecialchars($tituloSeccion) ?></div>
        <div class="topbar-user">
          <span class="text-primary"><?= campanaNotificaciones($notificacionesNoLeidas) ?></span>
          <?php if ($rolSesion === 'tutor'): ?>
            <a href="/views/tutor/panel.php#perfil" class="d-flex align-items-center gap-2 text-decoration-none" title="Ir a mi perfil profesional" aria-label="Ir a mi perfil profesional">
              <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
              <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
            </a>
          <?php else: ?>
            <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
            <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
          <?php endif; ?>
          <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Salir</a>
        </div>
      </header>
<?php endif; ?>

<main class="app-content">
<!-- Mensajes flash convertidos en toasts (esquina superior derecha) -->
<?php if (!empty($mensajesFlash)): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" id="contenedor-toasts" style="z-index: 1090;">
  <?php foreach ($mensajesFlash as $flash):
      $tipoFlash = $flash['tipo'] === 'error' ? 'danger' : htmlspecialchars($flash['tipo']);
      $iconoFlash = [
          'success' => 'bi-check-circle-fill',
          'danger'  => 'bi-exclamation-octagon-fill',
          'warning' => 'bi-exclamation-triangle-fill',
          'info'    => 'bi-info-circle-fill',
      ][$tipoFlash] ?? 'bi-info-circle-fill';
  ?>
    <div class="toast toast-upds toast-<?= $tipoFlash ?>" role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-delay="6000" data-bs-autohide="true">
      <div class="toast-header">
        <i class="bi <?= $iconoFlash ?> me-2 text-<?= $tipoFlash ?>"></i>
        <strong class="me-auto"><?= ['success' => 'Éxito', 'danger' => 'Error', 'warning' => 'Atención', 'info' => 'Información'][$tipoFlash] ?? 'Aviso' ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar"></button>
      </div>
      <div class="toast-body"><?= htmlspecialchars($flash['mensaje']) ?></div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
