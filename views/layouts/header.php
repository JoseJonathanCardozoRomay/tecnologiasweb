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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#002b49">
  <title><?= htmlspecialchars($tituloPagina ?? 'Sistema de Tutorías - UPDS') ?></title>
  <!-- Reemplazar el logo provisional de assets/img/logo-upds.svg por el logo oficial cuando esté disponible. -->
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/upds-theme.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
          </ul>
          <div class="topbar-user">
            <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
            <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
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
        <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
        <div class="flex-grow-1"><div class="sidebar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="sidebar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
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
        <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-link text-white p-0" aria-label="Cerrar sesión"><i class="bi bi-box-arrow-right fs-5" aria-hidden="true"></i></a>
      </div>
      <header class="app-topbar d-flex justify-content-between align-items-center">
        <div class="topbar-title"><?= htmlspecialchars($tituloSeccion) ?></div>
        <div class="topbar-user">
          <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
          <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
          <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Salir</a>
        </div>
      </header>
<?php endif; ?>

<main class="app-content">
<?php foreach ($mensajesFlash as $flash): ?>
  <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash['mensaje']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endforeach; ?>
