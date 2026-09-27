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
$mgCompletada = $mgCompletada ?? false;
header('Content-Type: text/html; charset=utf-8');
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
            <li class="nav-item"><a class="nav-link <?= menuActivo('estudiante/mg_portal') ? 'active' : '' ?>" href="/views/estudiante/mg_portal.php"><i class="bi bi-mortarboard-fill me-1" aria-hidden="true"></i>Modalidad de Grado</a></li>
            <?php if (empty($mgCompletada)): ?>
              <li class="nav-item"><a class="nav-link <?= menuActivo('solicitar') ? 'active' : '' ?>" href="/controllers/tutorias_solicitar.php"><i class="bi bi-calendar-plus me-1" aria-hidden="true"></i>Solicitar Tutoría</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link <?= menuActivo('estudiante/historial') ? 'active' : '' ?>" href="/views/estudiante/historial.php"><i class="bi bi-clock-history me-1" aria-hidden="true"></i>Historial</a></li>
          </ul>
          <div class="topbar-user">
            <div class="dropdown notif-dropdown me-3">
              <button class="btn btn-link position-relative p-1 notif-bell-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notificaciones" title="Notificaciones">
                <i class="bi bi-bell fs-5" aria-hidden="true"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill border border-light notif-count" style="display:none;">0</span>
              </button>
              <div class="dropdown-menu dropdown-menu-end p-0 notif-menu" style="width: 360px; max-width: 90vw;">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                  <strong class="small text-uppercase text-muted">Notificaciones <span class="notif-count-label text-primary"></span></strong>
                  <a href="/controllers/notificaciones_listar.php" class="small text-decoration-none">Ver todas</a>
                </div>
                <div class="notif-items" style="max-height: 320px; overflow-y: auto;">
                  <div class="text-center py-4 text-muted"><i class="bi bi-bell-slash d-block mb-1"></i>Sin notificaciones</div>
                </div>
              </div>
            </div>
            <span class="d-flex align-items-center gap-2">
              <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
              <div><div class="topbar-user-name"><?= htmlspecialchars("{$nombreSesion} {$apellidoSesion}") ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
            </span>
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
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people" aria-hidden="true"></i><span class="sidebar-label">Usuarios</span></a>
          <a class="sidebar-link <?= menuActivo('carreras') ? 'active' : '' ?>" href="/controllers/carreras_listar.php"><i class="bi bi-mortarboard" aria-hidden="true"></i><span class="sidebar-label">Carreras</span></a>
          <a class="sidebar-link <?= menuActivo('materias') ? 'active' : '' ?>" href="/controllers/materias_listar.php"><i class="bi bi-journal-bookmark" aria-hidden="true"></i><span class="sidebar-label">Materias</span></a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3" aria-hidden="true"></i><span class="sidebar-label">Tutores</span></a>
          <a class="sidebar-link <?= menuActivo('solicitudes_tutor') ? 'active' : '' ?>" href="/controllers/solicitudes_tutor_listar.php"><i class="bi bi-send-check" aria-hidden="true"></i><span class="sidebar-label">Solicitudes de tutores</span></a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias_listar') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i><span class="sidebar-label">Tutorías</span></a>
          <a class="sidebar-link <?= menuActivo('tutorias_asignar') ? 'active' : '' ?>" href="/controllers/tutorias_asignar.php"><i class="bi bi-person-plus" aria-hidden="true"></i><span class="sidebar-label">Asignar tutoría</span></a>
          <a class="sidebar-link <?= menuActivo('tribunales_asignar') ? 'active' : '' ?>" href="/controllers/tribunales_asignar.php"><i class="bi bi-people" aria-hidden="true"></i><span class="sidebar-label">Tribunales</span></a>
          <a class="sidebar-link <?= menuActivo('expediente_documentos') ? 'active' : '' ?>" href="/controllers/expediente_documentos.php"><i class="bi bi-folder2-open" aria-hidden="true"></i><span class="sidebar-label">Expedientes</span></a>
          <div class="sidebar-section mt-4">Modalidad de Grado</div>
          <a class="sidebar-link <?= menuActivo('mg_modalidades') ? 'active' : '' ?>" href="/controllers/mg_modalidades_listar.php"><i class="bi bi-diagram-3" aria-hidden="true"></i><span class="sidebar-label">Modalidades</span></a>
          <a class="sidebar-link <?= menuActivo('mg_cohortes') ? 'active' : '' ?>" href="/controllers/mg_cohortes_listar.php"><i class="bi bi-calendar-range" aria-hidden="true"></i><span class="sidebar-label">Cohortes</span></a>
          <a class="sidebar-link <?= menuActivo('mg_calendario') ? 'active' : '' ?>" href="/controllers/mg_calendario_listar.php"><i class="bi bi-calendar-event" aria-hidden="true"></i><span class="sidebar-label">Calendario MG</span></a>
          <a class="sidebar-link <?= menuActivo('mg_defensas') ? 'active' : '' ?>" href="/controllers/mg_defensas_listar.php"><i class="bi bi-calendar2-check" aria-hidden="true"></i><span class="sidebar-label">Defensas</span></a>
          <a class="sidebar-link <?= menuActivo('mg_padron') ? 'active' : '' ?>" href="/controllers/mg_padron_importar.php"><i class="bi bi-upload" aria-hidden="true"></i><span class="sidebar-label">Importar Padrón</span></a>
          <a class="sidebar-link <?= menuActivo('mg_reportes') ? 'active' : '' ?>" href="/controllers/mg_reportes_cohorte.php"><i class="bi bi-file-earmark-bar-chart" aria-hidden="true"></i><span class="sidebar-label">Reportes</span></a>
          <a class="sidebar-link <?= menuActivo('mg_expedientes') ? 'active' : '' ?>" href="/controllers/mg_expedientes_listar.php"><i class="bi bi-folder2-open" aria-hidden="true"></i><span class="sidebar-label">Expedientes MG</span></a>
          <a class="sidebar-link <?= menuActivo('mg_parametros') ? 'active' : '' ?>" href="/controllers/mg_parametros_listar.php"><i class="bi bi-sliders" aria-hidden="true"></i><span class="sidebar-label">Parámetros MG</span></a>
          <a class="sidebar-link <?= menuActivo('mg_alertas') ? 'active' : '' ?>" href="/controllers/mg_alertas.php"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><span class="sidebar-label">Alertas</span></a>
          <div class="sidebar-section mt-4">Auditoría</div>
          <a class="sidebar-link <?= menuActivo('historial_listar') ? 'active' : '' ?>" href="/controllers/historial_listar.php"><i class="bi bi-shield-lock" aria-hidden="true"></i><span class="sidebar-label">Historial y Auditoría</span></a>
        <?php elseif ($rolSesion === 'auxiliar'): ?>
          <div class="sidebar-section">Gestión académica</div>
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people" aria-hidden="true"></i><span class="sidebar-label">Usuarios</span></a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3" aria-hidden="true"></i><span class="sidebar-label">Tutores</span></a>
          <a class="sidebar-link <?= menuActivo('solicitudes_tutor') ? 'active' : '' ?>" href="/controllers/solicitudes_tutor_listar.php"><i class="bi bi-send-check" aria-hidden="true"></i><span class="sidebar-label">Solicitudes de tutores</span></a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias_listar') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i><span class="sidebar-label">Tutorías</span></a>
          <a class="sidebar-link <?= menuActivo('tutorias_asignar') ? 'active' : '' ?>" href="/controllers/tutorias_asignar.php"><i class="bi bi-person-plus" aria-hidden="true"></i><span class="sidebar-label">Asignar tutoría</span></a>
          <a class="sidebar-link <?= menuActivo('tribunales_asignar') ? 'active' : '' ?>" href="/controllers/tribunales_asignar.php"><i class="bi bi-people" aria-hidden="true"></i><span class="sidebar-label">Tribunales</span></a>
          <a class="sidebar-link <?= menuActivo('mg_comprobantes') ? 'active' : '' ?>" href="/controllers/mg_comprobantes_listar.php"><i class="bi bi-file-earmark-check" aria-hidden="true"></i><span class="sidebar-label">Comprobantes MG</span></a>
          <a class="sidebar-link <?= menuActivo('expediente_documentos') ? 'active' : '' ?>" href="/controllers/expediente_documentos.php"><i class="bi bi-folder2-open" aria-hidden="true"></i><span class="sidebar-label">Expedientes</span></a>
          <div class="sidebar-section mt-4">Modalidad de Grado</div>
          <a class="sidebar-link <?= menuActivo('mg_modalidades') ? 'active' : '' ?>" href="/controllers/mg_modalidades_listar.php"><i class="bi bi-diagram-3" aria-hidden="true"></i><span class="sidebar-label">Modalidades</span></a>
          <a class="sidebar-link <?= menuActivo('mg_cohortes') ? 'active' : '' ?>" href="/controllers/mg_cohortes_listar.php"><i class="bi bi-calendar-range" aria-hidden="true"></i><span class="sidebar-label">Cohortes</span></a>
          <a class="sidebar-link <?= menuActivo('mg_calendario') ? 'active' : '' ?>" href="/controllers/mg_calendario_listar.php"><i class="bi bi-calendar-event" aria-hidden="true"></i><span class="sidebar-label">Calendario MG</span></a>
          <a class="sidebar-link <?= menuActivo('mg_defensas') ? 'active' : '' ?>" href="/controllers/mg_defensas_listar.php"><i class="bi bi-calendar2-check" aria-hidden="true"></i><span class="sidebar-label">Defensas</span></a>
          <a class="sidebar-link <?= menuActivo('mg_padron') ? 'active' : '' ?>" href="/controllers/mg_padron_importar.php"><i class="bi bi-upload" aria-hidden="true"></i><span class="sidebar-label">Importar Padrón</span></a>
          <a class="sidebar-link <?= menuActivo('mg_reportes') ? 'active' : '' ?>" href="/controllers/mg_reportes_cohorte.php"><i class="bi bi-file-earmark-bar-chart" aria-hidden="true"></i><span class="sidebar-label">Reportes</span></a>
          <a class="sidebar-link <?= menuActivo('mg_expedientes') ? 'active' : '' ?>" href="/controllers/mg_expedientes_listar.php"><i class="bi bi-folder2-open" aria-hidden="true"></i><span class="sidebar-label">Expedientes MG</span></a>
          <div class="sidebar-section mt-4">Auditoría</div>
          <a class="sidebar-link <?= menuActivo('bitacora_listar') ? 'active' : '' ?>" href="/controllers/bitacora_listar.php"><i class="bi bi-shield-lock" aria-hidden="true"></i><span class="sidebar-label">Bitácora de usuarios</span></a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <div class="sidebar-section">Mi espacio</div>
          <a class="sidebar-link <?= menuActivo('tutor/panel') ? 'active' : '' ?>" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i><span class="sidebar-label">Mi panel</span></a>
          <a class="sidebar-link <?= menuActivo('cartas_responder') ? 'active' : '' ?>" href="/controllers/cartas_responder.php"><i class="bi bi-envelope-paper" aria-hidden="true"></i><span class="sidebar-label">Cartas de designación</span></a>
          <a class="sidebar-link <?= menuActivo('reuniones_registrar') ? 'active' : '' ?>" href="/controllers/reuniones_registrar.php"><i class="bi bi-calendar2-week" aria-hidden="true"></i><span class="sidebar-label">Reuniones</span></a>
          <a class="sidebar-link <?= menuActivo('informes_registrar') ? 'active' : '' ?>" href="/controllers/informes_registrar.php"><i class="bi bi-clipboard2-check" aria-hidden="true"></i><span class="sidebar-label">Informes de avance</span></a>
          <a class="sidebar-link <?= menuActivo('disponibilidad') ? 'active' : '' ?>" href="/controllers/tutores_disponibilidad.php"><i class="bi bi-clock-history" aria-hidden="true"></i><span class="sidebar-label">Horarios y materias</span></a>
        <?php endif; ?>
      </nav>
      <div class="sidebar-user d-flex align-items-center gap-2">
        <?php if ($rolSesion === 'tutor'): ?>
          <a href="/controllers/tutor_perfil.php" class="sidebar-user-perfil d-flex align-items-center gap-2 flex-grow-1 text-decoration-none" title="Ver y editar mi perfil" aria-label="Ver y editar mi perfil">
            <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
            <div class="flex-grow-1 min-w-0"><div class="sidebar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="sidebar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
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
          <a class="sidebar-link <?= menuActivo('solicitudes_tutor') ? 'active' : '' ?>" href="/controllers/solicitudes_tutor_listar.php"><i class="bi bi-send-check" aria-hidden="true"></i>Solicitudes de tutores</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias_listar') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i>Tutorías</a>
          <a class="sidebar-link <?= menuActivo('tutorias_asignar') ? 'active' : '' ?>" href="/controllers/tutorias_asignar.php"><i class="bi bi-person-plus" aria-hidden="true"></i>Asignar tutoría</a>
          <a class="sidebar-link <?= menuActivo('tribunales_asignar') ? 'active' : '' ?>" href="/controllers/tribunales_asignar.php"><i class="bi bi-people" aria-hidden="true"></i>Tribunales</a>
          <a class="sidebar-link <?= menuActivo('expediente_documentos') ? 'active' : '' ?>" href="/controllers/expediente_documentos.php"><i class="bi bi-folder2-open" aria-hidden="true"></i>Expedientes</a>
          <div class="sidebar-section mt-4">Modalidad de Grado</div>
          <a class="sidebar-link <?= menuActivo('mg_modalidades') ? 'active' : '' ?>" href="/controllers/mg_modalidades_listar.php"><i class="bi bi-diagram-3" aria-hidden="true"></i>Modalidades</a>
          <a class="sidebar-link <?= menuActivo('mg_cohortes') ? 'active' : '' ?>" href="/controllers/mg_cohortes_listar.php"><i class="bi bi-calendar-range" aria-hidden="true"></i>Cohortes</a>
          <a class="sidebar-link <?= menuActivo('mg_calendario') ? 'active' : '' ?>" href="/controllers/mg_calendario_listar.php"><i class="bi bi-calendar-event" aria-hidden="true"></i>Calendario MG</a>
          <a class="sidebar-link <?= menuActivo('mg_defensas') ? 'active' : '' ?>" href="/controllers/mg_defensas_listar.php"><i class="bi bi-calendar2-check" aria-hidden="true"></i>Defensas</a>
          <a class="sidebar-link <?= menuActivo('mg_padron') ? 'active' : '' ?>" href="/controllers/mg_padron_importar.php"><i class="bi bi-upload" aria-hidden="true"></i>Importar Padrón</a>
          <a class="sidebar-link <?= menuActivo('mg_reportes') ? 'active' : '' ?>" href="/controllers/mg_reportes_cohorte.php"><i class="bi bi-file-earmark-bar-chart" aria-hidden="true"></i>Reportes</a>
          <a class="sidebar-link <?= menuActivo('mg_expedientes') ? 'active' : '' ?>" href="/controllers/mg_expedientes_listar.php"><i class="bi bi-folder2-open" aria-hidden="true"></i>Expedientes MG</a>
          <a class="sidebar-link <?= menuActivo('mg_parametros') ? 'active' : '' ?>" href="/controllers/mg_parametros_listar.php"><i class="bi bi-sliders" aria-hidden="true"></i>Parámetros MG</a>
          <a class="sidebar-link <?= menuActivo('mg_alertas') ? 'active' : '' ?>" href="/controllers/mg_alertas.php"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i>Alertas</a>
          <div class="sidebar-section mt-4">Auditoría</div>
          <a class="sidebar-link <?= menuActivo('historial_listar') ? 'active' : '' ?>" href="/controllers/historial_listar.php"><i class="bi bi-shield-lock" aria-hidden="true"></i>Historial y Auditoría</a>
        <?php elseif ($rolSesion === 'auxiliar'): ?>
          <div class="sidebar-section">Gestión académica</div>
          <a class="sidebar-link <?= menuActivo('usuarios') ? 'active' : '' ?>" href="/controllers/usuarios_listar.php"><i class="bi bi-people" aria-hidden="true"></i>Usuarios</a>
          <a class="sidebar-link <?= menuActivo('tutores') ? 'active' : '' ?>" href="/controllers/tutores_listar.php"><i class="bi bi-person-video3" aria-hidden="true"></i>Tutores</a>
          <a class="sidebar-link <?= menuActivo('solicitudes_tutor') ? 'active' : '' ?>" href="/controllers/solicitudes_tutor_listar.php"><i class="bi bi-send-check" aria-hidden="true"></i>Solicitudes de tutores</a>
          <div class="sidebar-section mt-4">Tutorías</div>
          <a class="sidebar-link <?= menuActivo('tutorias_listar') ? 'active' : '' ?>" href="/controllers/tutorias_listar.php"><i class="bi bi-calendar-check" aria-hidden="true"></i>Tutorías</a>
          <a class="sidebar-link <?= menuActivo('tutorias_asignar') ? 'active' : '' ?>" href="/controllers/tutorias_asignar.php"><i class="bi bi-person-plus" aria-hidden="true"></i>Asignar tutoría</a>
          <a class="sidebar-link <?= menuActivo('tribunales_asignar') ? 'active' : '' ?>" href="/controllers/tribunales_asignar.php"><i class="bi bi-people" aria-hidden="true"></i>Tribunales</a>
          <a class="sidebar-link <?= menuActivo('mg_comprobantes') ? 'active' : '' ?>" href="/controllers/mg_comprobantes_listar.php"><i class="bi bi-file-earmark-check" aria-hidden="true"></i>Comprobantes MG</a>
          <a class="sidebar-link <?= menuActivo('expediente_documentos') ? 'active' : '' ?>" href="/controllers/expediente_documentos.php"><i class="bi bi-folder2-open" aria-hidden="true"></i>Expedientes</a>
          <div class="sidebar-section mt-4">Modalidad de Grado</div>
          <a class="sidebar-link <?= menuActivo('mg_modalidades') ? 'active' : '' ?>" href="/controllers/mg_modalidades_listar.php"><i class="bi bi-diagram-3" aria-hidden="true"></i>Modalidades</a>
          <a class="sidebar-link <?= menuActivo('mg_cohortes') ? 'active' : '' ?>" href="/controllers/mg_cohortes_listar.php"><i class="bi bi-calendar-range" aria-hidden="true"></i>Cohortes</a>
          <a class="sidebar-link <?= menuActivo('mg_calendario') ? 'active' : '' ?>" href="/controllers/mg_calendario_listar.php"><i class="bi bi-calendar-event" aria-hidden="true"></i>Calendario MG</a>
          <a class="sidebar-link <?= menuActivo('mg_defensas') ? 'active' : '' ?>" href="/controllers/mg_defensas_listar.php"><i class="bi bi-calendar2-check" aria-hidden="true"></i>Defensas</a>
          <a class="sidebar-link <?= menuActivo('mg_padron') ? 'active' : '' ?>" href="/controllers/mg_padron_importar.php"><i class="bi bi-upload" aria-hidden="true"></i>Importar Padrón</a>
          <a class="sidebar-link <?= menuActivo('mg_reportes') ? 'active' : '' ?>" href="/controllers/mg_reportes_cohorte.php"><i class="bi bi-file-earmark-bar-chart" aria-hidden="true"></i>Reportes</a>
          <a class="sidebar-link <?= menuActivo('mg_expedientes') ? 'active' : '' ?>" href="/controllers/mg_expedientes_listar.php"><i class="bi bi-folder2-open" aria-hidden="true"></i>Expedientes MG</a>
          <div class="sidebar-section mt-4">Auditoría</div>
          <a class="sidebar-link <?= menuActivo('bitacora_listar') ? 'active' : '' ?>" href="/controllers/bitacora_listar.php"><i class="bi bi-shield-lock" aria-hidden="true"></i>Bitácora de usuarios</a>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <div class="sidebar-section">Mi espacio</div>
          <a class="sidebar-link <?= menuActivo('tutor/panel') ? 'active' : '' ?>" href="/views/tutor/panel.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i>Mi panel</a>
          <a class="sidebar-link <?= menuActivo('cartas_responder') ? 'active' : '' ?>" href="/controllers/cartas_responder.php"><i class="bi bi-envelope-paper" aria-hidden="true"></i>Cartas de designación</a>
          <a class="sidebar-link <?= menuActivo('reuniones_registrar') ? 'active' : '' ?>" href="/controllers/reuniones_registrar.php"><i class="bi bi-calendar2-week" aria-hidden="true"></i>Reuniones</a>
          <a class="sidebar-link <?= menuActivo('informes_registrar') ? 'active' : '' ?>" href="/controllers/informes_registrar.php"><i class="bi bi-clipboard2-check" aria-hidden="true"></i>Informes de avance</a>
          <a class="sidebar-link <?= menuActivo('disponibilidad') ? 'active' : '' ?>" href="/controllers/tutores_disponibilidad.php"><i class="bi bi-clock-history" aria-hidden="true"></i>Horarios y materias</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="app-main">
      <div class="mobile-menu-bar">
        <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Abrir menú"><i class="bi bi-list fs-3" aria-hidden="true"></i></button>
        <img class="brand-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
        <div class="d-flex align-items-center gap-1 text-white">
          <a href="/controllers/logout.php" onclick="cerrarSesion(event)" class="btn btn-link text-white p-0" aria-label="Cerrar sesión"><i class="bi bi-box-arrow-right fs-5" aria-hidden="true"></i></a>
        </div>
      </div>
      <header class="app-topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <button type="button" class="btn btn-link p-0 text-primary d-none d-lg-inline-flex sidebar-toggle-btn" id="btnSidebarToggle" aria-label="Colapsar o expandir menú lateral" title="Colapsar o expandir menú">
            <i class="bi bi-layout-sidebar-inset fs-4" aria-hidden="true"></i>
          </button>
          <div class="topbar-title"><?= htmlspecialchars($tituloSeccion) ?></div>
        </div>
        <div class="topbar-user">
          <div class="dropdown notif-dropdown me-3">
            <button class="btn btn-link position-relative p-1 notif-bell-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notificaciones" title="Notificaciones">
              <i class="bi bi-bell fs-5" aria-hidden="true"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill border border-light notif-count" style="display:none;">0</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 notif-menu" style="width: 360px; max-width: 90vw;">
              <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <strong class="small text-uppercase text-muted">Notificaciones <span class="notif-count-label text-primary"></span></strong>
                <a href="/controllers/notificaciones_listar.php" class="small text-decoration-none">Ver todas</a>
              </div>
              <div class="notif-items" style="max-height: 320px; overflow-y: auto;">
                <div class="text-center py-4 text-muted"><i class="bi bi-bell-slash d-block mb-1"></i>Sin notificaciones</div>
              </div>
            </div>
          </div>
          <?php if ($rolSesion === 'tutor'): ?>
            <a href="/controllers/tutor_perfil.php" class="topbar-user-perfil d-flex align-items-center gap-2 text-decoration-none" title="Ver y editar mi perfil" aria-label="Ver y editar mi perfil">
              <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
              <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
            </a>
          <?php else: ?>
            <?= avatar($nombreSesion, $apellidoSesion, $rolSesion) ?>
            <div><div class="topbar-user-name"><?= htmlspecialchars($nombreSesion) ?></div><div class="topbar-user-role"><?= htmlspecialchars($rolSesion) ?></div></div>
          <?php endif; ?>
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