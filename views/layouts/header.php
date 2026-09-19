<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rolSesion = $_SESSION['rol'] ?? '';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $tituloPagina ?? 'Sistema de Tutorías - UPDS' ?></title>
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background-color: #f7f9fc;
      color: #334155;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #001e3d 0%, #002b49 70%, #002855 100%);
      border-bottom: 3px solid #f5a623;
    }
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      background: #fff;
    }
    .badge-admin { background-color: #fff3d6; color: #8a5700; border: 1px solid #f5a623; }
    .badge-tutor { background-color: #e4f0f7; color: #002b49; border: 1px solid #b7d3e2; }
    .badge-estudiante { background-color: #e5f5ef; color: #176b4d; border: 1px solid #b8e4d0; }
    .monogram { background: linear-gradient(135deg, #f5a623, #e59819); color: #002b49; }
    .btn-primary { background-color: #002b49; border-color: #002b49; }
    .btn-primary:hover { background-color: #001e3d; border-color: #001e3d; }
  </style>
</head>
<body>

<?php if (isset($_SESSION['id_usuario'])): ?>
<!-- Navbar principal del sistema -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/controllers/usuarios_listar.php">
      <i class="bi bi-mortarboard-fill text-warning fs-4"></i>
      <span><strong>UPDS</strong><small class="d-block fw-normal" style="font-size: 0.62rem; letter-spacing: .08em;">SEDE TARIJA</small></span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($rolSesion === 'administrador'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/usuarios_listar.php">
              <i class="bi bi-people-fill"></i> Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'reportes') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/reportes.php">
              <i class="bi bi-bar-chart-line-fill"></i> Reportes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'materias') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/materias_listar.php">
              <i class="bi bi-journal-bookmark-fill"></i> Materias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'carreras') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/carreras_listar.php">
              <i class="bi bi-mortarboard"></i> Carreras
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'tutores') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/tutores_listar.php">
              <i class="bi bi-person-video3"></i> Tutores
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'estudiantes') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/estudiantes_listar.php">
              <i class="bi bi-mortarboard-fill"></i> Estudiantes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'tutorias') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/tutorias_listar.php">
              <i class="bi bi-calendar-check-fill"></i> Tutorías
            </a>
          </li>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'tutor/panel') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/views/tutor/panel.php">
              <i class="bi bi-speedometer2"></i> Mi Panel
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'disponibilidad') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/tutores_disponibilidad.php">
              <i class="bi bi-clock-history"></i> Mis Horarios y Materias
            </a>
          </li>
        <?php elseif ($rolSesion === 'estudiante'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'estudiante/panel') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/views/estudiante/panel.php">
              <i class="bi bi-calendar2-check"></i> Mis Tutorías
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'solicitar') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/tutorias_solicitar.php">
              <i class="bi bi-calendar-plus"></i> Solicitar Tutoría
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Perfil de usuario y botón salir -->
      <div class="d-flex align-items-center gap-3">
        <div class="monogram rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
          <?= strtoupper(substr($nombreSesion, 0, 1) . substr(strstr($nombreSesion, ' ') ?: '', 1, 1)) ?>
        </div>
        <div class="text-end text-white d-none d-md-block">
          <div class="fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($nombreSesion) ?></div>
          <span class="badge rounded-pill text-uppercase px-2" style="font-size: 0.65rem; background: rgba(255,255,255,0.2);">
            <?= htmlspecialchars($rolSesion) ?>
          </span>
        </div>
        <a href="/controllers/logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
          <i class="bi bi-box-arrow-right"></i>
          <span>Salir</span>
        </a>
      </div>
    </div>
  </div>
</nav>
<?php endif; ?>

<main class="container py-4 flex-grow-1">
